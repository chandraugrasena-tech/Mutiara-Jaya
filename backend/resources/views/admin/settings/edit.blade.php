<x-admin-layout :title="$title">
    <form action="{{ route('admin.settings.update') }}" method="POST" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @php $field = 'mt-1 block w-full sm:text-sm border border-gray-300 rounded-md px-3 py-2'; @endphp

        <div class="bg-white rounded shadow p-6 space-y-4">
            <h3 class="font-bold">Profil toko</h3>
            <div>
                <label for="store_name" class="block text-sm font-medium text-gray-700">Nama toko</label>
                <input type="text" id="store_name" name="store_name" required value="{{ old('store_name', $values['store_name']) }}" class="{{ $field }}">
                @error('store_name') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="store_tagline" class="block text-sm font-medium text-gray-700">Slogan</label>
                <input type="text" id="store_tagline" name="store_tagline" value="{{ old('store_tagline', $values['store_tagline']) }}" class="{{ $field }}">
                @error('store_tagline') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="store_description" class="block text-sm font-medium text-gray-700">Deskripsi toko</label>
                <textarea id="store_description" name="store_description" rows="3" class="{{ $field }}">{{ old('store_description', $values['store_description']) }}</textarea>
                @error('store_description') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="bg-white rounded shadow p-6 space-y-4">
            <h3 class="font-bold">Alamat dan kontak</h3>
            <div>
                <label for="store_address" class="block text-sm font-medium text-gray-700">Alamat toko</label>
                <input type="text" id="store_address" name="store_address" value="{{ old('store_address', $values['store_address']) }}" class="{{ $field }}">
                @error('store_address') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="store_hours" class="block text-sm font-medium text-gray-700">Jam buka</label>
                <input type="text" id="store_hours" name="store_hours" placeholder="Contoh: Setiap hari, 08.00 - 21.00 WITA" value="{{ old('store_hours', $values['store_hours']) }}" class="{{ $field }}">
                @error('store_hours') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="store_phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                    <input type="text" id="store_phone" name="store_phone" value="{{ old('store_phone', $values['store_phone']) }}" class="{{ $field }}">
                    @error('store_phone') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="store_whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp (angka saja)</label>
                    <input type="text" id="store_whatsapp" name="store_whatsapp" value="{{ old('store_whatsapp', $values['store_whatsapp']) }}" class="{{ $field }}">
                    @error('store_whatsapp') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="store_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="store_email" name="store_email" value="{{ old('store_email', $values['store_email']) }}" class="{{ $field }}">
                    @error('store_email') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow p-6 space-y-4">
            <h3 class="font-bold">Pengantaran dan stok</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="delivery_area" class="block text-sm font-medium text-gray-700">Area antar</label>
                    <input type="text" id="delivery_area" name="delivery_area" value="{{ old('delivery_area', $values['delivery_area']) }}" class="{{ $field }}">
                    @error('delivery_area') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700">Ongkos kirim (Rp)</label>
                    <input type="number" id="delivery_fee" name="delivery_fee" min="0" required value="{{ old('delivery_fee', $values['delivery_fee']) }}" class="{{ $field }}">
                    @error('delivery_fee') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="low_stock_limit" class="block text-sm font-medium text-gray-700">Batas stok menipis</label>
                    <input type="number" id="low_stock_limit" name="low_stock_limit" min="0" required value="{{ old('low_stock_limit', $values['low_stock_limit']) }}" class="{{ $field }}">
                    @error('low_stock_limit') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            <p class="text-xs text-gray-500">Ongkos kirim dipakai untuk semua pesanan baru. Menu dengan stok sama atau di bawah batas ditandai menipis.</p>
        </div>

        <div class="text-right">
            <button type="submit" class="inline-flex justify-center py-2 px-6 text-sm font-medium rounded-md bg-[#165823] hover:bg-[#12481c] text-white cursor-pointer">Simpan</button>
        </div>
    </form>
</x-admin-layout>
