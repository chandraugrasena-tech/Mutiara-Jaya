<x-admin-layout :title="$title">
    <div class="shadow bg-white rounded">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-4">
            <div>
                <a href="{{ route('admin.categories.create') }}">
                    <button class="px-4 py-1.5 text-sm rounded text-purple-600 font-semibold border border-purple-200 hover:text-white hover:bg-purple-600 hover:border-transparent cursor-pointer">Tambah</button>
                </a>
            </div>
            <form action="{{ route('admin.categories.index') }}" method="GET" class="flex md:justify-end">
                <input type="text" name="s" value="{{ request('s') }}" placeholder="Cari kategori"
                       class="px-4 py-2 rounded-none rounded-l-md sm:text-sm border border-gray-300">
                <button class="rounded-r-md border border-l-0 px-4 py-1 border-gray-300 bg-gray-50 text-blue-600 hover:text-white hover:bg-blue-600 cursor-pointer">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">ID</th><th class="px-6 py-3">Kategori</th><th class="px-6 py-3">Jumlah Menu</th><th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($categories as $c)
                        <tr>
                            <td class="px-6 py-4">{{ $c->category_id }}</td>
                            <td class="px-6 py-4 font-medium">{{ $c->category }}</td>
                            <td class="px-6 py-4">{{ $c->products_count }} menu</td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('admin.categories.edit', $c) }}" class="text-indigo-600 hover:text-indigo-900 font-medium mr-2">Edit</a>
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium cursor-pointer"
                                            onclick="return confirm('Hapus kategori ini? {{ $c->products_count }} menu di dalamnya ikut terhapus.')">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="m-4">{{ $categories->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
