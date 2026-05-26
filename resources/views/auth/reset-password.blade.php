@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow">
    <h1 class="mb-4 text-2xl font-bold">Reset Password</h1>
    <form method="post" action="{{ route('password.update') }}" class="grid gap-4">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input class="field" name="email" type="email" value="{{ $email }}" placeholder="Email" required>
        <input class="field" name="password" type="password" placeholder="Password baru" required>
        <input class="field" name="password_confirmation" type="password" placeholder="Konfirmasi password" required>
        <button class="btn btn-primary">Reset Password</button>
    </form>
</div>
@endsection
