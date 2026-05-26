@extends('layouts.admin')
@section('content')
<form method="post" action="{{ route('admin.customers.store') }}" class="mx-auto grid max-w-xl gap-4 rounded-xl bg-white p-6 shadow">@csrf
    <h1 class="text-2xl font-bold">Tambah Customer</h1>
    <input class="field" name="name" placeholder="Nama" required>
    <input class="field" name="email" type="email" placeholder="Email" required>
    <input class="field" name="phone" placeholder="No HP / WA">
    <input class="field" name="password" type="password" placeholder="Password" required>
    <input class="field" name="password_confirmation" type="password" placeholder="Konfirmasi Password" required>
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection
