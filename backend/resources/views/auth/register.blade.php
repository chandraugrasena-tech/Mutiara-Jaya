<x-home-layout :title="$title">
    <div class="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-5">
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#165823]">Daftar akun</h1>
            <p class="text-xs text-gray-500 mt-1">Gratis dan hanya butuh satu menit.</p>
        </div>

        @if (! empty($notice))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs font-semibold rounded-xl p-3">{{ $notice }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            @foreach ([
                ['name', 'Nama lengkap', 'text', true],
                ['email', 'Email', 'email', true],
                ['phone', 'No. HP (opsional)', 'text', false],
                ['password', 'Password (min. 6 karakter)', 'password', true],
                ['password_confirmation', 'Ulangi password', 'password', true],
            ] as [$field, $label, $type, $required])
                <div>
                    <label for="{{ $field }}" class="block text-sm font-bold text-gray-700 mb-1">{{ $label }}</label>
                    <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" @if ($type !== 'password') value="{{ old($field) }}" @endif @required($required)
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none">
                    @error($field) <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <button type="submit" class="w-full bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3 rounded-xl cursor-pointer">Daftar</button>
        </form>

        <p class="text-center text-xs text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#165823] underline">Masuk</a></p>
    </div>
</x-home-layout>
