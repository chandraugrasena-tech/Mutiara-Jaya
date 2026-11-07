<x-admin-layout :title="$title">
    <div class="shadow px-6 py-4 bg-white rounded">
        {{-- enctype multipart/form-data wajib untuk upload file --}}
        <form enctype="multipart/form-data"
              action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST">
            @csrf
            @if (isset($product)) @method('PUT') @endif

            @php $field = 'mt-1 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500'; @endphp

            <div class="grid grid-cols-6 gap-6 py-4">
                <div class="col-span-6 sm:col-span-3">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama menu</label>
                    <input type="text" id="name" name="name" required placeholder="Nama menu" value="{{ old('name', $product->name ?? '') }}" class="{{ $field }}">
                    @error('name') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" class="{{ $field }} bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->category_id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $c->category_id)>{{ $c->category }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                    <input type="number" id="price" name="price" min="0" required value="{{ old('price', isset($product) ? (int) $product->price : '') }}" class="{{ $field }}">
                    @error('price') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                    <input type="number" id="stock" name="stock" min="0" required value="{{ old('stock', $product->stock ?? 100) }}" class="{{ $field }}">
                    @error('stock') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="time" class="block text-sm font-medium text-gray-700">Estimasi</label>
                    <input type="text" id="time" name="time" placeholder="10-15 min" value="{{ old('time', $product->time ?? '') }}" class="{{ $field }}">
                    @error('time') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="{{ $field }}">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="img" class="block text-sm font-medium text-gray-700">Foto (jpg/png/webp, maks. 2 MB)</label>
                    <input type="file" id="img" name="img" accept="image/*" class="{{ $field }}">
                    @error('img') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                    @if (isset($product))
                        <img src="{{ $product->image_url }}" alt="" class="mt-2 h-24 w-24 rounded-lg object-cover border">
                    @endif
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="img_url" class="block text-sm font-medium text-gray-700">atau URL gambar (opsional)</label>
                    <input type="url" id="img_url" name="img_url" placeholder="https://..." value="{{ old('img_url', isset($product) && ! $product->hasLocalImage() ? $product->img : '') }}" class="{{ $field }}">
                    @error('img_url') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                    <p class="text-xs text-gray-500 mt-1">Kalau file dan URL diisi, file upload yang dipakai.</p>
                </div>

                <div class="col-span-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $product->is_active ?? true))>
                        Tampilkan di toko (aktif)
                    </label>
                </div>
            </div>

            <hr>
            <div class="pt-4 text-right">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">Batal</a>
                <button type="submit" class="inline-flex justify-center w-24 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer">Save</button>
            </div>
        </form>
    </div>
</x-admin-layout>
