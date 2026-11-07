<x-home-layout :title="$title">
    @php $from = request('from'); @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between border-b pb-3">
            <h2 class="text-xl font-bold text-gray-800">Anter ke mana?</h2>
            <a href="{{ $from === 'cart' ? route('cart.index') : route('home') }}" class="text-xs font-bold text-[#165823] hover:underline">
                {{ $from === 'cart' ? 'Kembali ke keranjang' : 'Kembali ke menu' }}
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Daftar alamat --}}
            <div class="space-y-2">
                @forelse ($addresses as $a)
                    <div class="p-3 rounded-xl border flex items-start gap-3 bg-white {{ $a->is_default ? 'border-[#165823] bg-green-50/60' : 'border-gray-200' }}">
                        <div class="mt-0.5 w-4 h-4 shrink-0 rounded-full border-2 flex items-center justify-center {{ $a->is_default ? 'border-[#165823]' : 'border-gray-300' }}">
                            @if ($a->is_default)<div class="w-2 h-2 rounded-full bg-[#165823]"></div>@endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-extrabold text-gray-800">{{ $a->label }} @if ($a->is_default)<span class="text-[10px] text-[#165823] font-bold">(dipilih)</span>@endif</p>
                            <p class="text-xs text-gray-600 break-words">{{ $a->detail }}</p>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 mt-2">
                                @unless ($a->is_default)
                                    <form action="{{ route('addresses.select', $a) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="from" value="{{ $from }}">
                                        <button class="text-[11px] font-bold text-[#165823] hover:underline cursor-pointer">Pilih</button>
                                    </form>
                                @endunless
                                <a href="{{ route('addresses.index', array_filter(['edit' => $a->address_id, 'from' => $from])) }}" class="text-[11px] font-bold text-blue-600 hover:underline">Ubah</a>
                                @if ($addresses->count() > 1)
                                    <form action="{{ route('addresses.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-[11px] font-bold text-red-500 hover:underline cursor-pointer">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada alamat. Tambahkan alamat pertamamu.</p>
                @endforelse
            </div>

            {{-- Form tambah / ubah --}}
            <form action="{{ $editing ? route('addresses.update', $editing) : route('addresses.store') }}" method="POST"
                  class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4 h-fit">
                @csrf
                @if ($editing) @method('PUT') @endif
                <input type="hidden" name="from" value="{{ $from }}">

                <h3 class="font-extrabold text-sm text-gray-900">{{ $editing ? 'Ubah alamat' : 'Tambah alamat baru' }}</h3>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-600 block">Simpan sebagai</label>
                    <div class="flex gap-2">
                        @foreach ($labels as $l)
                            <label class="cursor-pointer">
                                <input type="radio" name="label" value="{{ $l }}" class="peer sr-only" @checked(old('label', $editing->label ?? 'Rumah') === $l)>
                                <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold border border-gray-300 text-gray-600 peer-checked:bg-[#165823] peer-checked:text-white peer-checked:border-[#165823]">{{ $l }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="detail" class="text-xs font-bold text-gray-600 block">Alamat lengkap</label>
                    <textarea id="detail" name="detail" rows="3" placeholder="Nama jalan, nomor rumah, patokan..."
                              class="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#165823]">{{ old('detail', $editing->detail ?? '') }}</textarea>
                    @error('detail') <p class="text-[11px] text-red-500 font-semibold">{{ $message }}</p> @enderror
                </div>

                {{-- koordinat opsional dari GPS / lokasi browser --}}
                <div class="space-y-2">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $editing->latitude ?? '') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $editing->longitude ?? '') }}">
                    <button type="button" id="btn-locate" class="px-3 py-2 rounded-xl text-xs font-bold border border-[#165823] text-[#165823] hover:bg-green-50 cursor-pointer">Gunakan lokasi saya</button>
                    <p id="locate-status" class="text-[11px] text-gray-500">
                        @if (old('latitude', $editing->latitude ?? null))
                            Titik lokasi tersimpan ({{ old('latitude', $editing->latitude ?? '') }}, {{ old('longitude', $editing->longitude ?? '') }}).
                        @else
                            Opsional: titik lokasi membantu kurir menemukan alamatmu. Alamat tertulis tetap wajib diisi.
                        @endif
                    </p>
                    @error('latitude') <p class="text-[11px] text-red-500 font-semibold">{{ $message }}</p> @enderror
                </div>
                <script>
                    document.getElementById('btn-locate').addEventListener('click', function () {
                        const status = document.getElementById('locate-status');
                        if (!navigator.geolocation) { status.textContent = 'Browser ini tidak mendukung lokasi. Isi alamat secara manual.'; return; }
                        status.textContent = 'Mencari lokasi...';
                        navigator.geolocation.getCurrentPosition(function (pos) {
                            document.getElementById('latitude').value = pos.coords.latitude.toFixed(7);
                            document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
                            status.textContent = 'Titik lokasi didapat (' + pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5) + '). Klik Simpan alamat.';
                        }, function () {
                            status.textContent = 'Lokasi tidak bisa diambil (izin ditolak). Isi alamat secara manual.';
                        }, { enableHighAccuracy: true, timeout: 10000 });
                    });
                </script>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    @if ($editing)
                        <a href="{{ route('addresses.index', array_filter(['from' => $from])) }}" class="px-4 py-2 rounded-xl text-xs font-bold border border-gray-300 text-gray-600 hover:bg-gray-50">Batal</a>
                    @endif
                    <button class="bg-[#165823] text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-[#12481c] cursor-pointer">Simpan alamat</button>
                </div>
            </form>
        </div>
    </div>
</x-home-layout>
