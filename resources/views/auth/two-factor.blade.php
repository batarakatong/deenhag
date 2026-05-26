@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md rounded-xl bg-white p-6 shadow">
    <h1 class="mb-4 text-2xl font-bold">Verifikasi 2FA</h1>
    <p class="mb-4 text-sm text-slate-600">Masukkan kode 6 digit yang dikirim ke email akun Anda.</p>
    <form method="post" action="{{ route('2fa.verify') }}" class="grid gap-4">@csrf
        <input class="field" name="code" inputmode="numeric" maxlength="6" placeholder="Kode 2FA" required>
        <button class="btn btn-primary">Verifikasi</button>
    </form>
</div>
@endsection
