@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-2xl rounded bg-white p-6 shadow">
    <h1 class="mb-4 text-2xl font-bold">Cek Status Pesanan</h1>
    <form class="flex gap-3">
        <input class="flex-1 rounded border p-3" name="order_number" placeholder="Nomor order" value="{{ request('order_number') }}">
        <button class="rounded bg-green-600 px-5 py-3 text-white">Cek</button>
    </form>
    @if($order)
        <div class="mt-6 rounded border p-4">
            <div class="font-bold">{{ $order->order_number }}</div>
            <div class="mt-2">@include('partials.status', ['status' => $order->status])</div>
            <div class="mt-2 text-sm">Total: Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
            <div class="mt-4">
                <h2 class="font-semibold">File desain</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @forelse($order->files as $file)
                        @php($isImage = str($file->original_name)->lower()->endsWith(['.jpg', '.jpeg', '.png', '.webp']))
                        <a class="rounded-lg border p-3" href="{{ route('track.files.show', ['file' => $file, 'order_number' => $order->order_number]) }}" target="_blank">
                            @if($isImage)
                                <img class="mb-2 h-32 w-full rounded object-cover" src="{{ route('track.files.show', ['file' => $file, 'order_number' => $order->order_number]) }}" alt="{{ $file->original_name }}">
                            @endif
                            <div class="text-sm font-semibold text-green-700">{{ $file->original_name }}</div>
                            <div class="text-xs text-slate-500">{{ $file->file_type }}</div>
                        </a>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada file desain yang terhubung ke order ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
