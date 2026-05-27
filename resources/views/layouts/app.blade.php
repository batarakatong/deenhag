<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $appSettings->get('company_name', 'GreenPrinting') }}</title>
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
    </style>
</head>
<body class="bg-[#f3f7f5] text-slate-900">
    <nav class="sticky top-0 z-30 border-b border-emerald-100 bg-white/90 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl font-black text-emerald-700">
                @if($appSettings->get('company_logo'))
                    <img class="h-10 w-10 rounded-xl bg-white object-contain ring-1 ring-emerald-100" src="{{ $mediaUrl($appSettings->get('company_logo')) }}" alt="{{ $appSettings->get('company_name', 'GreenPrinting') }}" decoding="async">
                @else
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-600 text-sm font-black text-white">GP</span>
                @endif
                {{ $appSettings->get('company_name', 'GreenPrinting') }}
            </a>
            <div class="flex flex-wrap items-center gap-3 text-sm font-semibold text-slate-700">
                <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('products.index') }}">Produk</a>
                <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('track') }}">Cek Pesanan</a>
                @auth
                    @if(auth()->user()->hasRole('admin','staff'))
                        <a class="rounded-xl bg-emerald-600 px-4 py-2 text-white" href="{{ route('admin.dashboard') }}">Admin</a>
                    @else
                        <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('customer.dashboard') }}">Dashboard</a>
                        <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('cart.index') }}">Keranjang</a>
                        <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('customer.notifications.index') }}">Notifikasi</a>
                        <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('customer.profile.edit') }}">Akun</a>
                        @if(auth()->user()->avatar_path)
                            <img class="h-9 w-9 rounded-full object-cover ring-2 ring-emerald-100" src="{{ $mediaUrl(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}" decoding="async">
                        @else
                            <div class="grid h-9 w-9 place-items-center rounded-full bg-green-100 text-sm font-bold text-green-700">{{ substr(auth()->user()->name,0,1) }}</div>
                        @endif
                    @endif
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded-xl border border-slate-200 px-3 py-2 text-slate-700">Logout</button></form>
                    <a class="text-xs text-slate-500" href="{{ route('logout.get') }}">Keluar cepat</a>
                @else
                    <a class="rounded-lg px-2 py-1 hover:text-emerald-700" href="{{ route('login') }}">Login</a>
                    <a class="rounded-xl bg-emerald-600 px-4 py-2 text-white shadow-sm" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8">
        @if($appSettings->get('header_text'))
            <div class="mb-6 rounded-2xl border border-green-100 bg-white px-5 py-4 text-sm font-medium text-green-900 shadow-sm">{{ $appSettings->get('header_text') }}</div>
        @endif
        @if(session('status'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-sm">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-sm">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="border-t border-emerald-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-slate-500">{{ $appSettings->get('footer_text', 'GreenPrinting - layanan percetakan online') }}</div>
    </footer>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</body>
</html>
