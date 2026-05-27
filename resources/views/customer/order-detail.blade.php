@extends('layouts.app')
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-bold">{{ $order->order_number }}</h1>
        <div class="mt-2">@include('partials.status', ['status' => $order->status]) @include('partials.status', ['status' => $order->payment_status])</div>
    </div>
    <div class="text-2xl font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <section class="grid gap-4 lg:col-span-2">
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">Item Pesanan</h2>
            @foreach($order->items as $item)
                <div class="border-b py-3 last:border-0">
                    <div class="font-semibold">{{ $item->product_name }}</div>
                    <div class="text-sm text-slate-600">@include('partials.specifications', ['specifications' => $item->specifications])</div>
                    <div class="text-sm">Qty {{ $item->quantity }} - Rp {{ number_format($item->total_price, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">Status Produksi</h2>
            <div class="grid gap-2 text-sm">
                @foreach(['pending_payment','payment_confirmed','file_received','printing','finishing','ready_pickup','completed'] as $status)
                    <div class="{{ $order->status === $status ? 'font-bold text-green-700' : 'text-slate-500' }}">- {{ $status }}</div>
                @endforeach
            </div>
        </div>
        <div class="rounded bg-white p-5 shadow">
            <h2 class="mb-3 font-bold">File Desain</h2>
            <form method="post" action="{{ route('customer.orders.revision-file', $order) }}" enctype="multipart/form-data" class="mb-4 grid gap-3 rounded-lg border bg-slate-50 p-3">@csrf
                <div class="font-semibold">Upload Revisi Desain</div>
                <input class="field" name="design_file" type="file" required>
                <textarea class="field" name="notes" placeholder="Catatan revisi"></textarea>
                <button class="btn btn-secondary">Upload Revisi</button>
            </form>
            <div class="grid gap-3 sm:grid-cols-2">
                @forelse($order->files as $file)
                    @php($isImage = str($file->original_name)->lower()->endsWith(['.jpg', '.jpeg', '.png', '.webp']))
                    <a class="rounded-lg border p-3" href="{{ route('files.order-file', $file) }}" target="_blank">
                        @if($isImage)
                            <img class="mb-2 h-32 w-full rounded object-cover" src="{{ route('files.order-file', $file) }}" alt="{{ $file->original_name }}" loading="lazy" decoding="async">
                        @endif
                        <div class="text-sm font-semibold text-green-700">{{ $file->original_name }}</div>
                        <div class="text-xs text-slate-500">{{ $file->file_type }}</div>
                    </a>
                @empty
                    <div class="text-sm text-slate-500">Belum ada file desain.</div>
                @endforelse
            </div>
        </div>
    </section>
    <aside class="rounded bg-white p-5 shadow">
        <h2 class="mb-3 font-bold">Pembayaran Manual</h2>
        <p class="mb-3 text-sm text-slate-600">Transfer ke BCA 1234567890 a.n GreenPrinting, lalu upload bukti pembayaran.</p>
        <form method="post" action="{{ route('customer.payments.proof', $order) }}" enctype="multipart/form-data" class="grid gap-3">@csrf
            <input class="rounded border p-3" name="bank_name" placeholder="Bank asal">
            <input class="rounded border p-3" name="account_name" placeholder="Nama pemilik rekening">
            <input class="rounded border p-3" name="proof_file" type="file" required>
            <button class="rounded bg-green-600 px-5 py-3 text-white">Upload Bukti</button>
        </form>
    </aside>
</div>
@endsection
