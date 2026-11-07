<x-admin-layout :title="$title">
    <div class="space-y-5">
        <form action="{{ route('admin.revenue') }}" method="GET" class="bg-white rounded shadow p-4 flex flex-wrap items-end gap-3">
            <div>
                <label for="from" class="block text-xs font-medium text-gray-600 mb-1">Dari tanggal</label>
                <input type="date" id="from" name="from" value="{{ $from->toDateString() }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-gray-600 mb-1">Sampai tanggal</label>
                <input type="date" id="to" name="to" value="{{ $to->toDateString() }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
            <button class="px-4 py-2 text-sm rounded-md bg-[#165823] text-white hover:bg-[#12481c] cursor-pointer">Tampilkan</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 cursor-pointer">Cetak</button>
            @error('to') <p class="text-xs text-red-700 w-full">{{ $message }}</p> @enderror
        </form>

        <p class="text-xs text-gray-500">Pemasukan dihitung dari pesanan yang sudah lunas. Pesanan dibatalkan tidak dihitung. Pesanan CASH otomatis lunas saat statusnya Selesai.</p>

        <div class="grid grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Pemasukan (lunas)</p><p class="text-xl font-extrabold text-[#165823]">{{ rp($summary['income']) }}</p></div>
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Menunggu pembayaran</p><p class="text-xl font-extrabold text-orange-600">{{ rp($summary['pending']) }}</p></div>
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Jumlah pesanan</p><p class="text-xl font-extrabold">{{ $summary['orders'] }}</p></div>
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Rata-rata per pesanan</p><p class="text-xl font-extrabold">{{ rp($summary['average']) }}</p></div>
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Pemasukan CASH</p><p class="text-xl font-extrabold">{{ rp($summary['cash']) }}</p></div>
            <div class="bg-white rounded shadow p-4"><p class="text-xs text-gray-500">Pemasukan QRIS</p><p class="text-xl font-extrabold">{{ rp($summary['qris']) }}</p></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded shadow lg:col-span-2 overflow-x-auto">
                <p class="p-4 border-b font-bold">Rekap per hari ({{ $from->format('d/m/Y') }} sampai {{ $to->format('d/m/Y') }})</p>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3 text-right">Pesanan</th><th class="px-4 py-3 text-right">Pemasukan</th><th class="px-4 py-3 text-right">Belum bayar</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($perDay as $d)
                            <tr>
                                <td class="px-4 py-3">{{ $d['date']->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">{{ $d['orders'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ rp($d['income']) }}</td>
                                <td class="px-4 py-3 text-right text-orange-600">{{ rp($d['pending']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada pesanan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded shadow p-4">
                <p class="font-bold mb-3">Menu terlaris (periode ini)</p>
                @forelse ($topProducts as $t)
                    <div class="flex justify-between gap-2 text-sm py-1.5 border-b last:border-0">
                        <span class="truncate">{{ $t->product_name }}</span>
                        <span class="shrink-0 text-right"><span class="font-bold">{{ $t->qty }}x</span><span class="block text-xs text-gray-500">{{ rp($t->revenue) }}</span></span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
