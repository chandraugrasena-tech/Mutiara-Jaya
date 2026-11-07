{{-- pesan sukses / error yang dikirim controller lewat ->with() --}}
@if (session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-xs font-semibold rounded-xl p-3">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl p-3">
        {{ session('error') }}
    </div>
@endif
@if (session('info'))
    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold rounded-xl p-3">{{ session('info') }}</div>
@endif
@foreach (['cart', 'address', 'stock', 'status'] as $key)
    @error($key)
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl p-3">{{ $message }}</div>
    @enderror
@endforeach
