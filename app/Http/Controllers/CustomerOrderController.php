<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function dashboard()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->limit(6)->get();
        return view('customer.dashboard', compact('orders'));
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(12);
        return view('customer.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('customer.order-detail', ['order' => $order->load(['items', 'files', 'payments', 'invoice', 'shipment'])]);
    }

    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $data = $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'bank_name' => ['nullable', 'string', 'max:80'],
            'account_name' => ['nullable', 'string', 'max:120'],
        ]);

        $payment = $order->payments()->latest()->firstOrFail();
        $path = $request->file('proof_file')->store('private/payment-proofs/'.$order->order_number);
        $payment->update([
            'proof_file' => $path,
            'bank_name' => $data['bank_name'] ?? null,
            'account_name' => $data['account_name'] ?? null,
            'status' => 'waiting_confirmation',
            'paid_at' => now(),
        ]);
        $order->update(['payment_status' => 'waiting_confirmation']);

        return back()->with('status', 'Bukti pembayaran terkirim dan menunggu konfirmasi admin.');
    }

    public function uploadRevision(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $data = $request->validate([
            'design_file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg,webp,ai,cdr,psd', 'max:51200'],
            'notes' => ['nullable', 'string'],
        ]);
        $file = $request->file('design_file');
        $path = $file->store('private/order-files/'.$order->order_number);
        $order->files()->create([
            'uploaded_by' => auth()->id(),
            'file_type' => 'revision',
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', 'File revisi berhasil diupload.');
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(15);
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return view('customer.notifications', compact('notifications'));
    }
}
