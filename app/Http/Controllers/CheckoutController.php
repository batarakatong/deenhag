<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CheckoutService;
use App\Services\WahaService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id(), 'status' => 'active'])->load('items.product');
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang masih kosong.');
        }

        return view('customer.checkout', compact('cart'));
    }

    public function store(Request $request, CheckoutService $checkout, WahaService $waha)
    {
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'fulfillment_method' => ['required', 'in:pickup,delivery'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'customer_note' => ['nullable', 'string'],
        ]);

        $cart = Cart::where('user_id', auth()->id())->where('status', 'active')->firstOrFail();
        $order = $checkout->checkout($cart, $data);
        if (\App\Models\Setting::valueOf('waha_notify_customer_order', '0') === '1' && ($data['phone'] ?? null)) {
            $waha->sendText($data['phone'], strtr(\App\Models\Setting::valueOf('waha_template_order', 'Halo {name}, order {order_number} sudah dibuat dengan total {total}.'), [
                '{name}' => $data['recipient_name'],
                '{order_number}' => $order->order_number,
                '{total}' => 'Rp '.number_format($order->grand_total, 0, ',', '.'),
            ]));
        }
        if (\App\Models\Setting::valueOf('waha_notify_admin_order', '0') === '1' && \App\Models\Setting::valueOf('waha_admin_number')) {
            $waha->sendText(\App\Models\Setting::valueOf('waha_admin_number'), 'Order baru '.$order->order_number.' dari '.$data['recipient_name'].' total Rp '.number_format($order->grand_total, 0, ',', '.'));
        }

        return redirect()->route('customer.orders.show', $order)->with('status', 'Pesanan dibuat. Silakan lakukan pembayaran.');
    }
}
