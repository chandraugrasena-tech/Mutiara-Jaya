@php
    $navCartCount = app(\App\Support\Cart::class)->count();
    $navAddress = auth()->check() ? auth()->user()->defaultAddress() : null;
@endphp

<header class="sticky top-0 z-50 bg-[#165823] text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-3 gap-y-2">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="font-black text-lg sm:text-xl tracking-wide flex items-center gap-2 shrink-0">
            <span>{{ \App\Models\Setting::val('store_name') }}</span>
        </a>

        {{-- Alamat "Anter ke" --}}
        @auth
            <a href="{{ route('addresses.index') }}"
               class="order-3 w-full md:order-none md:w-auto flex items-center gap-1.5 bg-[#1d6b2c] hover:bg-[#258338] active:scale-95 px-3 py-1.5 rounded-full text-xs font-semibold transition-all border border-green-400/40 shadow-sm">
                <span class="text-green-200 shrink-0">Anter ke:</span>
                <span class="truncate md:max-w-[200px]">{{ $navAddress ? $navAddress->label.' | '.$navAddress->detail : 'Pilih alamat' }}</span>
            </a>
        @else
            <a href="{{ route('login') }}"
               class="order-3 w-full md:order-none md:w-auto flex items-center gap-1.5 bg-[#1d6b2c] hover:bg-[#258338] px-3 py-1.5 rounded-full text-xs font-semibold border border-green-400/40 shadow-sm">
                <span class="text-green-200 shrink-0">Anter ke:</span><span>Masuk untuk pilih alamat</span>
            </a>
        @endauth

        {{-- Pencarian (GET ?s=) --}}
        <form action="{{ route('home') }}" method="GET" class="order-4 w-full md:order-none md:w-auto md:flex-1 md:max-w-md md:mx-2">
            <div class="relative">
                <input type="text" name="s" value="{{ request('s') }}" placeholder="Mau makan apa hari ini?"
                       class="w-full bg-white text-gray-800 text-xs sm:text-sm pl-4 pr-8 py-2 rounded-full focus:outline-none shadow-inner">
                @if (request('s'))
                    <a href="{{ route('home') }}" class="absolute right-3 top-2 text-gray-400 hover:text-gray-600 text-xs" aria-label="Hapus pencarian">Hapus</a>
                @endif
            </div>
        </form>

        {{-- Keranjang (HP) --}}
        <a href="{{ route('cart.index') }}" class="md:hidden ml-auto flex items-center gap-1.5 text-sm font-bold">
            <span>Keranjang</span>
            @if ($navCartCount > 0)
                <span class="bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">{{ $navCartCount }}</span>
            @endif
        </a>

        {{-- Menu (laptop) --}}
        <nav class="hidden md:flex items-center gap-6 ml-auto text-sm font-bold">
            <a href="{{ route('home') }}" class="hover:text-green-200 {{ request()->routeIs('home') ? 'underline underline-offset-4 decoration-2' : '' }}">Jelajahi</a>
            <a href="{{ route('promo') }}" class="hover:text-green-200 {{ request()->routeIs('promo') ? 'underline underline-offset-4 decoration-2 text-yellow-300' : '' }}">Promo</a>
            <a href="{{ route('about') }}" class="hover:text-green-200 {{ request()->routeIs('about') ? 'underline underline-offset-4 decoration-2' : '' }}">Tentang</a>
            <a href="{{ route('cart.index') }}" class="hover:text-green-200 flex items-center gap-1.5 {{ request()->routeIs('cart.*') ? 'underline underline-offset-4 decoration-2' : '' }}">
                <span>Keranjang</span>
                @if ($navCartCount > 0)
                    <span class="bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">{{ $navCartCount }}</span>
                @endif
            </a>

            @auth
                <a href="{{ route('orders.index') }}" class="hover:text-green-200 {{ request()->routeIs('orders.*') ? 'underline underline-offset-4 decoration-2' : '' }}">Pesanan</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-full text-xs">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-green-200 text-xs font-bold cursor-pointer">Keluar ({{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hover:text-green-200">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[#ff788d] hover:bg-[#ff5f78] px-4 py-1.5 rounded-full text-xs">Daftar</a>
            @endauth
        </nav>
    </div>
</header>
