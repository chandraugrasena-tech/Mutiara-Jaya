<x-home-layout :title="$title">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between border-b pb-3">
            <h2 class="text-xl font-bold text-gray-800">Promo &amp; voucher</h2>
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#165823] hover:underline">Kembali ke menu</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse ($promos as $p)
                @php
                    $used = $summary['selected_promo'] && $summary['selected_promo']->promo_id == $p->promo_id;
                    $hasCart = $summary['subtotal'] > 0;
                    $eligible = $summary['subtotal'] >= $p->min_order;
                @endphp
                <div class="bg-white p-5 rounded-2xl border shadow-sm space-y-3 {{ $used ? 'border-[#165823]' : 'border-gray-100' }}">
                    <h3 class="font-bold text-base text-[#165823]">{{ $p->title }}</h3>
                    <p class="text-xs text-gray-600">{{ $p->description }}</p>
                    <p class="text-[11px] text-gray-500">
                        Min. belanja {{ rp($p->min_order) }}
                        @if ($hasCart && ! $eligible)
                            <span class="text-red-500 font-semibold"> | kurang {{ rp($p->min_order - $summary['subtotal']) }}</span>
                        @endif
                    </p>
                    <div class="flex items-center justify-between gap-2">
                        <div class="bg-gray-100 px-2 py-1 rounded-lg text-xs font-mono font-bold text-gray-800 border">{{ $p->code }}</div>

                        @if ($used)
                            <form action="{{ route('cart.promo.remove') }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-xs font-bold px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer">Lepas promo</button>
                            </form>
                        @else
                            <form action="{{ route('cart.promo') }}" method="POST">
                                @csrf
                                <input type="hidden" name="code" value="{{ $p->code }}">
                                <button class="text-xs font-bold px-4 py-2 rounded-xl bg-[#165823] text-white hover:bg-[#12481c] cursor-pointer">Pakai promo</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada promo aktif.</p>
            @endforelse
        </div>
    </div>
</x-home-layout>
