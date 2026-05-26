<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function checkout(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            $cart->load('items.product');
            $user = $cart->user;
            $customer = Customer::firstOrCreate(
                ['user_id' => $user->id],
                ['customer_code' => 'CUST-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)]
            );

            $subtotal = $cart->items->sum('estimated_price');
            $shipping = ($data['fulfillment_method'] ?? 'pickup') === 'delivery' ? (float) ($data['shipping_cost'] ?? 0) : 0;
            $grandTotal = $subtotal + $shipping;

            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('Ymd-His').'-'.$user->id,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'order_date' => now(),
                'status' => 'pending_payment',
                'payment_status' => 'unpaid',
                'fulfillment_method' => $data['fulfillment_method'] ?? 'pickup',
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'grand_total' => $grandTotal,
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $orderItem = $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'specifications' => $item->specifications,
                    'width' => $item->width,
                    'height' => $item->height,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->estimated_price,
                    'total_price' => $item->estimated_price,
                    'production_status' => 'waiting',
                ]);

                if ($item->design_file_path) {
                    $order->files()->create([
                        'order_item_id' => $orderItem->id,
                        'uploaded_by' => $user->id,
                        'file_type' => 'customer_design',
                        'original_name' => $item->design_file_name,
                        'file_path' => $item->design_file_path,
                    ]);
                }
            }

            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-'.now()->format('Ymd-His').'-'.$order->id,
                'invoice_date' => today(),
                'due_date' => today()->addDays(3),
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'grand_total' => $grandTotal,
                'status' => 'unpaid',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_number' => 'PAY-'.now()->format('Ymd-His').'-'.$order->id,
                'amount' => $grandTotal,
                'status' => 'unpaid',
            ]);

            $order->shipment()->create([
                'method' => $order->fulfillment_method,
                'recipient_name' => $data['recipient_name'] ?? $user->name,
                'phone' => $data['phone'] ?? $user->phone,
                'address' => $data['address'] ?? $customer->address,
                'shipping_cost' => $shipping,
                'status' => 'pending',
            ]);

            $cart->update(['status' => 'checked_out']);

            return $order->fresh(['items', 'invoice', 'payments']);
        });
    }
}
