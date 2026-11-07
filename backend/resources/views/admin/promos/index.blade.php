<x-admin-layout :title="$title">
    <div class="shadow bg-white rounded">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-4">
            <div>
                <a href="{{ route('admin.promos.create') }}">
                    <button class="px-4 py-1.5 text-sm rounded text-purple-600 font-semibold border border-purple-200 hover:text-white hover:bg-purple-600 hover:border-transparent cursor-pointer">Tambah</button>
                </a>
            </div>
            <form action="{{ route('admin.promos.index') }}" method="GET" class="flex md:justify-end">
                <input type="text" name="s" value="{{ request('s') }}" placeholder="Cari kode / judul" class="px-4 py-2 rounded-none rounded-l-md sm:text-sm border border-gray-300">
                <button class="rounded-r-md border border-l-0 px-4 py-1 border-gray-300 bg-gray-50 text-blue-600 hover:text-white hover:bg-blue-600 cursor-pointer">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Kode</th><th class="px-6 py-3">Promo</th><th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Min. belanja</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($promos as $p)
                        <tr>
                            <td class="px-6 py-4 font-mono font-bold">{{ $p->code }}</td>
                            <td class="px-6 py-4"><div class="font-medium">{{ $p->title }}</div><div class="text-xs text-gray-500">{{ $p->description }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \App\Models\Promo::TYPES[$p->type] }}
                                <div class="text-xs text-gray-500">{{ $p->type === 'persen' ? $p->value.'%'.($p->max_discount ? ' (maks. '.rp($p->max_discount).')' : '') : rp($p->value) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ rp($p->min_order) }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.promos.destroy', $p) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('admin.promos.edit', $p) }}" class="text-indigo-600 hover:text-indigo-900 font-medium mr-2">Edit</a>
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium cursor-pointer" onclick="return confirm('Anda Yakin ?')">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada promo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="m-4">{{ $promos->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
