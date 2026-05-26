@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md rounded bg-white p-6 shadow">
    <h1 class="mb-5 text-2xl font-bold">Login</h1>
    <form method="post" action="{{ route('login.store') }}" class="grid gap-4">@csrf
        <input class="rounded border p-3" name="email" type="email" placeholder="Email" value="{{ old('email') }}" required>
        <input class="rounded border p-3" name="password" type="password" placeholder="Password" required>
        <button class="rounded bg-green-600 px-4 py-3 font-semibold text-white">Login</button>
    </form>
    <div class="mt-4 flex justify-between text-sm">
        <a class="text-green-700" href="{{ route('register') }}">Daftar customer</a>
        <a class="text-green-700" href="{{ route('password.request') }}">Lupa password?</a>
    </div>
</div>
@endsection
