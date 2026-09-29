<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mastulongmas') }} - Mitra</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        
        /* Sembunyikan tombol mata bawaan Windows / Edge agar tidak dobel */
        input::-ms-reveal,
        input::-ms-clear,
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
        }
        
        /* Bottom Navigation Animations */
        @keyframes slideUp {
            from {
                transform: translate(-50%, 100%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0);
                opacity: 0.6;
            }
            100% {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes bounce-in {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        #bottom-nav {
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(0, 152, 231, 0.15);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .nav-item:active::before {
            width: 60px;
            height: 60px;
        }

        .nav-item:hover {
            transform: translateY(-3px);
        }

        .nav-item:active {
            transform: translateY(-1px) scale(0.95);
        }

        .nav-item svg {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-item:hover svg {
            transform: scale(1.15);
        }

        .nav-item:active svg {
            transform: scale(0.9);
        }

        .nav-item.active svg {
            animation: bounce-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-item .nav-label {
            transition: all 0.2s ease;
        }

        .nav-item:hover .nav-label {
            transform: scale(1.05);
        }

        .nav-fab {
            position: relative;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-fab::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #0098e7 0%, #0077cc 100%);
            transform: translate(-50%, -50%);
            z-index: -1;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-fab:hover {
            transform: translateY(-4px) rotate(90deg);
        }

        .nav-fab:hover::after {
            transform: translate(-50%, -50%) scale(1.2);
            box-shadow: 0 8px 20px rgba(0, 152, 231, 0.4);
        }

        .nav-fab:active {
            transform: translateY(-2px) rotate(90deg) scale(0.9);
        }

        .nav-fab svg {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-fab:hover svg {
            transform: rotate(-90deg);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-2px);
            }
        }

        .nav-item.active {
            animation: float 2s ease-in-out infinite;
        }

        /* Indicator dot for active state */
        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: currentColor;
            animation: bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Centered Container -->
    <div class="min-h-screen flex items-start justify-center bg-gray-100">
        <!-- Mobile Width Container -->
        <div class="w-full max-w-md bg-gray-50 relative shadow-2xl">
            <!-- Floating Top Flash Notification Pop-Up (Instant Render) -->
            @if (session()->has('message') || session()->has('success') || session()->has('error') || session()->has('status'))
                <div id="flash-toast"
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => { show = false; }, 3000)"
                    class="fixed top-4 left-1/2 -translate-x-1/2 z-[999999] max-w-sm w-[92%] pointer-events-auto transition-all duration-300 transform">
                    @if (session()->has('error'))
                        <div class="bg-white border border-red-200 text-gray-900 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-red-900">Perhatian</div>
                                <div class="text-[11px] text-gray-600 leading-snug">{{ session('error') }}</div>
                            </div>
                            <button @click="show = false; document.getElementById('flash-toast')?.remove();" class="text-gray-400 hover:text-gray-600 p-1 text-xs">✕</button>
                        </div>
                    @else
                        <div class="bg-white border border-emerald-200 text-gray-900 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-emerald-900">Berhasil</div>
                                <div class="text-[11px] text-gray-600 leading-snug">{{ session('message') ?? session('success') ?? session('status') }}</div>
                            </div>
                            <button @click="show = false; document.getElementById('flash-toast')?.remove();" class="text-gray-400 hover:text-gray-600 p-1 text-xs">✕</button>
                        </div>
                    @endif
                </div>
                <script>
                    setTimeout(function() {
                        var toast = document.getElementById('flash-toast');
                        if (toast) {
                            toast.style.opacity = '0';
                            toast.style.transition = 'opacity 0.4s ease';
                            setTimeout(function() { toast.remove(); }, 400);
                        }
                    }, 3000);
                </script>
            @endif

            <!-- Global notification (toast) for mitra actions -->
            <div id="mitra-global-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 pointer-events-none" style="max-width:448px; width:100vw; z-index:99999;">
                <div id="mitra-global-notification-inner" class="mx-auto max-w-md"></div>
            </div>
            <!-- Content -->
            <div class="flex flex-col min-h-screen">
                <!-- Livewire -->
                <div class="flex-1 pb-20">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </div>

                <!-- Bottom Navigation Bar -->
                <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 bg-white border-t border-gray-200 shadow-2xl z-50"
                    style="max-width: 448px; width: 100vw;">
                    <div class="max-w-md mx-auto flex items-center justify-around px-4 py-2.5">
                        <a href="{{ route('mitra.dashboard') }}"
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('mitra.dashboard') && !request()->has('tab') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-xs font-bold mt-0.5">Beranda</span>
                        </a>
                        <a href="{{ route('mitra.helps.all') }}"
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('mitra.helps.all') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="text-xs font-bold mt-0.5">Cari</span>
                        </a>
                        @php
                            $activeMitraJobs = 0;
                            $unreadMitraChats = 0;
                            if (auth()->check()) {
                                try {
                                    $activeMitraJobs = \App\Models\Help::where('mitra_id', auth()->id())
                                        ->whereIn('status', [
                                            'memperoleh_mitra',
                                            'taken',
                                            'partner_on_the_way',
                                            'partner_arrived',
                                            'in_progress',
                                            'sedang_diproses',
                                            'partner_cancel_requested',
                                            'diproses_mitra',
                                            'waiting_customer_confirmation'
                                        ])->count();
                                } catch (\Throwable $e) {}

                                try {
                                    $unreadMitraChats = \App\Models\Chat::where('mitra_id', auth()->id())
                                        ->whereNull('read_at')
                                        ->where('sender_type', 'customer')
                                        ->count();
                                } catch (\Throwable $e) {}
                            }
                        @endphp

                        <a href="{{ route('mitra.helps.processing') }}"
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('mitra.helps.processing') || request()->routeIs('mitra.helps.completed') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                @if($activeMitraJobs > 0)
                                    <span class="absolute bottom-0.5 -right-0.5 flex h-2 w-2 items-center justify-center pointer-events-none">
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 ring-1.5 ring-white"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-bold mt-0.5">Pekerjaan</span>
                        </a>

                        <a href="{{ route('mitra.chat') }}"
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('mitra.chat*') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                @if($unreadMitraChats > 0)
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 items-center justify-center pointer-events-none">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 ring-1.5 ring-white"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-bold mt-0.5">Chat</span>
                        </a>

                        @php
                            $u = auth()->user();
                            $needsMitraAttention = $u && (
                                !$u->verified || 
                                !empty($u->getMissingBiodataFields()) || 
                                !$u->hasVerifiedEmail()
                            );
                        @endphp
                        <a href="{{ route('mitra.profile') }}"
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('mitra.profile') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                @if($needsMitraAttention)
                                    <span class="absolute bottom-0.5 -right-0.5 flex h-2 w-2 items-center justify-center pointer-events-none">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 ring-1.5 ring-white"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-bold mt-0.5">Profil</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @include('partials.help-modal')

    {{-- Realtime notifications poll component (invisible) --}}
    @livewire('mitra.realtime-notifications')

    <script>
        function showMitraNotification({ title = 'Notifikasi', message = '', url = '#' , timeout = 3000, type = 'success' }) {
            try {
                console.log('showMitraNotification (text-only) called', { title, message, url, timeout, type });
                const container = document.getElementById('mitra-global-notification-inner');
                if (!container) { console.warn('mitra-global-notification-inner not found'); return; }
                container.innerHTML = '';

                const wrap = document.createElement('div');
                wrap.className = 'bg-white rounded-xl shadow-xl border border-gray-100 p-3 max-w-md mx-3 pointer-events-auto transition transform duration-300';
                wrap.style.boxShadow = '0 10px 30px rgba(2,6,23,0.08)';

                // Text-only body (no icons)
                const body = document.createElement('div');
                body.className = 'min-w-0';
                const titleEl = document.createElement('div');
                titleEl.className = 'text-sm font-semibold text-gray-900';
                titleEl.innerText = String(title || 'Notifikasi');

                const msgEl = document.createElement('div');
                msgEl.className = 'text-xs text-gray-600 mt-0.5';
                msgEl.innerText = String(message || '');

                body.appendChild(titleEl);
                if ((message || '').toString().trim() !== '') body.appendChild(msgEl);

                wrap.appendChild(body);

                wrap.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                    container.innerHTML = '';
                });

                container.appendChild(wrap);

                // Play audio chime if available
                if (typeof window.playNotifChime === 'function') {
                    window.playNotifChime();
                }

                const effectiveTimeout = (type === 'error' || type === 'warning' || type === 'danger') ? Math.max(timeout, 8000) : timeout;
                setTimeout(() => { container.innerHTML = ''; }, effectiveTimeout);
            } catch (err) { console.error('showMitraNotification error', err); }
        }

        function escapeHtml(unsafe) {
            return String(unsafe).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        const mitraHelpDetailTemplate = "{{ route('mitra.helps.detail', ['id' => 'REPLACE_ID']) }}";
        const mitraChatRoute = "{{ route('mitra.chat') }}";

        window.addEventListener('help-taken', function (e) {
            const helpId = e && e.detail && e.detail.helpId ? e.detail.helpId : null;
            const url = helpId ? mitraHelpDetailTemplate.replace('REPLACE_ID', helpId) : mitraHelpDetailTemplate.replace('REPLACE_ID', '');
            showMitraNotification({ title: 'Bantuan Diambil', message: 'Anda berhasil mengambil bantuan. Ketuk untuk melihat detail.', url });
        });

        window.addEventListener('message-sent', function (e) {
            const helpId = e && e.detail && e.detail.helpId ? e.detail.helpId : null;
            const url = helpId ? mitraChatRoute.replace(/\/?$/, '') + '/' + encodeURIComponent(helpId) : mitraChatRoute;
            showMitraNotification({ title: 'Pesan Terkirim', message: 'Pesan berhasil dikirim. Ketuk untuk membuka chat.', url });
        });

        window.addEventListener('help-new-message', function (e) {
            console.log('help-new-message received', e && e.detail ? e.detail : e);
            const helpId = e && e.detail && e.detail.helpId ? e.detail.helpId : null;
            const from = e && e.detail && e.detail.from ? e.detail.from : 'Customer';
            const message = e && e.detail && e.detail.message ? e.detail.message : '';
            const url = helpId ? mitraChatRoute.replace(/\/?$/, '') + '/' + encodeURIComponent(helpId) : mitraChatRoute;
            showMitraNotification({ title: 'Pesan Baru dari ' + from, message: message || 'Ketuk untuk membuka chat.', url, timeout: 2500, type: 'message' });
        });

        window.triggerMitraNotification = function (payload) { showMitraNotification(payload || {}); }
    </script>

    <!-- Modal Notifikasi Verifikasi KTP / Identitas Ditolak -->
    @include('partials.ktp-rejected-modal')

    <!-- Modal Notifikasi Akun Diblokir / Dinonaktifkan -->
    @php
        $isMitraAccountDisabled = auth()->check() && in_array(auth()->user()->status, ['blocked', 'inactive']);
        $isMitraInactive = auth()->check() && auth()->user()->status === 'inactive';
    @endphp
    <div id="blocked-account-modal" class="{{ $isMitraAccountDisabled ? '' : 'hidden' }}" style="{{ $isMitraAccountDisabled ? 'display: flex !important;' : '' }} position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 transform animate-bounce-in">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <h3 id="blocked-account-title" class="text-lg font-extrabold text-gray-900 mb-2">
                {{ $isMitraInactive ? 'Akun Anda Dinonaktifkan' : 'Akun Anda Telah Diblokir' }}
            </h3>
            <p id="blocked-account-message" class="text-xs text-gray-600 mb-6 leading-relaxed">
                {{ $isMitraInactive 
                    ? 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan bantuan.' 
                    : 'Akses akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan informasi lebih lanjut.' }}
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-xl text-sm shadow-lg shadow-red-200 transition-all flex items-center justify-center gap-2">
                    <span>OK, Mengerti</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
        function triggerBlockedModal(isInactive = null) {
            const modal = document.getElementById('blocked-account-modal');
            if (!modal) return;

            const updateTexts = (inactive) => {
                const title = document.getElementById('blocked-account-title');
                const msg = document.getElementById('blocked-account-message');
                if (inactive) {
                    if (title) title.innerText = 'Akun Anda Dinonaktifkan';
                    if (msg) msg.innerText = 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan bantuan.';
                } else {
                    if (title) title.innerText = 'Akun Anda Telah Diblokir';
                    if (msg) msg.innerText = 'Akses akun Anda telah diblokir oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan informasi lebih lanjut.';
                }
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            };

            if (isInactive === true || isInactive === false) {
                updateTexts(isInactive);
            } else {
                fetch("{{ route('account.status.check') }}", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    const inactive = !!(data && (data.status === 'inactive' || data.is_inactive));
                    updateTexts(inactive);
                })
                .catch(() => {
                    updateTexts(false);
                });
            }
        }

        // Check account blocked status in real-time
        function checkAccountStatus() {
            fetch("{{ route('account.status.check') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (res.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (data && (data.should_logout || data.is_blocked || data.is_inactive || data.status === 'blocked' || data.status === 'inactive')) {
                    const isInactive = (data.status === 'inactive' || data.is_inactive);
                    triggerBlockedModal(isInactive);
                }
            })
            .catch(() => {});
        }

        // Check immediately on load, on focus, on click, and every 2.5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            @if(auth()->check() && in_array(auth()->user()->status, ['blocked', 'inactive']))
                triggerBlockedModal({{ auth()->user()->status === 'inactive' ? 'true' : 'false' }});
            @else
                checkAccountStatus();
            @endif
        });

        setInterval(checkAccountStatus, 2500);
        window.addEventListener('focus', checkAccountStatus);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkAccountStatus();
        });

        // Intercept any Livewire request errors
        document.addEventListener('livewire:init', () => {
            if (typeof Livewire !== 'undefined' && Livewire.hook) {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, content }) => {
                        if (status === 403 || status === 401) {
                            let isInactive = null;
                            try {
                                if (content) {
                                    const parsed = typeof content === 'string' ? JSON.parse(content) : content;
                                    if (parsed && (parsed.status || parsed.is_inactive !== undefined)) {
                                        isInactive = !!(parsed.status === 'inactive' || parsed.is_inactive);
                                    }
                                }
                            } catch(e) {}
                            triggerBlockedModal(isInactive);
                        }
                    });
                });
            }

            // Real-time chat & help audio notification listener
            window.addEventListener('help-new-message', () => {
                if (typeof window.playNotifChime === 'function') {
                    window.playNotifChime();
                }
            });
            window.addEventListener('mitra-help-status', () => {
                if (typeof window.playNotifChime === 'function') {
                    window.playNotifChime();
                }
            });
        });

        window.playNotifChime = function () {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();
                const now = ctx.currentTime;
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (i * 0.08));
                    gain.gain.setValueAtTime(0.3, now + (i * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.08) + 0.3);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.08));
                    osc.stop(now + (i * 0.08) + 0.3);
                });
            } catch(e){}
        };

        window.playRingChime = function () {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();
                const now = ctx.currentTime;
                const notes = [587.33, 880, 587.33, 880];
                notes.forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (i * 0.12));
                    gain.gain.setValueAtTime(0.25, now + (i * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.12) + 0.22);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.12));
                    osc.stop(now + (i * 0.12) + 0.22);
                });
            } catch(e){}
        };

        window.triggerVibrate = function () {
            if ('vibrate' in navigator) {
                try { navigator.vibrate([150, 80, 150]); } catch(e){}
            }
        };
    </script>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
            } else {
                field.type = 'password';
            }
        }

        // Global interactive toast popup
        window.showGlobalFlashToast = function(message, type = 'success') {
            let existing = document.getElementById('flash-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'flash-toast';
            toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[999999] max-w-sm w-[92%] pointer-events-auto transition-all duration-300 transform';
            
            const isError = type === 'error';
            const borderColor = isError ? 'border-red-200' : 'border-emerald-200';
            const iconBg = isError ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600';
            const titleColor = isError ? 'text-red-900' : 'text-emerald-900';
            const title = isError ? 'Perhatian' : 'Berhasil';
            const iconSvg = isError 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />';

            toast.innerHTML = `
                <div class="bg-white border ${borderColor} text-gray-900 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in">
                    <div class="w-8 h-8 rounded-full ${iconBg} flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold ${titleColor}">${title}</div>
                        <div class="text-[11px] text-gray-600 leading-snug">${message}</div>
                    </div>
                    <button onclick="this.closest('#flash-toast').remove()" class="text-gray-400 hover:text-gray-600 p-1 text-xs">✕</button>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast && toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.4s ease';
                    setTimeout(() => toast.remove(), 400);
                }
            }, 5000);
        };

        // Asynchronous Email Verification Trigger with "Mengirim..." -> "Terkirim ✓" flow
        window.sendEmailVerification = function(btn, url) {
            if (!btn || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            const originalBg = btn.style.background;
            const originalShadow = btn.style.boxShadow;

            btn.disabled = true;
            btn.innerHTML = `
                <span class="inline-flex items-center gap-1">
                    <svg class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengirim...
                </span>
            `;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Terkirim ✓
                        </span>
                    `;
                    btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                    btn.style.boxShadow = '0 2px 6px rgba(16, 185, 129, 0.35)';

                    if (typeof window.showGlobalFlashToast === 'function') {
                        window.showGlobalFlashToast(data.message || 'Tautan verifikasi berhasil dikirim!', 'success');
                    }

                    // Countdown timer 60s
                    let remaining = 60;
                    const timer = setInterval(() => {
                        remaining--;
                        if (remaining > 0) {
                            btn.innerHTML = `
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Terkirim (${remaining}s)
                                </span>
                            `;
                        } else {
                            clearInterval(timer);
                            btn.disabled = false;
                            btn.innerHTML = 'Kirim Ulang';
                            btn.style.background = originalBg;
                            btn.style.boxShadow = originalShadow;
                        }
                    }, 1000);
                } else {
                    let errorMsg = data.message || 'Gagal mengirim email verifikasi. Silakan coba lagi.';
                    if (response.status === 429) {
                        errorMsg = 'Terlalu banyak permintaan. Mohon tunggu sebentar.';
                    }
                    if (typeof window.showGlobalFlashToast === 'function') {
                        window.showGlobalFlashToast(errorMsg, 'error');
                    }
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                console.error('Error sending verification email:', err);
                if (typeof window.showGlobalFlashToast === 'function') {
                    window.showGlobalFlashToast('Terjadi kesalahan jaringan. Silakan periksa koneksi Anda.', 'error');
                }
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        };
    </script>
    @stack('scripts')
</body>

</html>