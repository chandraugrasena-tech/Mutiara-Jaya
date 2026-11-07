<x-admin-layout :title="$title">
    <div class="shadow px-6 py-4 bg-white rounded max-w-3xl">
        <form action="{{ isset($promo) ? route('admin.promos.update', $promo) : route('admin.promos.store') }}" method="POST">
            @csrf
            @if (isset($promo)) @method('PUT') @endif

            @php $field = 'mt-1 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500'; @endphp

            <div class="grid grid-cols-6 gap-6 py-4">
                <div class="col-span-6 sm:col-span-2">
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode promo</label>
                    <input type="text" id="code" name="code" required placeholder="HEMAT10" value="{{ old('code', $promo->code ?? '') }}" class="{{ $field }} uppercase">
                    @error('code') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $promo->title ?? '') }}" class="{{ $field }}">
                    @error('title') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <input type="text" id="description" name="description" value="{{ old('description', $promo->description ?? '') }}" class="{{ $field }}">
                    @error('description') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
                    <select id="type" name="type" class="{{ $field }} bg-white">
                        @foreach (\App\Models\Promo::TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $promo->type ?? 'diskon') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label for="value" class="block text-sm font-medium text-gray-700">Nilai (Rp, atau % untuk persen)</label>
                    <input type="number" id="value" name="value" min="1" required value="{{ old('value', $promo->value ?? '') }}" class="{{ $field }}">
                    @error('value') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <label for="min_order" class="block text-sm font-medium text-gray-700">Min. belanja (Rp)</label>
                    <input type="number" id="min_order" name="min_order" min="0" required value="{{ old('min_order', $promo->min_order ?? 0) }}" class="{{ $field }}">
                    @error('min_order') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <label for="max_discount" class="block text-sm font-medium text-gray-700">Maks. diskon (khusus persen)</label>
                    <input type="number" id="max_discount" name="max_discount" min="0" value="{{ old('max_discount', $promo->max_discount ?? '') }}" class="{{ $field }}">
                    @error('max_discount') <div class="text-xs text-red-800 mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-span-6 sm:col-span-3 flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $promo->is_active ?? true))> Promo aktif
                    </label>
                </div>
            </div>

            <hr>
            <div class="pt-4 text-right">
                <a href="{{ route('admin.promos.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">Batal</a>
                <button type="submit" class="inline-flex justify-center w-24 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer">Save</button>
            </div>
        </form>
    </div>
</x-admin-layout>
