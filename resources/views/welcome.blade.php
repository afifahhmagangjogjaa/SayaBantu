<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>sayabantu - Platform Bantuan Sosial</title>
    <meta name="description" content="Temukan Bantuan Yang Tersedia. Platform yang menghubungkan mereka yang membutuhkan bantuan dengan para relawan dan mitra profesional.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f7f9fa;
        }

        .blur-text {
            filter: blur(4px);
            user-select: none;
            -webkit-user-select: none;
            transition: filter 0.3s ease;
        }

        .job-item {
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .job-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .job-item:active {
            transform: scale(0.98);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
</head>

<body class="antialiased text-gray-900 selection:bg-blue-500 selection:text-white">
    <div class="min-h-screen flex flex-col justify-between max-w-sm mx-auto bg-white shadow-xl sm:rounded-3xl my-0 sm:my-6 overflow-hidden border border-gray-100">

        <!-- Top Header Bar Sesuai Mockup: BANTUAN / Temukan Bantuan Yang Tersedia -->
        <header class="pt-8 pb-5 px-6 text-center bg-gray-100/90 border-b border-gray-200/60">
            <h1 class="text-2xl font-black tracking-wide text-gray-900 uppercase">
                BANTUAN
            </h1>
            <p class="text-sm text-gray-600 font-medium mt-1">
                Temukan Bantuan Yang Tersedia
            </p>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-5 py-5 flex flex-col justify-between">

            <!-- Hero Image / Banner Sesuai Mockup -->
            <div class="mb-5 fade-in">
                <div class="w-full h-44 rounded-2xl bg-gradient-to-br from-blue-100 via-sky-50 to-indigo-100 border border-blue-200/70 overflow-hidden relative flex flex-col items-center justify-center p-4 shadow-inner group">
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center text-3xl mb-2 group-hover:scale-105 transition-transform">
                        🤝
                    </div>
                    <span class="text-xs font-bold text-blue-900 tracking-wide uppercase">Gotong Royong &amp; Kepedulian</span>
                    <span class="text-[11px] text-blue-700/80 text-center mt-0.5">Saling membantu untuk sesama di seluruh Indonesia</span>

                    <!-- Decorative elements -->
                    <div class="absolute -top-6 -right-6 w-20 h-20 bg-blue-400/10 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-indigo-400/10 rounded-full blur-xl"></div>
                </div>
            </div>

            <!-- Pekerjaan yang tersedia Label -->
            <div class="mb-2 fade-in">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">
                    Pekerjaan yang tersedia:
                </h2>
            </div>

            <!-- Gray Container with Blurred Job List Sesuai Mockup -->
            <div class="bg-gray-200/80 rounded-2xl p-3.5 mb-6 fade-in space-y-2.5 shadow-inner">
                
                <!-- Job Item 1 -->
                <div onclick="window.location='{{ route('login') }}'" 
                     class="job-item bg-white rounded-xl py-3 px-4 shadow-xs flex items-center justify-between border border-gray-100 group">
                    <span class="text-sm font-semibold text-gray-800 blur-text">
                        Membersihkan rumput
                    </span>
                    <span class="text-xs text-blue-500 font-bold opacity-70 group-hover:opacity-100 transition-opacity">
                        🔒 Login
                    </span>
                </div>

                <!-- Job Item 2 -->
                <div onclick="window.location='{{ route('login') }}'" 
                     class="job-item bg-white rounded-xl py-3 px-4 shadow-xs flex items-center justify-between border border-gray-100 group">
                    <span class="text-sm font-semibold text-gray-800 blur-text">
                        Membantu angkut pindahan rumah
                    </span>
                    <span class="text-xs text-blue-500 font-bold opacity-70 group-hover:opacity-100 transition-opacity">
                        🔒 Login
                    </span>
                </div>

                <!-- Job Item 3 -->
                <div onclick="window.location='{{ route('login') }}'" 
                     class="job-item bg-white rounded-xl py-3 px-4 shadow-xs flex items-center justify-between border border-gray-100 group">
                    <span class="text-sm font-semibold text-gray-800 blur-text">
                        Membersihkan saluran air
                    </span>
                    <span class="text-xs text-blue-500 font-bold opacity-70 group-hover:opacity-100 transition-opacity">
                        🔒 Login
                    </span>
                </div>

                <!-- Job Item 4 -->
                <div onclick="window.location='{{ route('login') }}'" 
                     class="job-item bg-white rounded-xl py-3 px-4 shadow-xs flex items-center justify-between border border-gray-100 group">
                    <span class="text-sm font-semibold text-gray-800 blur-text">
                        Perbaikan genteng &amp; atap rumah
                    </span>
                    <span class="text-xs text-blue-500 font-bold opacity-70 group-hover:opacity-100 transition-opacity">
                        🔒 Login
                    </span>
                </div>

            </div>

            <!-- Bottom CTA Buttons Sesuai Mockup -->
            <div class="mt-auto pt-2 pb-4 fade-in">
                <!-- Tombol Mulai Berbagi Kebaikan -->
                <a href="{{ route('login') }}"
                   class="block w-full py-4 px-6 rounded-2xl bg-gray-300 hover:bg-gray-400/90 text-gray-900 font-bold text-center text-base tracking-wide transition active:scale-98 shadow-sm">
                    Mulai Berbagi Kebaikan
                </a>

                <!-- Sub link: Belum punya akun? masuk sekarang -->
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-xs font-medium text-gray-700 hover:text-blue-600 transition">
                        Belum punya akun? <span class="font-bold underline">masuk sekarang</span>
                    </a>
                </div>
            </div>

        </main>

    </div>
</body>

</html>