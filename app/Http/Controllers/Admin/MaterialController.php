<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.materials.index', [
            'materials' => Material::with(['category', 'supplier'])
                ->when($request->search, fn ($query) => $query->where('name', 'like', '%'.$request->search.'%'))
                ->when($request->material_category_id, fn ($query) => $query->where('material_category_id', $request->material_category_id))
                ->when($request->supplier_id, fn ($query) => $query->where('supplier_id', $request->supplier_id))
                ->when($request->stock_status === 'low', fn ($query) => $query->whereColumn('current_stock', '<=', 'minimum_stock'))
                ->paginate(15)
                ->withQueryString(),
            'categories' => MaterialCategory::all(),
            'suppliers' => Supplier::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_category_id' => ['required', 'exists:material_categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'max:160'],
            'unit' => ['required', 'max:30'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);
        Material::create($data);
        return back()->with('status', 'Bahan dibuat.');
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);
        $material->update($data);
        return back()->with('status', 'Bahan diperbarui.');
    }

    public function adjust(Request $request, Material $material, StockService $stock)
    {
        $data = $request->validate(['quantity' => ['required', 'numeric', 'min:0'], 'note' => ['nullable', 'string']]);
        $stock->move($material, 'adjustment', (float) $data['quantity'], null, $data['note'] ?? 'Koreksi stok manual');
        return back()->with('status', 'Stok dikoreksi.');
    }
}
