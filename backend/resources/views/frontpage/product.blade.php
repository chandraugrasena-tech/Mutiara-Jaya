<x-home-layout :title="$title">
    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 max-w-4xl mx-auto">
        <div class="w-full md:w-1/2">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-64 md:h-96 object-cover rounded-2xl shadow-sm">
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center">
            <span class="inline-block bg-[#ffdada] text-[#165823] px-3 py-1 rounded-lg text-sm font-bold w-fit mb-4">{{ $product->category?->category }}</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#165823] mb-2">{{ $product->name }}</h1>
            <p class="text-[#ff788d] text-2xl font-extrabold mb-1">{{ rp($product->price) }}</p>
            <p class="text-xs text-gray-500 mb-6">Estimasi {{ $product->time ?: '15-25 min' }} | Stok: {{ $product->stock }}</p>

            <div class="mb-6">
                <h3 class="font-bold text-gray-800 mb-2">Deskripsi menu</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $product->description ?: 'Hidangan spesial dari Mutiara Jaya yang disiapkan dengan bahan berkualitas tinggi dan bumbu pilihan.' }}
                </p>
            </div>

            @guest
                <div class="space-y-3">
                    <p class="text-sm text-gray-600">Masuk dulu untuk memasukkan menu ini ke keranjang.</p>
                    <div class="flex gap-3">
                        <a href="{{ route('login', ['need' => 1]) }}" class="flex-1 text-center text-white py-3 rounded-xl font-bold bg-[#165823] hover:bg-[#12481c]">Masuk</a>
                        <a href="{{ route('register', ['need' => 1]) }}" class="flex-1 text-center py-3 rounded-xl font-bold border-2 border-[#165823] text-[#165823] hover:bg-gray-50">Daftar</a>
                    </div>
                </div>
            @endguest
            @auth
            <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex items-center gap-3">
                    <label for="qty" class="text-sm font-bold text-gray-700">Jumlah</label>
                    <input type="number" id="qty" name="qty" value="1" min="1" max="99"
                           class="w-20 border rounded-xl px-3 py-2 text-center font-bold bg-gray-50 focus:outline-none focus:border-[#165823]">
                </div>

                <div class="flex gap-3">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
                       class="px-5 py-3 border-2 border-[#165823] text-[#165823] font-bold rounded-xl hover:bg-gray-50 transition-colors">Kembali</a>
                    <button type="submit" @disabled($product->stock <= 0)
                            class="flex-1 text-white py-3 rounded-xl font-bold bg-[#ff788d] hover:bg-[#ff5f78] hover:shadow-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                        {{ $product->stock <= 0 ? 'Stok habis' : '+ Masukkan keranjang' }}
                    </button>
                </div>
            </form>
            @endauth

            @auth
                <a href="{{ route('cart.index') }}" class="mt-3 text-center text-sm font-bold text-[#165823] underline">Lihat keranjang</a>
            @endauth
        </div>
    </div>

    @if ($related->isNotEmpty())
        <div class="max-w-4xl mx-auto mt-8">
            <h3 class="font-extrabold text-gray-800 text-lg mb-4">Menu lain yang mungkin kamu suka</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </div>
    @endif
</x-home-layout>
