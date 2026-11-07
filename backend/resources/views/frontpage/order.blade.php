<x-home-layout :title="$title">
    @php
        $steps = \App\Models\Order::STATUS_STEPS;
        $keys = array_keys($steps);
        $cancelled = $order->status === 'dibatalkan';
        $stepIndex = $cancelled ? -1 : array_search($order->status, $keys);
        $current = $cancelled ? null : $steps[$order->status];
        $finished = $cancelled || $order->status === 'selesai';
    @endphp

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Status saat ini --}}
        @if ($cancelled)
            <div class="bg-red-600 text-white p-5 rounded-3xl">
                <p class="text-xs text-red-100">Pesanan #{{ $order->order_code }}</p>
                <h2 class="text-xl font-black mt-1">Pesanan dibatalkan</h2>
                <p class="text-xs text-red-100 mt-1">Hubungi admin jika ini tidak sesuai.</p>
            </div>
        @else
            <div class="bg-gradient-to-r from-[#165823] to-[#218335] text-white p-5 rounded-3xl">
                <p class="text-xs text-[#ffdada]">Pesanan #{{ $order->order_code }}</p>
                <h2 class="text-xl font-black mt-1">{{ $current['title'] }}</h2>
                <p class="text-xs text-[#ffdada] mt-1">{{ $current['desc'] }}</p>
            </div>
        @endif

        {{-- QRIS --}}
        @if ($order->payment_method === 'QRIS' && $order->payment_status === 'belum_bayar' && ! $cancelled)
            <div class="bg-white rounded-2xl p-6 text-center space-y-4 border border-gray-100 shadow-sm">
                <h3 class="font-extrabold text-base text-gray-800">Scan QRIS untuk membayar</h3>
                <p class="text-xs text-gray-500">Total: <strong class="text-[#165823]">{{ rp($order->total) }}</strong></p>
                <div class="bg-gray-100 p-4 rounded-2xl inline-block border border-gray-200">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode('MUTIARAJAYA_QRIS_'.$order->order_code) }}" alt="Kode QRIS" class="w-44 h-44 mx-auto">
                </div>
                <p class="text-[11px] text-gray-500">Buka GoPay, OVO, Dana, atau mobile banking kamu untuk scan.</p>
                <form action="{{ route('orders.pay', $order) }}" method="POST">
                    @csrf
                    <button class="px-6 py-2.5 bg-[#165823] text-white rounded-xl text-xs font-bold hover:bg-[#12481c] cursor-pointer">Saya sudah bayar</button>
                </form>
            </div>
        @endif

        {{-- Tracking --}}
        @unless ($cancelled)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                @foreach ($steps as $key => $s)
                    @php $reached = $loop->index <= $stepIndex; @endphp
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center text-xs font-bold {{ $reached ? 'bg-[#165823] text-white' : 'bg-gray-100 text-gray-400' }}">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="text-sm font-bold {{ $reached ? 'text-gray-800' : 'text-gray-400' }}">{{ $s['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $s['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endunless

        {{-- Rincian --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-2 text-xs text-gray-700">
            <p><strong>Diantar ke:</strong> {{ $order->address_label }} | {{ $order->address_detail }}</p>
            <p><strong>Pembayaran:</strong> {{ $order->payment_method }} ({{ $order->payment_status === 'lunas' ? 'Lunas' : 'Belum dibayar' }})</p>
            @if ($order->notes)<p><strong>Catatan:</strong> {{ $order->notes }}</p>@endif
            <hr>
            @foreach ($order->items as $i)
                <div class="flex justify-between"><span>{{ $i->quantity }}x {{ $i->product_name }}</span><span>{{ rp($i->price * $i->quantity) }}</span></div>
            @endforeach
            <hr>
            <div class="flex justify-between"><span>Subtotal</span><span>{{ rp($order->subtotal) }}</span></div>
            <div class="flex justify-between"><span>Ongkir</span><span>{{ rp($order->ongkir) }}</span></div>
            @if ($order->discount > 0)
                <div class="flex justify-between text-green-700 font-semibold"><span>Promo {{ $order->promo_title ? '('.$order->promo_title.')' : '' }}</span><span>- {{ rp($order->discount) }}</span></div>
            @endif
            <div class="flex justify-between font-extrabold text-sm text-gray-900 pt-1"><span>Total</span><span>{{ rp($order->total) }}</span></div>
        </div>

        <a href="{{ route('home') }}" class="block text-center py-3 rounded-xl text-sm font-bold {{ $finished ? 'bg-[#165823] text-white hover:bg-[#12481c]' : 'border border-[#165823] text-[#165823] hover:bg-green-50' }}">
            {{ $finished ? 'Pesan lagi' : 'Kembali ke beranda' }}
        </a>
    </div>

    {{-- status diperbarui admin: halaman dimuat ulang tiap 15 detik selama pesanan belum selesai --}}
    @unless ($finished)
        <script>setTimeout(() => location.reload(), 15000);</script>
    @endunless
</x-home-layout>
