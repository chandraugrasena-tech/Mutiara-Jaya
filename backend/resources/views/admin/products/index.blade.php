<x-admin-layout :title="$title">
    <div class="shadow bg-white rounded">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 p-4">
            <div>
                <a href="{{ route('admin.products.create') }}">
                    <button class="px-4 py-1.5 text-sm rounded text-purple-600 font-semibold border border-purple-200 hover:text-white hover:bg-purple-600 hover:border-transparent cursor-pointer">Tambah</button>
                </a>
            </div>

            {{-- PENCARIAN + FILTER kategori & status --}}
            <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                <select name="category" class="border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->category_id }}" @selected((string) request('category') === (string) $c->category_id)>{{ $c->category }}</option>
                    @endforeach
                </select>
                <select name="status" class="border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
                <input type="text" name="s" value="{{ request('s') }}" placeholder="Cari menu" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <button class="rounded-md border px-4 py-2 border-gray-300 bg-gray-50 text-blue-600 hover:text-white hover:bg-blue-600 text-sm cursor-pointer">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Menu</th><th class="px-6 py-3">Kategori</th><th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3">Stok</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $p)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img class="h-10 w-10 rounded-full object-cover bg-gray-100" src="{{ $p->image_url }}" alt="">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $p->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $p->category?->category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ rp($p->price) }}</td>
                            <td class="px-6 py-4 {{ $p->stock <= 5 ? 'text-red-600 font-bold' : '' }}">{{ $p->stock }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('admin.products.edit', $p) }}" class="text-indigo-600 hover:text-indigo-900 font-medium mr-2">Edit</a>
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium cursor-pointer" onclick="return confirm('Anda Yakin ?')">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Menu tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION lengkap dengan parameter pencarian & filter --}}
        <div class="m-4">{{ $products->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
