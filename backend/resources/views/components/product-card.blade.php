@props(['product'])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col justify-between">
    <div>
        <a href="{{ route('product.show', $product) }}" class="block relative h-36 sm:h-44 w-full bg-gray-100 overflow-hidden">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            @if ($product->stock <= 0)
                <span class="absolute inset-0 bg-black/50 text-white text-xs font-extrabold flex items-center justify-center">HABIS</span>
            @endif
        </a>

        <div class="p-3">
            <div class="flex items-center gap-1 text-[11px] font-bold text-gray-500 mb-1">
                <span class="text-yellow-600">Rating 4.8</span>
                <span>|</span>
                <span>{{ $product->time ?: '15-25 min' }}</span>
            </div>
            <h4 class="font-bold text-gray-800 text-sm sm:text-base line-clamp-1">{{ $product->name }}</h4>
            <p class="text-gray-500 text-xs line-clamp-2 mt-1">{{ $product->description }}</p>
            <div class="mt-3 font-extrabold text-gray-900 text-sm sm:text-base">{{ rp($product->price) }}</div>
        </div>
    </div>

    <div class="p-3 pt-0 flex gap-2 mt-2">
        <a href="{{ route('product.show', $product) }}"
           class="flex-1 text-center border border-[#165823] text-[#165823] text-xs font-bold py-2 rounded-xl hover:bg-green-50 transition-colors flex items-center justify-center">
            Detail
        </a>
        @auth
            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" @disabled($product->stock <= 0)
                        class="w-full text-white text-xs font-bold py-2 rounded-xl bg-[#165823] hover:bg-[#12481c] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                    + Tambah
                </button>
            </form>
        @else
            {{-- pengunjung belum login: arahkan ke halaman masuk --}}
            <a href="{{ route('login', ['need' => 1]) }}"
               class="flex-1 text-center text-white text-xs font-bold py-2 rounded-xl bg-[#165823] hover:bg-[#12481c] flex items-center justify-center">
                + Tambah
            </a>
        @endauth
    </div>
</div>
