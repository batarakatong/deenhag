<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin GreenPrinting' }}</title>
    <meta name="theme-color" content="#16a34a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="GreenPrinting">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.75rem;padding:.72rem 1rem;font-weight:700;font-size:.875rem;transition:all .18s ease;box-shadow:0 1px 2px rgba(15,23,42,.08)}
        .btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(15,23,42,.14)}
        .btn-primary{background:linear-gradient(135deg,#16a34a,#047857);color:white}
        .btn-secondary{background:white;color:#334155;border:1px solid #dbe3ee}
        .btn-danger{background:#dc2626;color:white}
        .field{border:1px solid #dbe3ee;border-radius:.75rem;padding:.72rem .9rem;background:white;outline:none}
        .field:focus{border-color:#22c55e;box-shadow:0 0 0 4px rgba(34,197,94,.12)}
        .panel{border:1px solid rgba(226,232,240,.9);border-radius:1rem;background:white;box-shadow:0 12px 34px rgba(15,23,42,.07)}
    </style>
</head>
@php
    $theme = $appSettings->get('theme_name', 'professional');
    $companyName = $appSettings->get('company_name', 'GreenPrinting');
    $logo = $appSettings->get('company_logo');
    $unreadCount = auth()->check() ? \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() : 0;
    $latestNotifications = auth()->check() ? \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(5)->get() : collect();
@endphp
<body class="{{ $theme === 'dark' ? 'bg-slate-950' : 'bg-[#f3f7f5]' }} text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="relative overflow-hidden bg-gradient-to-b from-emerald-950 via-emerald-800 to-green-700 p-5 text-emerald-50 shadow-2xl lg:fixed lg:inset-y-0 lg:w-72">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-72 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.18),transparent_38%)]"></div>
            <a href="{{ route('admin.dashboard') }}" class="relative mb-8 flex items-center gap-3 rounded-2xl bg-white/10 p-3 ring-1 ring-white/10">
                @if($logo)
                    <img class="h-11 w-11 rounded-xl bg-white object-contain p-1" src="{{ $mediaUrl($logo) }}" alt="{{ $companyName }}" decoding="async">
                @else
                    <div class="grid h-11 w-11 place-items-center rounded-xl bg-white font-black text-emerald-700">GP</div>
                @endif
                <div>
                    <div class="font-bold text-white">{{ $companyName }}</div>
                    <div class="text-xs text-emerald-100">Professional Backoffice</div>
                </div>
            </a>
            <nav class="relative grid gap-1 text-sm font-semibold">
                @foreach([
                    ['Dashboard', route('admin.dashboard')],
                    ['Produk', route('admin.products.index')],
                    ['Kategori', route('admin.categories.index')],
                    ['Pesanan', route('admin.orders.index')],
                    ['Pembayaran', route('admin.payments.index')],
                    ['Customer', route('admin.customers.index')],
                    ['Produksi', route('admin.production.index')],
                    ['Alur Produksi', route('admin.production.steps')],
                    ['Stok Bahan', route('admin.materials.index')],
                    ['Laporan Penjualan', route('admin.reports.sales')],
                    ['Laporan Stok', route('admin.reports.stocks')],
                    ['Notifikasi', route('admin.notifications.index')],
                    ['Setting', route('admin.settings.index')],
                    ['About Greentech.dev', route('admin.settings.about')],
                ] as [$label, $url])
                    <a class="rounded-xl px-3 py-2.5 text-emerald-50/85 transition hover:bg-white/15 hover:text-white {{ request()->fullUrlIs($url) ? 'bg-white/20 text-white shadow-lg' : '' }}" href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </nav>
        </aside>
        <div class="flex-1 lg:ml-72">
            <header class="sticky top-0 z-20 border-b border-emerald-100 bg-white/90 px-6 py-4 shadow-sm backdrop-blur">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-emerald-700">{{ $appSettings->get('header_text', 'Sistem Manajemen Percetakan') }}</div>
                        <div class="text-lg font-bold text-slate-900">{{ $title ?? 'Backoffice' }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a class="hidden rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100 md:inline-flex" href="{{ route('home') }}" target="_blank">Visit Website</a>
                        <input class="hidden rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 md:block" placeholder="Cari cepat...">
                        <a class="relative rounded-full border border-slate-200 bg-white p-2 shadow-sm" href="{{ route('admin.notifications.index') }}" title="Notifikasi">
                            <span class="text-lg">🔔</span>
                            @if($unreadCount)
                                <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 text-xs text-white">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        @if(auth()->user()->avatar_path)
                            <img class="h-10 w-10 rounded-full object-cover ring-2 ring-emerald-100" src="{{ $mediaUrl(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" decoding="async">
                        @else
                            <div class="grid h-10 w-10 place-items-center rounded-full bg-green-100 font-bold text-green-700">{{ substr(auth()->user()->name,0,1) }}</div>
                        @endif
                        <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Logout</button></form>
                        <a class="text-xs text-slate-500" href="{{ route('logout.get') }}">Keluar cepat</a>
                    </div>
                </div>
                @if($latestNotifications->count())
                    <div class="mt-3 flex gap-2 overflow-x-auto text-xs">
                        @foreach($latestNotifications as $notification)
                            <span class="rounded-full bg-green-50 px-3 py-1 text-green-800">{{ $notification->title }}</span>
                        @endforeach
                    </div>
                @endif
            </header>
            <main class="p-6">
                @if(session('status'))
                    <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-sm">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-sm">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
            <footer class="px-6 pb-6 text-xs text-slate-500">{{ $appSettings->get('footer_text', 'GreenPrinting backoffice') }}</footer>
        </div>
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</body>
</html>
