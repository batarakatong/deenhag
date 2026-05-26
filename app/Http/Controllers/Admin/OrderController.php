<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Setting;
use App\Services\StockService;
use App\Services\WahaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->search, fn ($query) => $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->search.'%')
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%'));
            }))
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($request->payment_status, fn ($query) => $query->where('payment_status', $request->payment_status))
            ->when($request->fulfillment_method, fn ($query) => $query->where('fulfillment_method', $request->fulfillment_method))
            ->when($request->from, fn ($query) => $query->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($query) => $query->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['user', 'items', 'files', 'payments', 'invoice', 'shipment'])]);
    }

    public function updateStatus(Request $request, Order $order, StockService $stock, WahaService $waha)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
            'internal_note' => ['nullable', 'string'],
        ]);
        $old = $order->status;
        $order->update($data);

        if ($old !== 'printing' && $data['status'] === 'printing') {
            $stock->reduceForOrder($order->load('items'));
        }

        if ($old !== $data['status']) {
            $label = $this->statusLabel($data['status']);
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Status pesanan berubah',
                'message' => 'Pesanan '.$order->order_number.' sekarang: '.$label,
                'type' => 'order',
                'data' => ['order_id' => $order->id, 'status' => $data['status']],
            ]);

            if (Setting::valueOf('waha_notify_customer_order', '0') === '1' && $order->user?->phone) {
                $waha->sendText($order->user->phone, 'Halo '.$order->user->name.', status pesanan '.$order->order_number.' sekarang: '.$label.'.');
            }
        }

        return back()->with('status', 'Status pesanan diperbarui.');
    }

    public function invoicePdf(Order $order)
    {
        $order->load(['user', 'items', 'invoice', 'shipment']);
        return Pdf::loadView('pdf.invoice', compact('order'))->download($order->invoice->invoice_number.'.pdf');
    }

    public function serviceOrderPrint(Order $order)
    {
        $order->load(['user', 'customer', 'items.product', 'files', 'invoice', 'shipment']);

        return view('admin.orders.service-order', compact('order'));
    }

    public function uploadRevision(Request $request, Order $order)
    {
        $data = $request->validate([
            'design_file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg,webp,ai,cdr,psd', 'max:51200'],
            'file_type' => ['required', 'in:admin_preview,revision,final_file'],
            'notes' => ['nullable', 'string'],
        ]);
        $file = $request->file('design_file');
        $path = $file->store('private/order-files/'.$order->order_number);
        $order->files()->create([
            'uploaded_by' => auth()->id(),
            'file_type' => $data['file_type'],
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('status', 'File admin/revisi berhasil diupload.');
    }

    private function statusLabel(string $status): string
    {
        return [
            'pending_payment' => 'Menunggu pembayaran',
            'payment_confirmed' => 'Pembayaran dikonfirmasi',
            'waiting_design' => 'Menunggu desain',
            'file_received' => 'File diterima',
            'design_process' => 'Proses desain',
            'waiting_approval' => 'Menunggu approval',
            'printing' => 'Proses cetak',
            'sablon_process' => 'Proses sablon',
            'finishing' => 'Finishing',
            'ready_pickup' => 'Siap diambil',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ][$status] ?? $status;
    }
}
