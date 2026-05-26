@extends('layouts.admin')
@section('content')
<h1 class="mb-5 text-2xl font-bold">Notifikasi</h1>
<div class="grid gap-3">
    @forelse($notifications as $notification)
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="flex justify-between gap-3">
                <div>
                    <div class="font-bold">{{ $notification->title }}</div>
                    <div class="text-sm text-slate-600">{{ $notification->message }}</div>
                </div>
                <div class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
        </div>
    @empty
        <div class="rounded-xl bg-white p-6 shadow">Belum ada notifikasi.</div>
    @endforelse
</div>
<div class="mt-5">{{ $notifications->links() }}</div>
@endsection
