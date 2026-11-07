<x-home-layout :title="$title">
    @if ($summary['lines']->isEmpty())
        <div class="max-w-2xl mx-auto p-12 text-center space-y-4">
            <h2 class="text-xl font-bold text-gray-800">Keranjang masih kosong</h2>
            <p class="text-xs text-gray-500">Pilih menu dulu dari halaman utama, nanti muncul di sini.</p>
            <a href="{{ route('home') }}" class="inline-block bg-[#165823] text-white text-xs font-bold px-6 py-2.5 rounded-xl hover:bg-[#12481c]">Pilih menu makanan</a>
        </div>
    @else
        @php
            $selected = $summary['selected_promo'];
            $active = $summary['active_promo'];
            $shortfall = $selected && ! $active ? $selected->min_order - $summary['subtotal'] : 0;
        @endphp

        <div class="max-w-2xl mx-auto space-y-5">

            {{-- Alamat --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-gray-800">Anter ke</h3>
                        @auth
                            @if ($address)
                                <p class="text-xs text-gray-700 mt-1.5 break-words"><strong>{{ $address->label }}</strong> | {{ $address->detail }}</p>
                            @else
                                <p class="text-xs text-red-500 mt-1.5 font-semibold">Belum ada alamat pengiriman.</p>
                            @endif
                        @else
                            <p class="text-xs text-gray-500 mt-1.5">Masuk dulu untuk memilih alamat pengantaran.</p>
                        @endauth
                    </div>
                    @auth
                        <a href="{{ route('addresses.index', ['from' => 'cart']) }}" class="text-xs font-bold text-[#165823] border border-[#165823] px-3 py-1.5 rounded-lg hover:bg-green-50 shrink-0">
                            {{ $address ? 'Ganti' : 'Tambah' }}
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Daftar pesanan --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-bold text-gray-800">Pesanan kamu</h2>
                    <a href="{{ route('home') }}" class="text-xs font-bold text-[#165823] hover:underline">+ Tambah menu</a>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 divide-y divide-gray-100 shadow-sm">
                    @foreach ($summary['lines'] as $line)
                        @php $p = $line['product']; @endphp
                        <div class="py-3 flex items-center gap-3 first:pt-0 last:pb-0">
                            <a href="{{ route('product.show', $p) }}">
                                <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-14 h-14 object-cover rounded-xl bg-gray-100">
                            </a>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-sm text-gray-800 truncate">{{ $p->name }}</h4>
                                <p class="text-xs font-extrabold text-[#165823]">{{ rp($p->price * $line['quantity']) }}</p>
                            </div>

                            <div class="flex items-center gap-2 border rounded-xl px-2 py-1 bg-gray-50">
                                <form action="{{ route('cart.update', $p) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="delta" value="-1">
                                    <button class="w-6 h-6 rounded-lg bg-white border font-bold text-xs hover:bg-gray-100 cursor-pointer" aria-label="Kurangi">-</button>
                                </form>
                                <span class="text-xs font-bold w-4 text-center">{{ $line['quantity'] }}</span>
                                <form action="{{ route('cart.update', $p) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="delta" value="1">
                                    <button class="w-6 h-6 rounded-lg bg-white border font-bold text-xs hover:bg-gray-100 cursor-pointer" aria-label="Tambah">+</button>
                                </form>
                            </div>

                            <form action="{{ route('cart.destroy', $p) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-xs font-bold px-1 cursor-pointer" aria-label="Hapus">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Promo --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-gray-800">Promo</h3>
                        @if ($active)
                            <p class="text-xs text-green-700 font-semibold mt-1">{{ $active->title }} | hemat {{ rp($summary['discount']) }}</p>
                        @elseif ($shortfall > 0)
                            <p class="text-xs text-red-500 font-semibold mt-1">{{ $selected->title }} belum berlaku, belanja {{ rp($shortfall) }} lagi</p>
                        @else
                            <p class="text-xs text-gray-500 mt-1">Pilih promo atau masukkan kode</p>
                        @endif
                    </div>
                    <a href="{{ route('promo') }}" class="text-xs font-bold text-[#165823] shrink-0">Lihat promo ›</a>
                </div>

                <form action="{{ route('cart.promo') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="Punya kode promo? Ketik di sini" value="{{ old('code') }}"
                           class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-xs uppercase focus:outline-none focus:border-[#165823]">
                    <button class="bg-[#165823] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#12481c] cursor-pointer">Pakai</button>
                </form>
                @error('code') <p class="text-[11px] text-red-500 font-semibold">{{ $message }}</p> @enderror

                @if ($selected)
                    <form action="{{ route('cart.promo.remove') }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-[11px] font-bold text-red-500 hover:underline cursor-pointer">Lepas promo</button>
                    </form>
                @endif
            </div>

            {{-- Catatan, pembayaran, ringkasan --}}
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2">
                    <h3 class="font-bold text-sm text-gray-800">Catatan untuk restoran</h3>
                    <input type="text" name="notes" value="{{ old('notes') }}" maxlength="255" placeholder="Contoh: sambal dipisah ya"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#165823]">
                </div>

                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                    <h3 class="font-bold text-sm text-gray-800">Metode pembayaran</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ([['CASH', 'CASH', 'Bayar di tempat'], ['QRIS', 'QRIS', 'Gopay, OVO, Shopee, BCA']] as [$key, $name, $sub])
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="{{ $key }}" class="peer sr-only" @checked(old('payment_method', 'CASH') === $key)>
                                <div class="p-3 rounded-xl border border-gray-200 hover:border-gray-300 text-gray-700 flex items-center gap-3 peer-checked:border-[#165823] peer-checked:bg-green-50 peer-checked:text-[#165823]">
                                    <div class="text-left">
                                        <p class="text-xs font-bold">{{ $name }}</p>
                                        <p class="text-[10px] text-gray-500 font-normal">{{ $sub }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600 text-xs"><span>Subtotal</span><span>{{ rp($summary['subtotal']) }}</span></div>
                    <div class="flex justify-between text-gray-600 text-xs"><span>Ongkir</span><span>{{ rp($summary['ongkir']) }}</span></div>
                    @if ($summary['discount'] > 0)
                        <div class="flex justify-between text-green-700 text-xs font-semibold"><span>Diskon promo</span><span>- {{ rp($summary['discount']) }}</span></div>
                    @endif
                    <hr class="my-2">
                    <div class="flex justify-between font-extrabold text-base text-gray-900"><span>Total</span><span>{{ rp($summary['total']) }}</span></div>

                    @auth
                        <button type="submit" class="w-full mt-3 bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-md active:scale-95 cursor-pointer">
                            Pesan sekarang | {{ rp($summary['total']) }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="block text-center w-full mt-3 bg-[#ff788d] hover:bg-[#ff5f78] text-white font-bold py-3.5 rounded-xl text-sm">
                            Masuk untuk memesan
                        </a>
                    @endauth
                </div>
            </form>
        </div>
    @endif
</x-home-layout>
