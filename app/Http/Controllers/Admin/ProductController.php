<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($query) => $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('product_code', 'like', '%'.$request->search.'%')
                    ->orWhere('default_material', 'like', '%'.$request->search.'%');
            }))
            ->when($request->category_id, fn ($query) => $query->where('category_id', $request->category_id))
            ->when($request->service_type, fn ($query) => $query->where('service_type', $request->service_type))
            ->when($request->pricing_type, fn ($query) => $query->where('pricing_type', $request->pricing_type))
            ->when($request->filled('is_active'), fn ($query) => $query->where('is_active', $request->boolean('is_active')))
            ->latest();

        return view('admin.products.index', [
            'products' => $products->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::all(),
            'materials' => Material::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validated($request));
        $this->syncOptions($product, $request);

        return redirect()->route('admin.products.index')->with('status', 'Produk dibuat.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product->load(['options', 'materialUsages']),
            'categories' => Category::all(),
            'materials' => Material::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));
        $this->syncOptions($product, $request);

        return redirect()->route('admin.products.index')->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        abort_if(auth()->user()->hasRole('staff'), 403);
        $product->delete();

        return back()->with('status', 'Produk dihapus.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'product_code' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'file_guidelines' => ['nullable', 'string'],
            'technical_specs' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sample_images.*' => ['nullable', 'image', 'max:4096'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'pricing_type' => ['required', 'in:pcs,meter,square_meter,package,rim,manual'],
            'service_type' => ['required', 'in:printing,sablon,design,finishing,merchandise'],
            'print_method' => ['nullable', 'string', 'max:120'],
            'default_material' => ['nullable', 'string', 'max:160'],
            'waste_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'estimated_days' => ['required', 'integer', 'min:1'],
            'min_order_qty' => ['required', 'integer', 'min:1'],
            'is_custom_size' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = $product?->slug ?: Str::slug($data['name']).'-'.substr(md5($data['name'].microtime()), 0, 6);
        $data['is_custom_size'] = $request->boolean('is_custom_size');
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('public/product-images');
        } elseif ($product?->image) {
            $data['image'] = $product->image;
        }
        if ($request->hasFile('sample_images')) {
            $data['sample_images'] = collect($request->file('sample_images'))
                ->map(fn ($file) => $file->store('public/product-samples'))
                ->values()
                ->all();
        } elseif ($product?->sample_images) {
            $data['sample_images'] = $product->sample_images;
        }

        return $data;
    }

    private function syncOptions(Product $product, Request $request): void
    {
        if (! $request->filled('option_name')) {
            return;
        }
        $product->options()->delete();
        foreach ($request->option_name as $index => $name) {
            if (! $name) {
                continue;
            }
            $product->options()->create([
                'option_type' => $request->option_type[$index] ?? 'finishing',
                'name' => $name,
                'price_modifier' => $request->option_price[$index] ?? 0,
                'calculation_type' => $request->option_calc[$index] ?? 'fixed',
                'is_active' => true,
            ]);
        }

        if ($request->has('material_id')) {
            $product->materialUsages()->delete();
            foreach ($request->material_id as $index => $materialId) {
                if (! $materialId) {
                    continue;
                }
                $product->materialUsages()->create([
                    'material_id' => $materialId,
                    'usage_per_unit' => $request->usage_per_unit[$index] ?? 1,
                    'usage_type' => $request->usage_type[$index] ?? 'per_item',
                    'is_primary' => (int) ($request->primary_material ?? -1) === $index,
                ]);
            }
        }
    }
}
