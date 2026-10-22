<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'sayabantu') }}</title>

    <!-- PWA & Favicon Settings -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#0284c7">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SayaBantu">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Sembunyikan scrollbar di seluruh halaman sign up / auth (Chrome, Safari, Edge, Firefox) */
        ::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        html, body, * {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        /* Sembunyikan tombol mata bawaan Windows / Edge agar tidak dobel */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-100 min-h-screen text-gray-800">
    <!-- Centered Mobile Frame container matching modern mockup -->
    <div class="max-w-md mx-auto min-h-screen min-h-[100dvh] flex flex-col bg-white shadow-xl sm:border-x sm:border-gray-200 relative">
        @php
            $currentRoute = request()->route() ? request()->route()->getName() : '';
            $isModernAuth = in_array($currentRoute, ['login', 'admin.login', 'password.request', 'password.reset', 'register.choose-role', 'register']);
            $backUrl = match($currentRoute) {
                'register.step4' => route('register.step3'),
                'register.step3' => route('register.step2'),
                'register.step2' => route('register.step1'),
                'register.step1' => route('register.choose-role'),
                'register.choose-role' => route('login'),
                'register' => route('register.choose-role'),
                'password.request', 'password.reset' => route('login'),
                'login', 'admin.login' => url('/'),
                default => url('/'),
            };
        @endphp

        @if($isModernAuth)
            <!-- Clean Modern Mobile Top Bar matching mockups -->
            <div class="px-5 pt-4 pb-2 flex items-center justify-between z-10">
                <a href="{{ $backUrl }}" {{ $backUrl === url('/') ? '' : 'wire:navigate' }}
                   class="w-10 h-10 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 active:scale-95 transition"
                   aria-label="Kembali">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex-1"></div>
            </div>

            <div class="px-6 pb-8 flex-1 flex flex-col">
                {{ $slot }}
            </div>
        @else
            <!-- Legacy Header with blue pattern for multi-step onboarding -->
            <div class="px-5 relative overflow-hidden flex-shrink-0 header-pattern" style="background: linear-gradient(to bottom right, #0066FF, #0052CC, #003D99); padding-top: 32px; padding-bottom: 32px;">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between text-white mb-0">
                        <div class="flex items-center">
                            <a href="{{ $backUrl }}" {{ $backUrl === url('/') ? '' : 'wire:navigate' }} aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        </div>

                        <div class="text-center flex-1">
                            <h1 class="text-lg font-bold">sayabantu</h1>
                            <p class="text-xs text-white/90 mt-0.5">Jasa Serabutan Terpercaya</p>
                        </div>

                        <div class="w-8"></div>
                    </div>
                </div>

                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
                </svg>
            </div>

            <div class="bg-white rounded-t-3xl -mt-2 px-6 pt-5 pb-6 flex-1 flex flex-col">
                {{ $slot }}
            </div>
        @endif
    </div>
    @livewireScripts
</body>

</html>