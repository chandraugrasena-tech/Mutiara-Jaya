<x-admin-layout :title="$title">
    <div class="shadow px-6 py-4 bg-white rounded max-w-xl">
        {{-- isset($category) => mode edit (method PUT), kalau tidak => tambah baru --}}
        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            @if (isset($category)) @method('PUT') @endif

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Nama kategori</label>
                <input type="text" id="category" name="category" required value="{{ old('category', $category->category ?? '') }}" placeholder="Contoh: Makanan Utama"
                       class="mt-1 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('category') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="text-right border-t pt-4">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">Batal</a>
                <button type="submit" class="inline-flex justify-center w-24 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer">Save</button>
            </div>
        </form>
    </div>
</x-admin-layout>
