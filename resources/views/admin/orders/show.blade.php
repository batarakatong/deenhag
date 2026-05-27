@extends('layouts.admin')
@section('content')
<div class="mb-5 flex flex-wrap justify-between gap-3">
    <div><h1 class="text-2xl font-bold">{{ $order->order_number }}</h1><p>{{ $order->user->name }} - Rp {{ number_format($order->grand_total,0,',','.') }}</p></div>
    <div class="flex flex-wrap gap-2">
        <a class="btn btn-secondary" href="{{ route('admin.orders.service-order',$order) }}" target="_blank">Cetak SO</a>
        <a class="btn btn-primary" href="{{ route('admin.orders.invoice-pdf',$order) }}">Download Invoice PDF</a>
    </div>
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <section class="grid gap-5 lg:col-span-2">
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">Item</h2>
            @foreach($order->items as $item)
                <div class="border-b py-3 last:border-0"><b>{{ $item->product_name }}</b><div class="text-sm">@include('partials.specifications', ['specifications' => $item->specifications])</div><div>Qty {{ $item->quantity }} - Rp {{ number_format($item->total_price,0,',','.') }}</div></div>
            @endforeach
        </div>
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">File Desain</h2>
            <form method="post" action="{{ route('admin.orders.revision-file', $order) }}" enctype="multipart/form-data" class="mb-4 grid gap-3 rounded-lg border bg-slate-50 p-3">@csrf
                <div class="font-semibold">Upload Preview/Revisi/Final</div>
                <select class="field" name="file_type">
                    <option value="admin_preview">Preview Admin</option>
                    <option value="revision">Revisi</option>
                    <option value="final_file">File Final</option>
                </select>
                <input class="field" name="design_file" type="file" required>
                <textarea class="field" name="notes" placeholder="Catatan file"></textarea>
                <button class="btn btn-secondary">Upload File</button>
            </form>
            <div class="grid gap-3 sm:grid-cols-2">
            @forelse($order->files as $file)
                @php($isImage = str($file->original_name)->lower()->endsWith(['.jpg', '.jpeg', '.png', '.webp']))
                <a class="rounded-lg border p-3" href="{{ route('files.order-file', $file) }}" target="_blank">
                    @if($isImage)
                        <img class="mb-2 h-32 w-full rounded object-cover" src="{{ route('files.order-file', $file) }}" alt="{{ $file->original_name }}" loading="lazy" decoding="async">
                    @endif
                    <div class="font-semibold text-green-700">{{ $file->original_name }}</div>
                    <div class="text-sm text-slate-500">{{ $file->file_type }}</div>
                </a>
            @empty
                <div class="text-slate-500">Belum ada file.</div>
            @endforelse
            </div>
        </div>
    </section>
    <aside class="grid gap-5">
        <form method="post" action="{{ route('admin.orders.status',$order) }}" class="rounded bg-white p-5 shadow">@csrf @method('patch')
            <h2 class="mb-3 font-bold">Update Status</h2>
            <select class="mb-3 w-full rounded border p-3" name="status">
                @foreach(['pending_payment','payment_confirmed','waiting_design','file_received','design_process','waiting_approval','printing','finishing','ready_pickup','shipped','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected($order->status===$status)>{{ $status }}</option>
                @endforeach
            </select>
            <textarea class="mb-3 w-full rounded border p-3" name="internal_note" placeholder="Catatan internal">{{ $order->internal_note }}</textarea>
            <button class="w-full rounded bg-green-600 px-5 py-3 text-white">Simpan</button>
        </form>
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">Pembayaran</h2>
            @foreach($order->payments as $payment)
                <div class="mb-3">@include('partials.status', ['status' => $payment->status]) Rp {{ number_format($payment->amount,0,',','.') }}</div>
                <form method="post" action="{{ route('admin.payments.confirm',$payment) }}" class="mb-2">@csrf<button class="rounded bg-green-600 px-4 py-2 text-white">Konfirmasi</button></form>
                <form method="post" action="{{ route('admin.payments.reject',$payment) }}" class="grid gap-2">@csrf<input class="rounded border p-2" name="rejection_reason" placeholder="Alasan tolak"><button class="rounded bg-red-600 px-4 py-2 text-white">Tolak</button></form>
            @endforeach
        </div>
    </aside>
</div>
@endsection
