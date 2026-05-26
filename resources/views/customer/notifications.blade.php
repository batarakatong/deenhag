@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Notifikasi Saya</h1>
<div class="grid gap-3">
    @forelse($notifications as $notification)
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="font-bold">{{ $notification->title }}</div>
            <div class="mt-1 text-sm text-slate-600">{{ $notification->message }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</div>
        </div>
    @empty
        <div class="rounded-xl bg-white p-6 shadow">Belum ada notifikasi.</div>
    @endforelse
</div>
<div class="mt-5">{{ $notifications->links() }}</div>
@endsection
