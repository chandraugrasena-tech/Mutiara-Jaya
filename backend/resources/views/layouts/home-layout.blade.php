<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \Illuminate\Support\Str::contains($title, 'Mutiara Jaya') ? $title : $title.' | Mutiara Jaya' }}</title>

    {{-- Tailwind via CDN (sama seperti modul), jadi tidak perlu npm run dev --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#fff2f2] font-sans text-gray-800 min-h-screen flex flex-col pb-20 md:pb-0">

    {{-- navbar halaman depan --}}
    @include('layouts.navigation-home')

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        @include('layouts.flash')

        {{-- Slot: isi halaman yang berubah sesuai view yang dipanggil controller --}}
        {{ $slot }}
    </main>

    @php $cartCount = app(\App\Support\Cart::class)->count(); @endphp

    {{-- Bottom navigation (HP) --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-6 py-2 flex justify-around items-center z-40 shadow-lg">
        <a href="{{ route('home') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('home') ? 'text-[#165823] font-bold' : 'text-gray-400 font-medium' }}">
            Jelajahi
        </a>
        <a href="{{ route('promo') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('promo') ? 'text-[#165823] font-bold' : 'text-gray-400 font-medium' }}">
            Promo
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center text-xs relative {{ request()->routeIs('cart.*') ? 'text-[#165823] font-bold' : 'text-gray-400 font-medium' }}">
            Keranjang
            @if ($cartCount > 0)
                <span class="absolute -top-1 right-0 bg-[#ff788d] text-white text-[10px] font-extrabold px-1.5 rounded-full">{{ $cartCount }}</span>
            @endif
        </a>
        <a href="{{ auth()->check() ? route('orders.index') : route('login') }}" class="flex flex-col items-center text-xs {{ request()->routeIs('orders.*', 'login', 'register') ? 'text-[#165823] font-bold' : 'text-gray-400 font-medium' }}">
            {{ auth()->check() ? 'Pesanan' : 'Masuk' }}
        </a>
    </nav>

    {{-- Footer: keterangan toko dari menu admin "Keterangan Toko" --}}
    @php
        $f = fn ($key) => \App\Models\Setting::val($key);
    @endphp
    <footer class="hidden md:block bg-[#165823] text-[#fff2f2] py-6 mt-12 border-t border-green-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-3 gap-6 text-sm">
            <div>
                <p class="font-bold text-base">{{ $f('store_name') }}</p>
                <p class="text-green-100 mt-1">{{ $f('store_tagline') }}</p>
            </div>
            <div class="space-y-1">
                @if ($f('store_address')) <p>Alamat: {{ $f('store_address') }}</p> @endif
                @if ($f('store_hours')) <p>Jam buka: {{ $f('store_hours') }}</p> @endif
                <p>Area antar: {{ $f('delivery_area') }}</p>
            </div>
            <div class="space-y-1">
                @if ($f('store_phone')) <p>Telepon: {{ $f('store_phone') }}</p> @endif
                @if ($f('store_email')) <p>Email: {{ $f('store_email') }}</p> @endif
                <p><a href="{{ route('about') }}" class="underline">Selengkapnya</a></p>
            </div>
        </div>
    </footer>
</body>
</html>
