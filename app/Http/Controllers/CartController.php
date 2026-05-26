<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('customer.cart', ['cart' => $this->activeCart()->load('items.product')]);
    }

    public function store(Request $request, Product $product, PricingService $pricing)
    {
        $data = $request->validate([
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'options' => ['nullable', 'array'],
            'width' => ['nullable', 'numeric', 'min:0.01'],
            'height' => ['nullable', 'numeric', 'min:0.01'],
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_note' => ['nullable', 'string'],
            'design_file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,ai,cdr,psd', 'max:51200'],
        ]);

        $price = $pricing->calculate($product, $data);
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('design_file')) {
            $file = $request->file('design_file');
            $filePath = $file->store('private/cart-designs');
            $fileName = $file->getClientOriginalName();
        }

        $this->activeCart()->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'specifications' => $price['specifications'],
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'quantity' => $data['quantity'],
            'estimated_price' => $price['total'],
            'customer_note' => $data['customer_note'] ?? null,
            'design_file_path' => $filePath,
            'design_file_name' => $fileName,
        ]);

        return redirect()->route('cart.index')->with('status', 'Produk masuk keranjang.');
    }

    public function destroy(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        $item->delete();

        return back()->with('status', 'Item dihapus.');
    }

    private function activeCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id(), 'status' => 'active']);
    }
}
