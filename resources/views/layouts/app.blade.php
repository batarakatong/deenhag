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
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.65rem;padding:.72rem 1rem;font-weight:700;font-size:.875rem;transition:all .18s ease;box-shadow:0 1px 2px rgba(15,23,42,.08)}
        .btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(15,23,42,.12)}
        .btn-primary{background:#16a34a;color:white}
        .btn-secondary{background:white;color:#334155;border:1px solid #cbd5e1}
        .btn-danger{background:#dc2626;color:white}
        .field{border:1px solid #cbd5e1;border-radius:.65rem;padding:.72rem .9rem;background:white}
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="border-b bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-xl font-bold text-green-700">
                @if($appSettings->get('company_logo'))
                    <img class="h-9 w-9 rounded object-contain" src="{{ asset(str_replace('public/', 'storage/', $appSettings->get('company_logo'))) }}" alt="{{ $appSettings->get('company_name', 'GreenPrinting') }}">
                @endif
                {{ $appSettings->get('company_name', 'GreenPrinting') }}
            </a>
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <a href="{{ route('products.index') }}">Produk</a>
                <a href="{{ route('track') }}">Cek Pesanan</a>
                @auth
                    @if(auth()->user()->hasRole('admin','staff'))
                        <a href="{{ route('admin.dashboard') }}">Admin</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                        <a href="{{ route('cart.index') }}">Keranjang</a>
                        <a href="{{ route('customer.notifications.index') }}">Notifikasi</a>
                        <a href="{{ route('customer.profile.edit') }}">Akun</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">@csrf<button>Logout</button></form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a class="rounded bg-green-600 px-4 py-2 text-white" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8">
        @if($appSettings->get('header_text'))
            <div class="mb-6 rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-900">{{ $appSettings->get('header_text') }}</div>
        @endif
        @if(session('status'))
            <div class="mb-5 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="border-t bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-slate-500">{{ $appSettings->get('footer_text', 'GreenPrinting - layanan percetakan online') }}</div>
    </footer>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</body>
</html>
