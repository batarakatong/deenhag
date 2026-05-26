<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        return view('store.home', [
            'categories' => Category::where('is_active', true)->withCount('products')->get(),
            'products' => Product::where('is_active', true)->with('category')->latest()->limit(6)->get(),
        ]);
    }

    public function products(Request $request)
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with('category')
            ->when($request->category, fn ($query) => $query->whereHas('category', fn ($q) => $q->where('slug', $request->category)))
            ->when($request->search, fn ($query) => $query->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(12);

        return view('store.products', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('store.product-detail', [
            'product' => $product->load(['category', 'variants', 'options']),
        ]);
    }

    public function calculate(Request $request, Product $product, PricingService $pricing)
    {
        return response()->json($pricing->calculate($product, $request->all()));
    }

    public function track(Request $request)
    {
        $order = null;
        if ($request->filled('order_number')) {
            $order = Order::where('order_number', $request->order_number)->with(['invoice', 'files'])->first();
        }

        return view('store.track', compact('order'));
    }
}
