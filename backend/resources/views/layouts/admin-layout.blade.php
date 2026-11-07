<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | Admin Mutiara Jaya</title>

    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js untuk buka/tutup sidebar di HP --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
@php
    $menus = [
        ['admin.dashboard', 'Dashboard', 'admin.dashboard'],
        ['admin.orders.index', 'Pesanan', 'admin.orders.*'],
        ['admin.revenue', 'Pemasukan', 'admin.revenue'],
        ['admin.stock.index', 'Stok Barang', 'admin.stock.*'],
        ['admin.products.index', 'Menu', 'admin.products.*'],
        ['admin.categories.index', 'Kategori', 'admin.categories.*'],
        ['admin.promos.index', 'Promo', 'admin.promos.*'],
        ['admin.settings.edit', 'Keterangan Toko', 'admin.settings.*'],
    ];
@endphp

<div x-data="{ open: false }" class="flex h-screen">

    {{-- Sidebar --}}
    <aside :class="open ? 'block' : 'hidden'" class="md:block fixed md:static inset-y-0 left-0 z-40 w-64 bg-white shadow-xl shrink-0 overflow-y-auto">
        <div class="p-5 border-b">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-xl text-[#165823]">Admin Mutiara Jaya</a>
        </div>
        <nav class="flex flex-col p-4 space-y-1">
            @foreach ($menus as [$route, $label, $pattern])
                <a href="{{ route($route) }}"
                   class="block p-2.5 rounded text-sm font-semibold {{ request()->routeIs($pattern) ? 'bg-[#165823] text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach

            <a href="{{ route('home') }}" class="block p-2.5 rounded text-sm font-semibold text-gray-500 hover:bg-gray-200">
                Kembali ke toko
            </a>
        </nav>
        <div class="p-4 border-t mt-2">
            <p class="text-xs text-gray-500">Masuk sebagai</p>
            <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button class="text-xs font-bold text-red-600 hover:underline cursor-pointer">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        {{-- Header HP --}}
        <div class="md:hidden bg-white shadow p-4 flex justify-between items-center">
            <h1 class="font-bold text-[#165823]">Admin Mutiara Jaya</h1>
            <button type="button" class="p-2 border rounded cursor-pointer" @click="open = !open">Menu</button>
        </div>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">{{ $title }}</h2>

            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm font-semibold rounded-lg p-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-lg p-3">{{ session('error') }}</div>
            @endif

            {{ $slot }}
        </main>

        <footer class="bg-white border-t p-4 text-center text-sm">© {{ date('Y') }} Admin Mutiara Jaya | v1.0.0</footer>
    </div>
</div>
</body>
</html>
