<x-home-layout :title="$title">
    <div class="max-w-md mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-5">
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#165823]">Masuk ke Mutiara Jaya</h1>
            <p class="text-xs text-gray-500 mt-1">Masuk untuk memesan dan memantau pesananmu.</p>
        </div>

        @if (! empty($notice))
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs font-semibold rounded-xl p-3">{{ $notice }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-[#165823] focus:ring-1 focus:ring-[#165823] outline-none">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
            </label>
            <button type="submit" class="w-full bg-[#165823] hover:bg-[#12481c] text-white font-bold py-3 rounded-xl cursor-pointer">Masuk</button>
        </form>

        <p class="text-center text-xs text-gray-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#165823] underline">Daftar</a></p>
    </div>
</x-home-layout>
