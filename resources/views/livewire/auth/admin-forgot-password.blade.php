<div class="min-h-screen flex">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>

    <!-- Left Side - Branding -->
    <div
        class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-500 to-primary-700 items-center justify-center p-12">
        <div class="max-w-md text-white">
            <h1 class="text-5xl font-bold mb-6">sayabantu</h1>
            <p class="text-xl mb-8 text-primary-100">Admin Panel</p>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="font-semibold">Kelola Platform</p>
                        <p class="text-sm text-primary-100">Kontrol penuh terhadap semua aspek platform</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="font-semibold">Moderasi Konten</p>
                        <p class="text-sm text-primary-100">Verifikasi dan kelola permintaan bantuan</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <p class="font-semibold">Laporan & Analitik</p>
                        <p class="text-sm text-primary-100">Dashboard lengkap dengan statistik real-time</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-md">
            <!-- Logo for Mobile -->
            <div class="lg:hidden text-center mb-8">
                <h1 class="text-3xl font-bold text-primary-600">sayabantu</h1>
                <p class="text-gray-600 mt-2">Admin Panel</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Lupa Password Admin</h2>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        Masukkan email akun administrator Anda untuk menerima tautan pemulihan kata sandi.
                    </p>
                </div>

                <!-- Session Status / Alert -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-start gap-3 shadow-2xs">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="leading-relaxed flex-1">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <form wire:submit="sendPasswordResetLink" class="space-y-6">
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email Administrator</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                            <input id="email" type="email" wire:model="email" required autofocus
                                class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-sm"
                                placeholder="admin@sayabantu.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-md transition duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60">
                        <span wire:loading.remove wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Kirim Link Reset Password</span>
                        </span>
                        <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengirim Link...</span>
                        </span>
                    </button>

                    <!-- Back to Login Link -->
                    <div class="text-center pt-2">
                        <a href="{{ route('admin.login') }}" wire:navigate
                            class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali ke Halaman Login</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-gray-500 mt-8">&copy; {{ date('Y') }} sayabantu. All rights reserved.</p>
        </div>
    </div>
</div>
