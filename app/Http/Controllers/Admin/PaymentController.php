<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\WahaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.payments.index', [
            'payments' => Payment::with('order.user')
                ->when($request->search, fn ($query) => $query->where(function ($q) use ($request) {
                    $q->where('payment_number', 'like', '%'.$request->search.'%')
                        ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', '%'.$request->search.'%'));
                }))
                ->when($request->status, fn ($query) => $query->where('status', $request->status))
                ->when($request->method, fn ($query) => $query->where('method', $request->method))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function confirm(Payment $payment, WahaService $waha)
    {
        $payment->update([
            'status' => 'paid',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        $payment->order->update(['payment_status' => 'paid', 'status' => 'payment_confirmed']);
        $payment->order->invoice?->update(['status' => 'paid']);
        if (Setting::valueOf('waha_notify_payment', '0') === '1' && $payment->order->user?->phone) {
            $waha->sendText($payment->order->user->phone, strtr(Setting::valueOf('waha_template_payment', 'Pembayaran order {order_number} berstatus {payment_status}.'), [
                '{order_number}' => $payment->order->order_number,
                '{payment_status}' => 'dibayar',
            ]));
        }

        return back()->with('status', 'Pembayaran dikonfirmasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate(['rejection_reason' => ['required', 'string']]);
        $payment->update($data + ['status' => 'rejected']);
        $payment->order->update(['payment_status' => 'rejected', 'status' => 'pending_payment']);

        return back()->with('status', 'Pembayaran ditolak.');
    }
}
