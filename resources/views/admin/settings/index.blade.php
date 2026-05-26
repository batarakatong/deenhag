@extends('layouts.admin')
@section('content')
<h1 class="mb-5 text-2xl font-bold">Setting Sistem</h1>
<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="grid gap-6">@csrf
    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">Profil Perusahaan & Invoice</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <input class="rounded border p-3" name="company_name" value="{{ $settings->get('company_name', 'GreenPrinting') }}" placeholder="Nama perusahaan">
            <input class="rounded border p-3" name="company_logo" type="file" accept="image/*">
            <input class="rounded border p-3" name="company_phone" value="{{ $settings->get('company_phone') }}" placeholder="Telepon">
            <input class="rounded border p-3" name="company_email" value="{{ $settings->get('company_email') }}" placeholder="Email">
            <input class="rounded border p-3" name="company_website" value="{{ $settings->get('company_website') }}" placeholder="Website">
            <input class="rounded border p-3" name="company_address" value="{{ $settings->get('company_address') }}" placeholder="Alamat">
            <textarea class="rounded border p-3 md:col-span-2" name="company_profile" placeholder="Profil singkat perusahaan">{{ $settings->get('company_profile') }}</textarea>
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">Printer Struk & Printer Cetak Besar</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <input class="rounded border p-3" name="receipt_printer_name" value="{{ $settings->get('receipt_printer_name') }}" placeholder="Nama printer struk kecil">
            <input class="rounded border p-3" name="receipt_printer_paper" value="{{ $settings->get('receipt_printer_paper', '58mm / 80mm') }}" placeholder="Ukuran kertas struk">
            <input class="rounded border p-3" name="production_printer_name" value="{{ $settings->get('production_printer_name') }}" placeholder="Nama printer cetak besar">
            <input class="rounded border p-3" name="production_printer_paper" value="{{ $settings->get('production_printer_paper', 'A3 / A2 / Roll') }}" placeholder="Media printer besar">
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">SMTP Email & Reset Password</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <input class="field" name="smtp_host" value="{{ $settings->get('smtp_host') }}" placeholder="SMTP host">
            <input class="field" name="smtp_port" value="{{ $settings->get('smtp_port', 587) }}" placeholder="SMTP port">
            <input class="field" name="smtp_username" value="{{ $settings->get('smtp_username') }}" placeholder="SMTP username">
            <input class="field" name="smtp_password" type="password" value="{{ $settings->get('smtp_password') }}" placeholder="SMTP password">
            <select class="field" name="smtp_encryption">
                @foreach(['tls'=>'TLS','ssl'=>'SSL','none'=>'None'] as $key => $label)
                    <option value="{{ $key }}" @selected($settings->get('smtp_encryption','tls')===$key)>{{ $label }}</option>
                @endforeach
            </select>
            <input class="field" name="smtp_from_address" value="{{ $settings->get('smtp_from_address') }}" placeholder="From email">
            <input class="field" name="smtp_from_name" value="{{ $settings->get('smtp_from_name', $settings->get('company_name','GreenPrinting')) }}" placeholder="From name">
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">Tema, Header, Footer</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <select class="rounded border p-3" name="theme_name">
                @foreach(['professional'=>'Professional', 'green'=>'Green', 'dark'=>'Dark', 'compact'=>'Compact'] as $key => $label)
                    <option value="{{ $key }}" @selected($settings->get('theme_name', 'professional')===$key)>{{ $label }}</option>
                @endforeach
            </select>
            <input class="rounded border p-3" name="header_text" value="{{ $settings->get('header_text') }}" placeholder="Teks header">
            <textarea class="rounded border p-3 md:col-span-2" name="footer_text" placeholder="Teks footer">{{ $settings->get('footer_text') }}</textarea>
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">Permission User</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b text-left"><th class="py-2">Role</th><th>Produk</th><th>Pesanan</th><th>Pembayaran</th><th>Stok</th><th>Laporan</th><th>Setting</th></tr></thead>
                <tbody>
                    @foreach($roles as $role)
                        @php($stored = json_decode($settings->get('role_permissions', '{}'), true) ?: [])
                        <tr class="border-b">
                            <td class="py-2 font-semibold">{{ $role->display_name }}</td>
                            @foreach(['products','orders','payments','stocks','reports','settings'] as $permission)
                                <td><input type="checkbox" name="permissions[{{ $role->name }}][]" value="{{ $permission }}" @checked(in_array($permission, $stored[$role->name] ?? ($role->name === 'admin' ? ['products','orders','payments','stocks','reports','settings'] : [])))></td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">Notifikasi WhatsApp WAHA</h2>
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-900">
            <div class="font-bold">Panduan isi WAHA</div>
            <div>Base URL di aplikasi: isi dengan URL API WAHA, contoh <code>https://waha-sveondnwqart.axpa.sumopod.my.id</code>.</div>
            <div>API Key: isi key dari tombol/ikon kunci WAHA Cloud. Tanpa ini WAHA akan membalas <code>401 Unauthorized</code>.</div>
            <div>Session: isi nama session WAHA, contoh <code>no_ku</code>.</div>
            <div>Webhook URL di dashboard WAHA: isi dengan <code>{{ url('/webhooks/waha') }}</code>.</div>
            <div class="mt-2 text-green-800">Jika WAHA berada di server publik, URL webhook tidak bisa memakai <code>127.0.0.1</code>. Untuk testing lokal gunakan URL publik seperti ngrok/cloudflared, lalu arahkan ke aplikasi Laravel lokal.</div>
            <div class="mt-2 text-green-800">Aplikasi bisa berjalan offline untuk input transaksi dan produksi. Namun kirim WhatsApp tetap butuh koneksi keluar dari server/VPS ke WAHA Cloud.</div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="flex items-center gap-2"><input type="checkbox" name="waha_enabled" value="1" @checked($settings->get('waha_enabled')==='1')> Aktifkan WAHA</label>
            <input class="rounded border p-3" name="waha_base_url" value="{{ $settings->get('waha_base_url') }}" placeholder="WAHA base URL, contoh http://localhost:3000">
            <input class="rounded border p-3" name="waha_api_key" value="{{ $settings->get('waha_api_key') }}" placeholder="WAHA API Key / X-Api-Key">
            <input class="rounded border p-3" name="waha_session" value="{{ $settings->get('waha_session', 'default') }}" placeholder="Session WAHA">
            <input class="rounded border p-3" name="waha_admin_number" value="{{ $settings->get('waha_admin_number') }}" placeholder="Nomor WA admin">
            <label class="flex items-center gap-2"><input type="checkbox" name="waha_notify_admin_order" value="1" @checked($settings->get('waha_notify_admin_order')==='1')> Notifikasi order ke admin</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="waha_notify_customer_order" value="1" @checked($settings->get('waha_notify_customer_order')==='1')> Notifikasi order ke customer</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="waha_notify_payment" value="1" @checked($settings->get('waha_notify_payment')==='1')> Notifikasi pembayaran</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="waha_verify_ssl" value="1" @checked($settings->get('waha_verify_ssl')==='1')> Verifikasi SSL WAHA</label>
            <div class="text-sm text-slate-500">Untuk XAMPP lokal yang error <code>curl-ca-bundle.crt</code>, biarkan Verifikasi SSL tidak dicentang. Di VPS production sebaiknya dicentang jika CA server valid.</div>
            <textarea class="rounded border p-3 md:col-span-2" name="waha_template_order" placeholder="Template order">{{ $settings->get('waha_template_order', 'Halo {name}, order {order_number} sudah dibuat dengan total {total}.') }}</textarea>
            <textarea class="rounded border p-3 md:col-span-2" name="waha_template_payment" placeholder="Template pembayaran">{{ $settings->get('waha_template_payment', 'Pembayaran order {order_number} berstatus {payment_status}.') }}</textarea>
        </div>
    </section>

    <div class="flex flex-wrap gap-3">
        <button class="btn btn-primary">Simpan Setting</button>
        <button class="btn btn-secondary" formaction="{{ route('admin.settings.waha-test') }}" formmethod="post">Test WAHA Notif</button>
    </div>
</form>
@endsection
