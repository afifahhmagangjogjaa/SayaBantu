<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header Register -->
            <div class="pt-2 mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-1.5 tracking-tight">Daftar Akun Baru</h2>
                <p class="text-xs text-gray-500 font-medium">
                    Masukkan email Anda untuk mendaftar sebagai <span class="font-bold text-gray-800">{{ $role === 'mitra' ? 'Mitra' : 'Customer' }}</span>
                </p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Masukkan alamat email aktif"
                        class="w-full px-4 py-3.5 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('email') border-red-500 @enderror">

                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-3">
                    <button type="submit"
                        class="w-full text-white font-bold py-3.5 px-4 rounded-full shadow-md hover:shadow-lg active:scale-98 transition text-sm tracking-wide"
                        style="background-color: #0098e7;">
                        Kirim Tautan Verifikasi →
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500 font-medium">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Masuk ke Akun</a>
            </p>
        </div>
    </div>
</x-guest-layout>