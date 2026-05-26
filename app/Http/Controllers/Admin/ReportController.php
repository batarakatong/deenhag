<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $orders = Order::query()
            ->with('user')
            ->when($request->search, fn ($q) => $q->where(function ($query) use ($request) {
                $query->where('order_number', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%'));
            }))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->payment_status, fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return view('admin.reports.sales', compact('orders'));
    }

    public function stocks(Request $request)
    {
        $materials = Material::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->material_category_id, fn ($q) => $q->where('material_category_id', $request->material_category_id))
            ->when($request->stock_status === 'low', fn ($q) => $q->whereColumn('current_stock', '<=', 'minimum_stock'))
            ->get();

        $movements = StockMovement::with('material')
            ->when($request->movement_type, fn ($q) => $q->where('movement_type', $request->movement_type))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.reports.stocks', [
            'materials' => $materials,
            'movements' => $movements,
            'categories' => MaterialCategory::orderBy('name')->get(),
        ]);
    }
}
