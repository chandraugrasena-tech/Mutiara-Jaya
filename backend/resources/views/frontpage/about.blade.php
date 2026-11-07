<x-home-layout :title="$title">
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="bg-gradient-to-r from-[#165823] to-[#218335] text-white rounded-3xl p-6">
            <h1 class="text-2xl font-black">{{ $info['store_name'] }}</h1>
            <p class="text-sm text-green-100 mt-1">{{ $info['store_tagline'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-2">
            <h2 class="font-bold text-lg text-gray-800">Tentang kami</h2>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $info['store_description'] }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
                <h3 class="font-bold text-gray-800">Pengantaran</h3>
                <p><span class="text-gray-500">Area antar:</span> {{ $info['delivery_area'] }}</p>
                <p><span class="text-gray-500">Ongkos kirim:</span> {{ rp($info['delivery_fee']) }}</p>
                <p><span class="text-gray-500">Pembayaran:</span> CASH dan QRIS</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
                <h3 class="font-bold text-gray-800">Informasi toko</h3>
                <p><span class="text-gray-500">Alamat:</span> {{ $info['store_address'] ?: 'Belum diisi' }}</p>
                <p><span class="text-gray-500">Jam buka:</span> {{ $info['store_hours'] ?: 'Belum diisi' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2 text-sm">
            <h3 class="font-bold text-gray-800">Hubungi kami</h3>
            @if ($info['store_phone'])<p><span class="text-gray-500">Telepon:</span> {{ $info['store_phone'] }}</p>@endif
            @if ($info['store_whatsapp'])<p><span class="text-gray-500">WhatsApp:</span> <a href="https://wa.me/{{ $info['store_whatsapp'] }}" target="_blank" rel="noreferrer" class="text-[#165823] font-bold underline">Chat sekarang</a></p>@endif
            @if ($info['store_email'])<p><span class="text-gray-500">Email:</span> {{ $info['store_email'] }}</p>@endif
        </div>
    </div>
</x-home-layout>
