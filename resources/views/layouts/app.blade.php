<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'sayabantu') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        
        /* Sembunyikan tombol mata bawaan Windows / Edge agar tidak dobel */
        input::-ms-reveal,
        input::-ms-clear,
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
        }

        /* Otomatis sembunyikan menu navigasi bawah saat modal overlay aktif agar layar tertutup penuh */
        body:has(.modal-backdrop-open) #bottom-nav {
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
            background-color: #ffffff !important;
            background: #ffffff !important;
            opacity: 1 !important;
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
            <!-- Global notification (toast) for customer actions -->
            <div id="customer-global-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 pointer-events-none" style="max-width:448px; width:100vw; z-index:99999;">
                <div id="customer-global-notification-inner" class="mx-auto max-w-md"></div>
            </div>

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
            <!-- Content -->
            <main class="pb-20">
                @if($__env->hasSection('content'))
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            {{-- Global Alert: Rekan Jasa Belum Berangkat setelah 30 Menit --}}
            @auth
                @if(in_array(auth()->user()->role, ['customer', 'kustomer', 'user']) || !auth()->user()->role)
                    <livewire:customer.idle-partner-alert />
                @endif
            @endauth

            <!-- Bottom Navigation (styled like mitra layout) -->
            @auth
                <nav id="bottom-nav" class="fixed bottom-0 left-1/2 transform -translate-x-1/2 bg-white border-t border-gray-200 shadow-2xl z-40"
                    style="max-width: 448px; width: 100vw; background-color: #ffffff !important;">
                    <div class="max-w-md mx-auto flex items-center justify-around px-4 py-2.5">
                        <a href="{{ route('customer.dashboard') }}" wire:navigate
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('customer.dashboard') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="nav-label text-xs font-bold mt-0.5">Beranda</span>
                        </a>

                        <a href="{{ route('customer.helps.index') }}" wire:navigate
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('customer.helps.*') && !request()->routeIs('customer.helps.create') && !request()->routeIs('customer.helps.history') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span class="nav-label text-xs font-bold mt-0.5">Aktivitas</span>
                        </a>

                        <a href="{{ route('customer.helps.create') }}" wire:navigate
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('customer.helps.create') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="nav-label text-xs font-bold mt-0.5">Buat</span>
                        </a>

                        @php
                            $unreadCustomerChats = 0;
                            if (auth()->check()) {
                                try {
                                    $unreadCustomerChats = \App\Models\Chat::where('customer_id', auth()->id())
                                        ->whereNull('read_at')
                                        ->where('sender_type', 'mitra')
                                        ->count();
                                } catch (\Throwable $e) {}
                            }
                        @endphp
                        <a href="{{ route('customer.chat') }}" wire:navigate
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('customer.chat*') || request()->routeIs('chat.*') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                @if($unreadCustomerChats > 0)
                                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5 items-center justify-center pointer-events-none">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 ring-1.5 ring-white"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="nav-label text-xs font-bold mt-0.5">Chat</span>
                        </a>

                        @php
                            $u = auth()->user();
                            $needsProfileAttention = $u && (
                                !$u->verified || 
                                !empty($u->getMissingBiodataFields()) || 
                                !$u->hasVerifiedEmail()
                            );
                        @endphp
                        <a href="{{ route('profile') }}" wire:navigate
                            class="nav-item flex flex-col items-center py-1.5 {{ request()->routeIs('profile.*') || request()->routeIs('profile') ? 'text-primary-600 active' : 'text-gray-400 hover:text-primary-600' }} transition">
                            <div class="relative inline-flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                @if($needsProfileAttention)
                                    <span class="absolute bottom-0.5 -right-0.5 flex h-2 w-2 items-center justify-center pointer-events-none">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 ring-1.5 ring-white"></span>
                                    </span>
                                @endif
                            </div>
                            <span class="nav-label text-xs font-bold mt-0.5">Akun</span>
                        </a>
                    </div>
                </nav>
            @endauth
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    {{-- Realtime notifications for customer (invisible) --}}
    @livewire('customer.realtime-notifications')

    <script>
        let _lastCustomerToast = { key: '', time: 0 };
        function showCustomerNotification(argObj) {
            try {
                let options = argObj || {};
                if (Array.isArray(options)) {
                    options = options[0] || {};
                } else if (options && typeof options === 'object' && options.detail) {
                    options = Array.isArray(options.detail) ? (options.detail[0] || {}) : options.detail;
                }

                let title = options.title || 'Notifikasi';
                let message = options.message || options.msg || options.body || '';
                let url = options.url || '#';
                let timeout = options.timeout || 4000;
                let type = options.type || 'info';

                // Debounce duplicate toast notifications within 2 seconds
                const toastKey = `${title}:::${message}`;
                const now = Date.now();
                if (_lastCustomerToast.key === toastKey && (now - _lastCustomerToast.time) < 2000) {
                    return;
                }
                _lastCustomerToast = { key: toastKey, time: now };

                console.log('showCustomerNotification called', { title, message, url, timeout, type });
                const container = document.getElementById('customer-global-notification-inner');
                if (!container) { console.warn('customer-global-notification-inner not found'); return; }
                container.innerHTML = '';

                const isRejected = (type === 'error' || String(title).toLowerCase().includes('ditolak') || String(title).toLowerCase().includes('reject'));
                const isWarning = (type === 'warning' || String(title).toLowerCase().includes('peringatan') || String(title).toLowerCase().includes('belum berangkat'));
                const isSuccess = (type === 'success' || String(title).toLowerCase().includes('disetujui') || String(title).toLowerCase().includes('selesai') || String(title).toLowerCase().includes('berhasil') || type === 'taken' || type === 'completed');

                const wrap = document.createElement('div');
                wrap.className = 'bg-white rounded-2xl shadow-2xl p-3.5 max-w-md mx-3 pointer-events-auto transition transform duration-300 border overflow-hidden relative cursor-pointer hover:shadow-3xl';
                wrap.style.boxShadow = '0 10px 30px rgba(2,6,23,0.15)';

                // Configure icon & theme
                let iconBg = 'bg-primary-50 text-primary-600 border-primary-200';
                let iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                let badgeText = 'Info';
                let badgeClass = 'bg-primary-100 text-primary-700';
                let borderClass = 'border-primary-100';
                let buttonText = 'Lihat Detail &rarr;';
                let buttonClass = 'bg-primary-600 hover:bg-primary-700 text-white';

                if (isRejected) {
                    iconBg = 'bg-red-50 text-red-600 border-red-200';
                    iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`;
                    badgeText = 'Ditolak';
                    badgeClass = 'bg-red-100 text-red-700';
                    borderClass = 'border-red-200 ring-1 ring-red-500/10';
                    buttonClass = 'bg-red-600 hover:bg-red-700 text-white';
                    if (url.includes('topup')) {
                        buttonText = 'Lihat Riwayat Top-Up &rarr;';
                    } else if (url.includes('withdraw')) {
                        buttonText = 'Lihat Riwayat Withdraw &rarr;';
                    } else {
                        buttonText = 'Lihat Detail Pembatalan &rarr;';
                    }
                } else if (isSuccess) {
                    iconBg = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
                    badgeText = 'Sukses';
                    badgeClass = 'bg-emerald-100 text-emerald-700';
                    borderClass = 'border-emerald-200';
                    buttonClass = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                    buttonText = url.includes('topup') ? 'Lihat Riwayat Top-Up &rarr;' : 'Lihat Detail &rarr;';
                } else if (isWarning) {
                    iconBg = 'bg-amber-50 text-amber-600 border-amber-200';
                    iconSvg = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
                    badgeText = 'Peringatan';
                    badgeClass = 'bg-amber-100 text-amber-700';
                    borderClass = 'border-amber-200';
                    buttonClass = 'bg-amber-600 hover:bg-amber-700 text-white';
                    buttonText = 'Cek Status &rarr;';
                } else {
                    if (url.includes('topup')) {
                        buttonText = 'Lihat Riwayat Top-Up &rarr;';
                    }
                }

                wrap.className += ' ' + borderClass;

                wrap.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl ${iconBg} border flex items-center justify-center flex-shrink-0 shadow-xs">
                            ${iconSvg}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs font-bold text-gray-900 truncate">${escapeHtml(title || 'Notifikasi')}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase ${badgeClass} flex-shrink-0">${badgeText}</span>
                                </div>
                                <button type="button" class="btn-close text-gray-400 hover:text-gray-600 p-1 -mr-1 -mt-1 rounded-lg transition cursor-pointer text-xs" title="Tutup">&times;</button>
                            </div>
                            ${message ? `<p class="text-xs text-gray-600 leading-snug line-clamp-2">${escapeHtml(message)}</p>` : ''}
                            ${(url && url !== '#') ? `
                            <div class="mt-2.5 flex items-center justify-end gap-2">
                                <button type="button" class="btn-detail px-3 py-1.5 ${buttonClass} active:scale-95 text-xs font-bold rounded-lg shadow-xs cursor-pointer transition">
                                    ${buttonText}
                                </button>
                            </div>` : ''}
                        </div>
                    </div>
                `;

                const closeBtn = wrap.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.onclick = function(e) {
                        e.stopPropagation();
                        container.innerHTML = '';
                    };
                }
                const btn = wrap.querySelector('.btn-detail');
                if (btn) {
                    btn.onclick = function(e) {
                        e.stopPropagation();
                        if (url && url !== '#') window.location.href = url;
                        container.innerHTML = '';
                    };
                }

                wrap.addEventListener('click', function (ev) {
                    if (ev.target && (ev.target.closest('.btn-close') || ev.target.closest('.btn-detail'))) return;
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

                const effectiveTimeout = (isRejected || isWarning) ? Math.max(timeout, 9000) : timeout;
                setTimeout(() => { 
                    if (wrap && wrap.parentNode) {
                        wrap.style.opacity = '0';
                        wrap.style.transform = 'translateY(-10px)';
                        setTimeout(() => { container.innerHTML = ''; }, 300);
                    }
                }, effectiveTimeout);
            } catch (err) { console.error('showCustomerNotification error', err); }
        }

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

        function escapeHtml(unsafe) {
            return String(unsafe).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        const customerHelpDetailTemplate = "{{ route('customer.helps.detail', ['id' => 'REPLACE_ID']) }}";
        const customerChatRoute = "{{ route('customer.chat') ?? route('mitra.chat') }}";

        // Listen for various help status updates
        window.addEventListener('help-new-message', function (e) {
            console.log('customer help-new-message received', e && e.detail ? e.detail : e);
            const helpId = e && e.detail && e.detail.helpId ? e.detail.helpId : null;
            const from = e && e.detail && e.detail.from ? e.detail.from : 'Mitra';
            const message = e && e.detail && e.detail.message ? e.detail.message : '';
            const url = helpId ? customerChatRoute.replace(/\/?$/, '') + '/' + encodeURIComponent(helpId) : customerChatRoute;
            showCustomerNotification({ title: 'Pesan Baru dari ' + from, message: message || 'Ketuk untuk membuka chat.', url, timeout: 2500, type: 'message' });
        });

        window.addEventListener('help-taken', function (e) {
            console.log('🎯 help-taken event received!', e.detail);
            const helpId = e && e.detail && (e.detail.helpId ?? e.detail.help_id) ? (e.detail.helpId ?? e.detail.help_id) : null;
            const helpTitle = e && e.detail && (e.detail.helpTitle ?? e.detail.help_title) ? (e.detail.helpTitle ?? e.detail.help_title) : null;
            const mitraName = e && e.detail && (e.detail.mitraName ?? e.detail.mitra_name) ? (e.detail.mitraName ?? e.detail.mitra_name) : 'Mitra';
            const url = helpId ? customerHelpDetailTemplate.replace('REPLACE_ID', helpId) : '#';
            const message = e && e.detail && e.detail.message ? e.detail.message : (helpTitle ? `${mitraName} telah mengambil bantuan Anda: ${helpTitle}` : `${mitraName} telah mengambil bantuan Anda. Ketuk untuk melihat detail.`);
            const title = helpTitle ? `Bantuan: ${helpTitle}` : '\u2705 Bantuan Diambil!';
            console.log('🔔 Showing toast notification for help taken', { title, message });
            showCustomerNotification({ 
                title, 
                message, 
                url, 
                type: 'taken',
                timeout: 6000 
            });
        });

        window.addEventListener('help-on-the-way', function (e) {
            const helpId = e && e.detail && (e.detail.helpId ?? e.detail.help_id) ? (e.detail.helpId ?? e.detail.help_id) : null;
            const helpTitle = e && e.detail && (e.detail.helpTitle ?? e.detail.help_title) ? (e.detail.helpTitle ?? e.detail.help_title) : null;
            const mitraName = e && e.detail && (e.detail.mitraName ?? e.detail.mitra_name) ? (e.detail.mitraName ?? e.detail.mitra_name) : 'Mitra';
            const url = helpId ? customerHelpDetailTemplate.replace('REPLACE_ID', helpId) : '#';
            const message = e && e.detail && e.detail.message ? e.detail.message : (helpTitle ? `${mitraName} sedang menuju lokasi bantuan '${helpTitle}'. Ketuk untuk tracking.` : `${mitraName} sedang menuju lokasi Anda. Ketuk untuk tracking.`);
            const title = helpTitle ? `Dalam Perjalanan: ${helpTitle}` : '\ud83d\ude80 Mitra Dalam Perjalanan';
            showCustomerNotification({ 
                title, 
                message, 
                url, 
                type: 'on_the_way',
                timeout: 7000 
            });
        });

        window.addEventListener('help-arrived', function (e) {
            const helpId = e && e.detail && (e.detail.helpId ?? e.detail.help_id) ? (e.detail.helpId ?? e.detail.help_id) : null;
            const helpTitle = e && e.detail && (e.detail.helpTitle ?? e.detail.help_title) ? (e.detail.helpTitle ?? e.detail.help_title) : null;
            const mitraName = e && e.detail && (e.detail.mitraName ?? e.detail.mitra_name) ? (e.detail.mitraName ?? e.detail.mitra_name) : 'Mitra';
            const url = helpId ? customerHelpDetailTemplate.replace('REPLACE_ID', helpId) : '#';
            const message = e && e.detail && e.detail.message ? e.detail.message : (helpTitle ? `${mitraName} telah tiba untuk bantuan '${helpTitle}'. Silakan konfirmasi.` : `${mitraName} telah tiba di lokasi Anda. Silakan konfirmasi.`);
            const title = helpTitle ? `Tiba: ${helpTitle}` : '\ud83d\udccd Mitra Sudah Sampai!';
            showCustomerNotification({ 
                title, 
                message, 
                url, 
                type: 'arrived',
                timeout: 8000 
            });
        });

        window.addEventListener('help-completed', function (e) {
            const helpId = e && e.detail && (e.detail.helpId ?? e.detail.help_id) ? (e.detail.helpId ?? e.detail.help_id) : null;
            const helpTitle = e && e.detail && (e.detail.helpTitle ?? e.detail.help_title) ? (e.detail.helpTitle ?? e.detail.help_title) : null;
            const mitraName = e && e.detail && (e.detail.mitraName ?? e.detail.mitra_name) ? (e.detail.mitraName ?? e.detail.mitra_name) : 'Mitra';
            const url = helpId ? customerHelpDetailTemplate.replace('REPLACE_ID', helpId) : '#';
            const message = e && e.detail && e.detail.message ? e.detail.message : (helpTitle ? `Bantuan '${helpTitle}' telah diselesaikan oleh ${mitraName}. Beri rating mitra Anda.` : `Bantuan telah diselesaikan oleh ${mitraName}. Beri rating mitra Anda.`);
            const title = helpTitle ? `Selesai: ${helpTitle}` : '\ud83c\udf89 Bantuan Selesai!';
            showCustomerNotification({ 
                title, 
                message, 
                url, 
                type: 'completed',
                timeout: 8000 
            });
        });

        window.addEventListener('help-status-update', function (e) {
            try {
                console.log('help-status-update raw event:', e);

                const detail = e && e.detail ? e.detail : {};

                // If payload is nested under `data` or first array element, normalize it
                const normalized = (detail.data) ? detail.data : (Array.isArray(detail) && detail.length ? detail[0] : detail);

                // Helper to read many possible keys
                const read = (obj, keys) => {
                    for (let k of keys) {
                        if (!obj) continue;
                        if (Object.prototype.hasOwnProperty.call(obj, k) && obj[k] !== null && obj[k] !== undefined && String(obj[k]) !== '') return obj[k];
                    }
                    return null;
                };

                const helpId = read(normalized, ['helpId','help_id','id']);
                const helpTitle = read(normalized, ['helpTitle','help_title']);
                const mitraName = read(normalized, ['mitraName','mitra_name','mitra']) || 'Mitra';
                const notifTitle = read(normalized, ['notifTitle','notif_title','notification_title']) || (normalized.title && normalized.title !== helpTitle ? normalized.title : null);

                const status = read(normalized, ['newStatus','new_status','status','state']) || '';
                const payloadMessage = read(normalized, ['message','msg','text']) || null;

                const url = (normalized.url && normalized.url !== '#') ? normalized.url : (helpId ? customerHelpDetailTemplate.replace('REPLACE_ID', helpId) : '#');

                // Build fallback based on status
                let fallbackMessage = 'Status bantuan diperbarui';
                let defaultTitle = '🔔 Update Status';
                let type = 'info';

                if (status) {
                    const s = String(status).toLowerCase();
                    if (s.includes('partner_on_the_way') || s.includes('on_the_way') || s.includes('perjalanan')) {
                        type = 'on_the_way';
                        defaultTitle = '🚗 Mitra Dalam Perjalanan';
                        fallbackMessage = `${mitraName} sedang menuju lokasi Anda.`;
                    } else if (s.includes('partner_arrived') || s.includes('arrived') || s.includes('sampai')) {
                        type = 'arrived';
                        defaultTitle = '📍 Mitra Telah Tiba di Lokasi';
                        fallbackMessage = `${mitraName} telah tiba di lokasi Anda.`;
                    } else if (s.includes('in_progress') || s.includes('sedang_diproses')) {
                        type = 'in_progress';
                        defaultTitle = '⚙️ Pekerjaan Dimulai';
                        fallbackMessage = `${mitraName} telah memulai pekerjaan.`;
                    } else if (s.includes('waiting_customer_confirmation')) {
                        type = 'confirmation';
                        defaultTitle = '✋ Menunggu Konfirmasi Anda';
                        fallbackMessage = `${mitraName} selesai mengerjakan bantuan '${helpTitle || ''}'. Silakan konfirmasi.`;
                    } else if (s.includes('rejected') || s.includes('ditolak')) {
                        type = 'error';
                        defaultTitle = '❌ Bantuan Ditolak Admin';
                        fallbackMessage = helpTitle ? `Permintaan bantuan '${helpTitle}' ditolak oleh admin.` : 'Permintaan bantuan Anda ditolak oleh admin.';
                    } else if (s.includes('selesai') || s.includes('completed')) {
                        type = 'completed';
                        defaultTitle = '🎉 Bantuan Selesai!';
                        fallbackMessage = helpTitle ? `Bantuan '${helpTitle}' telah selesai.` : 'Bantuan telah selesai.';
                    } else if (s.includes('partner_cancelled_direct') || s.includes('cancelled_direct')) {
                        type = 'warning';
                        defaultTitle = '🔄 Rekan Jasa Berhalangan Hadir';
                        fallbackMessage = `Rekan Jasa ${mitraName} berhalangan hadir. Sistem sedang mencarikan Rekan Jasa pengganti untuk Anda.`;
                    } else if (s.includes('diambil') || s.includes('taken')) {
                        type = 'taken';
                        defaultTitle = '✅ Bantuan Diambil!';
                        fallbackMessage = helpTitle ? `${mitraName} telah mengambil bantuan '${helpTitle}'.` : `${mitraName} telah mengambil bantuan Anda.`;
                    }
                }

                const title = notifTitle || defaultTitle;
                const message = payloadMessage || fallbackMessage;

                console.log('help-status-update parsed:', { helpId, helpTitle, mitraName, status, message, type, title, url });

                showCustomerNotification({ title, message, url, type, timeout: 7000 });
            } catch (err) { console.error('help-status-update handler error', err); }
        });

        // Generic text-only toast trigger for other components
        // Usage: window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: 'Hi', message: 'Hello', type: 'info', timeout: 4000, url: '#' } }));
        window.addEventListener('customer-toast', function (e) {
            try {
                let d = e && e.detail ? e.detail : {};
                if (Array.isArray(d)) {
                    d = d[0] || {};
                }
                showCustomerNotification(d);
            } catch (err) { console.error('customer-toast handler error', err); }
        });
    </script>
    <script>
        // Toggle blur on bottom nav and any elements with `.blur-on-modal` when a modal is present in DOM.
        function checkConfirmModalAndToggleBlur() {
            try {
                var modal = document.querySelector('[data-confirm-modal], [data-transaction-modal], [data-tracking-modal]');
                var nav = document.querySelector('#bottom-nav');
                var extras = document.querySelectorAll('.blur-on-modal');

                if (nav) {
                    if (modal) {
                        nav.classList.add('filter', 'blur-sm');
                    } else {
                        nav.classList.remove('filter', 'blur-sm');
                    }
                }

                if (extras && extras.length) {
                    extras.forEach(function (el) {
                        if (modal) {
                            el.classList.add('filter', 'blur-sm');
                        } else {
                            el.classList.remove('filter', 'blur-sm');
                        }
                    });
                }
            } catch (e) {
                console.warn('checkConfirmModalAndToggleBlur error', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            checkConfirmModalAndToggleBlur();
        });

        // Livewire fires these events after DOM updates
        window.addEventListener('livewire:load', function () {
            checkConfirmModalAndToggleBlur();
        });

        window.addEventListener('livewire:update', function () {
            checkConfirmModalAndToggleBlur();
        });

        // Also observe mutations to catch cases where Livewire doesn't trigger events
        try {
            var observer = new MutationObserver(function () { checkConfirmModalAndToggleBlur(); });
            observer.observe(document.body, { childList: true, subtree: true });
        } catch (e) {
            // ignore
        }
    </script>

    <!-- Modal Notifikasi Verifikasi KTP / Identitas Ditolak -->
    @include('partials.ktp-rejected-modal')

    <!-- Modal Notifikasi Akun Diblokir / Dinonaktifkan -->
    @php
        $isCustomerAccountDisabled = auth()->check() && in_array(auth()->user()->status, ['blocked', 'inactive']);
        $isCustomerInactive = auth()->check() && auth()->user()->status === 'inactive';
    @endphp
    <div id="blocked-account-modal" class="{{ $isCustomerAccountDisabled ? '' : 'hidden' }}" style="{{ $isCustomerAccountDisabled ? 'display: flex !important;' : '' }} position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 transform animate-bounce-in">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <h3 id="blocked-account-title" class="text-lg font-extrabold text-gray-900 mb-2">
                {{ $isCustomerInactive ? 'Akun Anda Dinonaktifkan' : 'Akun Anda Telah Diblokir' }}
            </h3>
            <p id="blocked-account-message" class="text-xs text-gray-600 mb-6 leading-relaxed">
                {{ $isCustomerInactive 
                    ? 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan bantuan.' 
                    : 'Akses akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service SayaBantu jika Anda memerlukan informasi lebih lanjut.' }}
            </p>
            <a href="{{ route('logout') }}"
               onclick="this.style.pointerEvents='none'; this.innerHTML='<span>Mengeluarkan...</span>';"
               class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-xl text-sm shadow-lg shadow-red-200 transition-all flex items-center justify-center gap-2 cursor-pointer text-center">
                <span>OK, Mengerti</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>

    <script>
        @auth
        function triggerCustomerBlockedModal(isInactive = null) {
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
        function checkCustomerAccountStatus() {
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
                    triggerCustomerBlockedModal(isInactive);
                }
            })
            .catch(() => {});
        }

        // Check immediately on load, on focus, on click, and every 2.5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            @if(in_array(auth()->user()->status, ['blocked', 'inactive']))
                triggerCustomerBlockedModal({{ auth()->user()->status === 'inactive' ? 'true' : 'false' }});
            @else
                checkCustomerAccountStatus();
            @endif
        });

        setInterval(checkCustomerAccountStatus, 30000);
        window.addEventListener('focus', checkCustomerAccountStatus);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkCustomerAccountStatus();
        });
        @else
        function triggerCustomerBlockedModal(isInactive = null) {}
        @endauth

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
                            triggerCustomerBlockedModal(isInactive);
                        }
                    });
                });
            }
        });

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
</body>

</html>