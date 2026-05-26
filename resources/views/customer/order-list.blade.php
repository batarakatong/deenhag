<div class="grid gap-4">
    @forelse($orders as $order)
        <a class="rounded bg-white p-5 shadow" href="{{ route('customer.orders.show', $order) }}">
            <div class="flex flex-wrap justify-between gap-3">
                <div>
                    <div class="font-bold">{{ $order->order_number }}</div>
                    <div class="text-sm text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                </div>
                <div>@include('partials.status', ['status' => $order->status]) @include('partials.status', ['status' => $order->payment_status])</div>
                <div class="font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
            </div>
        </a>
    @empty
        <div class="rounded bg-white p-6 shadow">Belum ada pesanan.</div>
    @endforelse
</div>
