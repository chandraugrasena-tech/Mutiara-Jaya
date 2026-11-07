<x-home-layout :title="$title">
    <div class="space-y-6">

        {{-- Banner --}}
        <div class="bg-gradient-to-r from-[#165823] to-[#218335] text-white p-6 rounded-3xl shadow-sm relative overflow-hidden flex justify-between items-center">
            <div class="z-10 max-w-full">
                <span class="bg-[#ff788d] text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">Restoran Mutiara Jaya</span>
                <h2 class="text-xl sm:text-3xl font-black mt-2 leading-tight">Pesan Menu Favoritmu Langsung dari Dapur Kami</h2>
                <p class="text-xs sm:text-sm text-[#ffdada] mt-1">Pengiriman cepat, sajian hangat &amp; higienis setiap hari.</p>
            </div>
        </div>

        {{-- Filter kategori (GET ?category=) --}}
        <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                <a href="{{ route('home', array_filter(['s' => request('s')])) }}"
                   class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap transition-all {{ ! $activeCategory ? 'bg-[#165823] text-white shadow-md' : 'bg-[#fff2f2] text-gray-700 hover:bg-[#ffdada] hover:text-[#165823]' }}">
                    Semua
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('home', array_filter(['category' => $cat->category_id, 's' => request('s')])) }}"
                       class="px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap transition-all {{ ($activeCategory && $activeCategory->category_id == $cat->category_id) ? 'bg-[#165823] text-white shadow-md' : 'bg-[#fff2f2] text-gray-700 hover:bg-[#ffdada] hover:text-[#165823]' }}">
                        {{ $cat->category }}
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="font-extrabold text-gray-800 text-lg sm:text-xl mb-4">
                @if (request()->filled('s'))
                    Hasil untuk "{{ request('s') }}"
                @else
                    Daftar menu: <span class="text-[#165823]">{{ $activeCategory?->category ?? 'Semua' }}</span>
                @endif
            </h3>

            @if ($products->isEmpty())
                <div class="text-center py-12 space-y-3">
                    <p class="text-sm text-gray-600">Menu yang kamu cari belum ada.</p>
                    <a href="{{ route('home') }}" class="text-xs font-bold text-[#165823] underline">Tampilkan semua menu</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($products as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>

                {{-- pagination membawa parameter pencarian & kategori --}}
                <div class="mt-6">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</x-home-layout>
