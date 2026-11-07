<x-admin-layout :title="$title">
    <div class="space-y-6">

        @if ($errors->has('amount') || $errors->has('action'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded p-3">{{ $errors->first('amount') ?: $errors->first('action') }}</div>
        @endif

        <div class="shadow bg-white rounded">
            {{-- Filter --}}
            <form action="{{ route('admin.stock.index') }}" method="GET" class="p-4 grid grid-cols-1 md:grid-cols-4 gap-2">
                <select name="category" class="border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->category_id }}" @selected((string) request('category') === (string) $c->category_id)>{{ $c->category }}</option>
                    @endforeach
                </select>
                <select name="level" class="border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">Semua stok</option>
                    <option value="menipis" @selected(request('level') === 'menipis')>Menipis (maks. {{ $limit }})</option>
                    <option value="habis" @selected(request('level') === 'habis')>Habis</option>
                </select>
                <input type="text" name="s" value="{{ request('s') }}" placeholder="Cari menu" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <button class="rounded-md border px-4 py-2 border-gray-300 bg-gray-50 text-[#165823] hover:text-white hover:bg-[#165823] text-sm cursor-pointer">Cari</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Menu</th><th class="px-6 py-3">Kategori</th><th class="px-6 py-3">Stok</th><th class="px-6 py-3">Ubah stok</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $p)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-10 w-10 rounded-full object-cover bg-gray-100" src="{{ $p->image_url }}" alt="">
                                        <span class="font-medium text-gray-900">{{ $p->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $p->category?->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-bold">{{ $p->stock }}</span>
                                    @if ($p->stock <= 0)
                                        <span class="ml-1 px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">Habis</span>
                                    @elseif ($p->stock <= $limit)
                                        <span class="ml-1 px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800">Menipis</span>
                                    @else
                                        <span class="ml-1 px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">Aman</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.stock.adjust', $p) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="amount" min="1" placeholder="Jumlah" required class="w-24 border border-gray-300 rounded px-2 py-1.5 text-sm">
                                        <input type="text" name="note" maxlength="80" placeholder="Catatan (opsional)" class="w-40 border border-gray-300 rounded px-2 py-1.5 text-sm">
                                        <button name="action" value="tambah" class="px-3 py-1.5 text-xs rounded bg-[#165823] text-white hover:bg-[#12481c] cursor-pointer">Tambah</button>
                                        <button name="action" value="kurangi" class="px-3 py-1.5 text-xs rounded border border-red-600 text-red-600 hover:bg-red-50 cursor-pointer">Kurangi</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Menu tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="m-4">{{ $products->appends(request()->query())->links() }}</div>
        </div>

        {{-- Riwayat stok --}}
        <div class="bg-white rounded shadow">
            <h3 class="font-bold p-4 border-b">Riwayat perubahan stok terbaru</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr><th class="px-6 py-3">Waktu</th><th class="px-6 py-3">Menu</th><th class="px-6 py-3 text-right">Perubahan</th><th class="px-6 py-3 text-right">Stok akhir</th><th class="px-6 py-3">Keterangan</th><th class="px-6 py-3">Oleh</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($logs as $l)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3">{{ $l->product?->name }}</td>
                                <td class="px-6 py-3 text-right font-bold {{ $l->change > 0 ? 'text-green-700' : 'text-red-600' }}">{{ $l->change > 0 ? '+' : '' }}{{ $l->change }}</td>
                                <td class="px-6 py-3 text-right">{{ $l->stock_after }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $l->reason }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $l->user?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-center text-gray-500">Belum ada perubahan stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
