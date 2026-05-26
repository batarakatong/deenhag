@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow">
    <h1 class="mb-4 text-2xl font-bold">Lupa Password</h1>
    <p class="mb-4 text-sm text-slate-600">Masukkan email akun Anda. Sistem akan mengirim link reset password jika SMTP sudah dikonfigurasi.</p>
    <form method="post" action="{{ route('password.email') }}" class="grid gap-4">@csrf
        <input class="field" name="email" type="email" placeholder="Email" required>
        <button class="btn btn-primary">Kirim Link Reset</button>
    </form>
</div>
@endsection
