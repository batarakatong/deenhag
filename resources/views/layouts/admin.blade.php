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
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.65rem;padding:.72rem 1rem;font-weight:700;font-size:.875rem;transition:all .18s ease;box-shadow:0 1px 2px rgba(15,23,42,.08)}
        .btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(15,23,42,.12)}
        .btn-primary{background:#16a34a;color:white}
        .btn-secondary{background:white;color:#334155;border:1px solid #cbd5e1}
        .btn-danger{background:#dc2626;color:white}
        .field{border:1px solid #cbd5e1;border-radius:.65rem;padding:.72rem .9rem;background:white}
        .panel{border-radius:1rem;background:white;box-shadow:0 8px 24px rgba(15,23,42,.06)}
    </style>
</head>
@php
    $theme = $appSettings->get('theme_name', 'professional');
    $companyName = $appSettings->get('company_name', 'GreenPrinting');
    $logo = $appSettings->get('company_logo');
    $unreadCount = auth()->check() ? \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() : 0;
    $latestNotifications = auth()->check() ? \App\Models\Notification::where('user_id', auth()->id())->latest()->limit(5)->get() : collect();
@endphp
<body class="{{ $theme === 'dark' ? 'bg-slate-950' : 'bg-slate-100' }} text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="bg-slate-950 p-5 text-slate-200 lg:w-72">
            <a href="{{ route('admin.dashboard') }}" class="mb-8 flex items-center gap-3">
                @if($logo)
                    <img class="h-10 w-10 rounded bg-white object-contain p-1" src="{{ asset(str_replace('public/', 'storage/', $logo)) }}" alt="{{ $companyName }}">
                @else
                    <div class="grid h-10 w-10 place-items-center rounded bg-green-500 font-bold text-white">GP</div>
                @endif
                <div>
                    <div class="font-bold text-white">{{ $companyName }}</div>
                    <div class="text-xs text-slate-400">Backoffice</div>
                </div>
            </a>
            <nav class="grid gap-1 text-sm">
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
                    <a class="rounded px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white" href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </nav>
        </aside>
        <div class="flex-1">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 px-6 py-4 backdrop-blur">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">{{ $appSettings->get('header_text', 'Sistem Manajemen Percetakan') }}</div>
                        <div class="font-semibold">{{ $title ?? 'Backoffice' }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input class="hidden rounded border border-slate-200 bg-slate-50 px-3 py-2 text-sm md:block" placeholder="Cari cepat...">
                        <a class="relative rounded-full border bg-white p-2" href="{{ route('admin.notifications.index') }}" title="Notifikasi">
                            <span class="text-lg">🔔</span>
                            @if($unreadCount)
                                <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 text-xs text-white">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded border px-4 py-2 text-sm text-slate-700">Logout</button></form>
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
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ $errors->first() }}</div>
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
