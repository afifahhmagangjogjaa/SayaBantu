<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>sayabantu.com - Jasa Serabutan, Mudah, Cepat, Terpercaya</title>
    <meta name="description" content="sayabantu.com - Jasa Serabutan Mudah, Cepat, dan Terpercaya untuk pekerjaan rumah tangga, kebersihan, perbaikan, dan lainnya.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen text-gray-800 antialiased" x-data="{ role: '{{ request()->query('role', 'customer') }}' }">
    <!-- Centered Mobile App Container matching Mockup -->
    <div class="max-w-md mx-auto min-h-screen min-h-[100dvh] flex flex-col justify-between bg-white shadow-xl sm:border-x sm:border-gray-200 relative px-6 py-6 overflow-hidden">
        
        <!-- Top Switcher: Customer vs Mitra preview -->
        <div class="flex items-center justify-between pb-2">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tampilan</span>
            <div class="flex p-1 bg-slate-100 rounded-full text-xs font-semibold gap-1">
                <button type="button" @click="role = 'customer'"
                        :class="role === 'customer' ? 'bg-white text-[#0066FF] shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                        class="px-3 py-1 rounded-full transition flex items-center gap-1">
                    <span>👤</span> Customer
                </button>
                <button type="button" @click="role = 'mitra'"
                        :class="role === 'mitra' ? 'bg-white text-[#0066FF] shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                        class="px-3 py-1 rounded-full transition flex items-center gap-1">
                    <span>🤝</span> Mitra
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col justify-center items-center text-center my-auto py-4">
            
            <!-- Logo sayabantu.com with waving hand -->
            <div class="mb-2">
                <x-sayabantu-logo size="xl" :showTagline="true" tagline="Jasa Serabutan, Mudah, Cepat, Terpercaya" />
            </div>

            <!-- Mascot Illustration matching mockup -->
            <div class="my-5 w-full max-w-[280px] aspect-square rounded-3xl overflow-hidden bg-blue-50/60 p-2 shadow-inner border border-blue-100/80 flex items-center justify-center relative">
                <img src="{{ asset('images/sayabantu-mascot.jpg') }}" alt="Mascot sayabantu.com" class="w-full h-full object-cover rounded-2xl transform hover:scale-102 transition duration-300">
            </div>

            <!-- Customer View Badges (Gambar 2: Tampilan Sebelum Login) -->
            <div x-show="role === 'customer'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" class="w-full">
                <!-- 3 Feature Badges in boxes with icons -->
                <div class="grid grid-cols-3 gap-2 w-full my-4">
                    <div class="p-2.5 bg-blue-50/70 border border-blue-100 rounded-2xl flex flex-col items-center justify-center text-center shadow-2xs">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-[#0066FF] flex items-center justify-center mb-1 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-800 leading-tight">Aman & Terpercaya</span>
                    </div>

                    <div class="p-2.5 bg-blue-50/70 border border-blue-100 rounded-2xl flex flex-col items-center justify-center text-center shadow-2xs">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-[#0066FF] flex items-center justify-center mb-1 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-800 leading-tight">Proses Cepat</span>
                    </div>

                    <div class="p-2.5 bg-blue-50/70 border border-blue-100 rounded-2xl flex flex-col items-center justify-center text-center shadow-2xs">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-[#0066FF] flex items-center justify-center mb-1 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-800 leading-tight">Harga Bersaing</span>
                    </div>
                </div>

                <!-- Customer Action Buttons -->
                <div class="space-y-3 w-full mt-4">
                    <a href="{{ route('login', ['role' => 'customer']) }}"
                       class="block w-full py-3.5 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition text-sm">
                        Mulai Sekarang
                    </a>

                    <div class="pt-1">
                        <p class="text-xs text-gray-500">
                            Sudah punya akun?
                            <a href="{{ route('login', ['role' => 'customer']) }}" class="font-bold text-[#0066FF] hover:underline">
                                Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mitra View Buttons (Gambar 1: Tampilan Sebelum Login) -->
            <div x-show="role === 'mitra'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" class="w-full">
                <!-- Mitra Benefits Summary -->
                <p class="text-xs text-gray-600 mb-4 px-4 font-medium">
                    Daftar jadi mitra, mulai terima order di sekitar Anda, dan tambah penghasilan setiap hari!
                </p>

                <!-- Mitra Action Buttons -->
                <div class="space-y-2.5 w-full mt-2">
                    <a href="{{ route('login', ['role' => 'mitra']) }}"
                       class="block w-full py-3.5 px-4 bg-[#0066FF] hover:bg-[#0052CC] active:scale-[0.99] text-white font-bold rounded-xl shadow-md hover:shadow-lg transition text-sm">
                        Masuk
                    </a>

                    <a href="{{ route('register.choose-role') }}"
                       class="block w-full py-3 px-4 bg-white border border-gray-300 hover:bg-gray-50 active:scale-[0.99] text-gray-700 font-bold rounded-xl transition text-sm">
                        Daftar
                    </a>

                    <div class="pt-1">
                        <p class="text-xs text-gray-500">
                            Sudah punya akun?
                            <a href="{{ route('login', ['role' => 'mitra']) }}" class="font-bold text-[#0066FF] hover:underline">
                                Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Info -->
        <div class="pt-2 text-center border-t border-slate-100">
            <span class="text-[10px] text-gray-400">sayabantu.com • Solusi Jasa Serabutan Terpercaya</span>
        </div>

    </div>
</body>

</html>