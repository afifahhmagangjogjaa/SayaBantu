<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>sayabantu.com - Jasa Serabutan, Mudah, Cepat, Terpercaya</title>
    <meta name="description" content="Temukan jasa serabutan yang mudah, cepat, dan terpercaya di sekitar Anda.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sembunyikan scrollbar bawaan agar tampilan aplikasi mulus tanpa scroll */
        ::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        html, body {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
            height: 100%;
            overflow: hidden;
        }
    </style>
</head>

<body class="h-screen h-[100dvh] bg-slate-100 font-sans text-slate-900 antialiased flex flex-col items-center justify-center sm:p-4">
    <!-- Centered Mobile App Container matching the design mockup -->
    <main class="w-full max-w-md h-full sm:h-[840px] sm:max-h-[92vh] bg-white sm:rounded-[36px] sm:shadow-2xl sm:border sm:border-slate-200/80 flex flex-col justify-between overflow-hidden relative">
        
        <!-- Header Section (Logo + Tagline) -->
        <header class="pt-10 sm:pt-14 px-6 pb-2 text-center shrink-0 z-10">
            <x-sayabantu-logo size="lg" />
            
            <h1 class="mt-4 sm:mt-5 text-base sm:text-[17px] font-bold text-slate-900 leading-snug tracking-tight">
                Jasa Serabutan,<br>Mudah, Cepat, Terpercaya
            </h1>
        </header>

        <!-- Center Hero Mascot Section -->
        <section class="relative w-full flex-1 flex items-center justify-center overflow-hidden my-auto min-h-[180px] max-h-[300px]">
            <img src="{{ asset('images/landing-mascot.png') }}"
                 alt="Petugas sayabantu.com berkaus biru bertopi memberi jempol"
                 class="w-full h-full object-cover object-top select-none pointer-events-none">
            
            <!-- Soft White Organic Wave Divider at bottom matching mockup -->
            <svg class="pointer-events-none absolute -bottom-0.5 left-0 w-full h-8 sm:h-10 text-white fill-current" viewBox="0 0 400 40" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,28 Q200,8 400,28 L400,40 L0,40 Z"></path>
            </svg>
        </section>

        <!-- Features Badges (Aman & Terpercaya, Proses Cepat, Harga Bersaing) -->
        <section class="grid grid-cols-3 gap-2 px-6 pt-3 pb-4 text-center shrink-0">
            <!-- Feature 1: Aman & Terpercaya -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0066FF] flex items-center justify-center mb-1.5 shadow-2xs">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                </div>
                <span class="text-xs sm:text-[13px] font-bold text-gray-800 leading-tight">
                    Aman<br>&amp; Terpercaya
                </span>
            </div>

            <!-- Feature 2: Proses Cepat -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0066FF] flex items-center justify-center mb-1.5 shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <polyline points="12 7 12 12 15 14"/>
                    </svg>
                </div>
                <span class="text-xs sm:text-[13px] font-bold text-gray-800 leading-tight">
                    Proses<br>Cepat
                </span>
            </div>

            <!-- Feature 3: Harga Bersaing -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0066FF] flex items-center justify-center mb-1.5 shadow-2xs">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21.71 8.71l-3.42-3.42a2.008 2.008 0 0 0-2.83 0L13.88 6.87a.996.996 0 0 0 0 1.41l.71.71-3.59 3.59-1.42-1.42a1 1 0 0 0-1.41 0l-.71.71 2.12 2.12-1.41 1.41-2.12-2.12-.71.71a1 1 0 0 0 0 1.41l1.42 1.42-1.41 1.41a.996.996 0 0 0 0 1.41l3.42 3.42c.78.78 2.05.78 2.83 0l8.34-8.34c.78-.78.78-2.05 0-2.83zM15.29 5.29c.39-.39 1.02-.39 1.41 0l3.42 3.42c.39.39.39 1.02 0 1.41l-2.12 2.12-4.83-4.83 2.12-2.12z"/>
                    </svg>
                </div>
                <span class="text-xs sm:text-[13px] font-bold text-gray-800 leading-tight">
                    Harga<br>Bersaing
                </span>
            </div>
        </section>

        <!-- CTA & Footer Section -->
        <footer class="px-6 pb-8 sm:pb-10 pt-2 shrink-0">
            <!-- Mulai Sekarang Button -->
            <a href="{{ route('login') }}" wire:navigate
               class="w-full flex items-center justify-center py-4 sm:py-4.5 rounded-full bg-[#0066FF] hover:bg-[#0055D4] active:scale-[0.98] text-white font-bold text-base shadow-md shadow-blue-500/25 transition cursor-pointer">
                Mulai Sekarang
            </a>
        </footer>

    </main>
</body>

</html>
