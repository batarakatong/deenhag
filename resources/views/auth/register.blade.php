@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md rounded bg-white p-6 shadow">
    <h1 class="mb-5 text-2xl font-bold">Daftar Customer</h1>
    <form method="post" action="{{ route('register.store') }}" class="grid gap-4">@csrf
        <input class="rounded border p-3" name="name" placeholder="Nama" required>
        <input class="rounded border p-3" name="email" type="email" placeholder="Email" required>
        <input class="rounded border p-3" name="phone" placeholder="Nomor HP">
        <input class="rounded border p-3" name="password" type="password" placeholder="Password" required>
        <input class="rounded border p-3" name="password_confirmation" type="password" placeholder="Konfirmasi password" required>
        <button class="rounded bg-green-600 px-4 py-3 font-semibold text-white">Daftar</button>
    </form>
</div>
@endsection
