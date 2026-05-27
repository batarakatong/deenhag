@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="mb-5 text-3xl font-bold">Akun Saya</h1>
    <form method="post" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="grid gap-5 rounded-xl bg-white p-6 shadow">@csrf @method('patch')
        <section>
            <h2 class="mb-3 text-lg font-bold">Data Kontak</h2>
            <div class="mb-4 flex items-center gap-4">
                @if(auth()->user()->avatar_path)
                    <img class="h-20 w-20 rounded-full object-cover" src="{{ $mediaUrl(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" decoding="async">
                @else
                    <div class="grid h-20 w-20 place-items-center rounded-full bg-green-100 text-2xl font-bold text-green-700">{{ substr(auth()->user()->name,0,1) }}</div>
                @endif
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Foto Profil</span>
                    <input class="field" name="avatar" type="file" accept="image/*">
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Nama</span>
                    <input class="field" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Nomor HP / WhatsApp</span>
                    <input class="field" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="081234567890" required>
                    <span class="text-xs text-slate-500">Nomor ini dipakai untuk notifikasi status pesanan via WhatsApp.</span>
                </label>
                <label class="grid gap-2 md:col-span-2">
                    <span class="text-sm font-semibold">Nama Perusahaan / Instansi</span>
                    <input class="field" name="company_name" value="{{ old('company_name', $customer->company_name) }}">
                </label>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-bold">Alamat</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-2 md:col-span-2">
                    <span class="text-sm font-semibold">Alamat Lengkap</span>
                    <textarea class="field" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Kota</span>
                    <input class="field" name="city" value="{{ old('city', $customer->city) }}">
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Provinsi</span>
                    <input class="field" name="province" value="{{ old('province', $customer->province) }}">
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Kode Pos</span>
                    <input class="field" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}">
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-semibold">Kode Customer</span>
                    <input class="field bg-slate-50" value="{{ $customer->customer_code }}" disabled>
                </label>
            </div>
        </section>

        <button class="btn btn-primary">Simpan Profil</button>
    </form>
</div>
@endsection
