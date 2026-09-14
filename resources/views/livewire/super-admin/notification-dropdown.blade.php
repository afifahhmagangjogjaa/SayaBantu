<div class="relative" 
     x-data="{ 
        open: false,
        queue: [],
        currentNotif: null,
        shownIds: JSON.parse(sessionStorage.getItem('sb_superadmin_shown_notifs') || '[]'),
        progress: 100,
        paused: false,
        timerInterval: null,
        
        init() {
            this.checkNewNotifications(@js($recentUnread));
            this.$watch('$wire.recentUnread', (val) => {
                this.checkNewNotifications(val);
            });
        },

        checkNewNotifications(items) {
            if (!items || !items.length) return;
            let hasNew = false;
            items.forEach(notif => {
                if (!this.shownIds.includes(notif.id)) {
                    this.queue.push(notif);
                    this.shownIds.push(notif.id);
                    hasNew = true;
                }
            });

            if (hasNew) {
                sessionStorage.setItem('sb_superadmin_shown_notifs', JSON.stringify(this.shownIds.slice(-50)));
                if (!this.currentNotif && this.queue.length > 0) {
                    this.showNext();
                    this.playChime();
                }
            }
        },

        showNext() {
            if (this.queue.length > 0) {
                this.currentNotif = this.queue.shift();
                this.resetTimer();
            } else {
                this.currentNotif = null;
                clearInterval(this.timerInterval);
            }
        },

        resetTimer() {
            clearInterval(this.timerInterval);
            this.progress = 100;
            const totalMs = 8000;
            const stepMs = 100;
            let elapsed = 0;
            this.timerInterval = setInterval(() => {
                if (!this.paused) {
                    elapsed += stepMs;
                    this.progress = Math.max(0, 100 - (elapsed / totalMs * 100));
                    if (elapsed >= totalMs) {
                        clearInterval(this.timerInterval);
                        this.showNext();
                    }
                }
            }, stepMs);
        },

        dismissCurrent() {
            this.showNext();
        },

        dismissAll() {
            this.queue = [];
            this.currentNotif = null;
            clearInterval(this.timerInterval);
        },

        async openUrl() {
            if (this.currentNotif) {
                const id = this.currentNotif.id;
                const url = this.currentNotif.url;
                this.dismissAll();
                try {
                    await @this.call('openNotification', id);
                } catch (e) {
                    console.error('Failed to open notification:', e);
                }
                if (url && url !== '#' && url !== '') {
                    window.location.href = url;
                }
            }
        },

         playChime() {
             try {
                 const ctx = new (window.AudioContext || window.webkitAudioContext)();
                 const now = ctx.currentTime;
                 
                 const osc1 = ctx.createOscillator();
                 const gain1 = ctx.createGain();
                 osc1.type = 'sine';
                 osc1.frequency.setValueAtTime(587.33, now);
                 gain1.gain.setValueAtTime(0.15, now);
                 gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                 osc1.connect(gain1);
                 gain1.connect(ctx.destination);
                 osc1.start(now);
                 osc1.stop(now + 0.35);

                 const osc2 = ctx.createOscillator();
                 const gain2 = ctx.createGain();
                 osc2.type = 'sine';
                 osc2.frequency.setValueAtTime(880, now + 0.12);
                 gain2.gain.setValueAtTime(0.2, now + 0.12);
                 gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                 osc2.connect(gain2);
                 gain2.connect(ctx.destination);
                 osc2.start(now + 0.12);
                 osc2.stop(now + 0.5);
             } catch (e) {}
         },

         toggle() {
             this.open = !this.open;
             if (this.open) {
                 @this.call('loadNotifications');
             }
         }
     }" 
     @click.away="open = false"
     wire:poll.10s="loadNotifications">
    
    <!-- Notification Button -->
    <button 
        @click="toggle()"
        class="relative w-9 h-9 flex items-center justify-center text-gray-600 hover:text-primary-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition shadow-2xs cursor-pointer focus:outline-none"
        type="button"
        title="Notifikasi">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white shadow-xs">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu (Compact & Sleek) -->
    <div 
        x-show="open"
        x-cloak
        @click.stop
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 overflow-hidden"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 bg-gray-50/70">
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-gray-900">Notifikasi</span>
                @if($unreadCount > 0)
                    <span class="text-[11px] text-gray-500 font-normal">({{ $unreadCount }} belum dibaca)</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <button 
                        wire:click="markAllAsRead"
                        type="button"
                        class="text-[11px] text-primary-600 hover:text-primary-700 font-semibold transition cursor-pointer hover:underline">
                        Tandai dibaca
                    </button>
                @endif
                @if(count($notifications) > 0)
                    <button 
                        wire:click="deleteAllNotifications"
                        wire:confirm="Apakah Anda yakin ingin menghapus semua notifikasi?"
                        type="button"
                        class="text-[11px] text-red-600 hover:text-red-700 font-semibold transition cursor-pointer hover:underline">
                        Hapus Semua
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List (Compact Rows) -->
        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $type = $data['type'] ?? '';
                    $isUnread = is_null($notification->read_at);
                    
                    if (str_contains($type, 'topup')) {
                        $iconBg = 'bg-emerald-100 text-emerald-600';
                    } elseif (str_contains($type, 'withdraw')) {
                        $iconBg = 'bg-amber-100 text-amber-600';
                    } elseif (str_contains($type, 'registration') || str_contains($type, 'ktp') || str_contains($type, 'user')) {
                        $iconBg = 'bg-indigo-100 text-indigo-600';
                    } elseif (str_contains($type, 'report') || str_contains($type, 'komplain')) {
                        $iconBg = 'bg-red-100 text-red-600';
                    } else {
                        $iconBg = 'bg-blue-100 text-blue-600';
                    }
                @endphp
                <div 
                    wire:key="dropdown-notif-{{ $notification->id }}"
                    wire:click="openNotification('{{ $notification->id }}')"
                    class="group flex items-center gap-2.5 px-3.5 py-2.5 hover:bg-gray-50 transition cursor-pointer {{ $isUnread ? 'bg-primary-50/25' : 'bg-white' }}">
                    
                    <!-- Icon -->
                    <div class="w-7 h-7 rounded-lg {{ $iconBg }} flex items-center justify-center flex-shrink-0">
                        @if(str_contains($type, 'topup'))
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif(str_contains($type, 'withdraw'))
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        @elseif(str_contains($type, 'registration') || str_contains($type, 'ktp') || str_contains($type, 'user'))
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        @elseif(str_contains($type, 'report') || str_contains($type, 'komplain'))
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    <!-- Text Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="text-xs font-semibold text-gray-900 truncate">
                                    {{ $data['title'] ?? 'Notifikasi' }}
                                </span>
                                @if($isUnread)
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap flex-shrink-0">
                                {{ $notification->created_at->diffForHumans(null, true, true) }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">
                            {{ $data['message'] ?? $data['body'] ?? 'Tidak ada pesan' }}
                        </p>
                    </div>

                    <!-- Quick Hover Actions -->
                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        @if($isUnread)
                            <button 
                                wire:click.stop="markAsRead('{{ $notification->id }}')"
                                class="p-1 text-primary-600 hover:bg-primary-100 rounded transition cursor-pointer"
                                title="Tandai dibaca"
                                type="button">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        @endif
                        <button 
                            wire:click.stop="deleteNotification('{{ $notification->id }}')"
                            wire:confirm="Hapus notifikasi ini?"
                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition cursor-pointer"
                            title="Hapus"
                            type="button">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="w-9 h-9 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-600">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-3 py-2 bg-gray-50/80 border-t border-gray-100 text-center">
            <a href="{{ route('superadmin.notifications.index') }}" 
                class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary-600 hover:text-primary-700 transition">
                <span>Lihat Semua</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Compact Floating Real-time Notification Pop-up (Like Customer & Mitra) -->
    <div wire:ignore
         x-show="currentNotif !== null" 
         x-cloak
         @keydown.escape.window="dismissAll()"
         class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[99999] w-[92vw] max-w-sm pointer-events-none"
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-3 scale-95">
        
        <div 
            @mouseenter="paused = true"
            @mouseleave="paused = false"
            class="pointer-events-auto relative w-full bg-white/95 backdrop-blur-md rounded-xl shadow-xl border border-gray-200/90 p-2.5 sm:p-3 overflow-hidden transition-all hover:shadow-2xl"
            style="box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);"
        >
            <div class="flex items-center gap-2.5">
                <!-- Compact Icon (w-8 h-8) -->
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-white shadow-xs"
                     :class="{
                         'bg-emerald-500': currentNotif && currentNotif.type && currentNotif.type.includes('topup'),
                         'bg-amber-500': currentNotif && currentNotif.type && currentNotif.type.includes('withdraw'),
                         'bg-blue-500': currentNotif && currentNotif.type && (currentNotif.type.includes('ktp') || currentNotif.type.includes('registration') || currentNotif.type.includes('user')),
                         'bg-rose-500': currentNotif && currentNotif.type && (currentNotif.type.includes('komplain') || currentNotif.type.includes('report') || currentNotif.type.includes('rejected')),
                         'bg-primary-600': currentNotif && (!currentNotif.type || (!currentNotif.type.includes('topup') && !currentNotif.type.includes('withdraw') && !currentNotif.type.includes('ktp') && !currentNotif.type.includes('registration') && !currentNotif.type.includes('user') && !currentNotif.type.includes('komplain') && !currentNotif.type.includes('report') && !currentNotif.type.includes('rejected')))
                     }">
                    <template x-if="currentNotif && currentNotif.type && currentNotif.type.includes('topup')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="currentNotif && currentNotif.type && currentNotif.type.includes('withdraw')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </template>
                    <template x-if="currentNotif && currentNotif.type && (currentNotif.type.includes('ktp') || currentNotif.type.includes('registration') || currentNotif.type.includes('user'))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </template>
                    <template x-if="currentNotif && currentNotif.type && (currentNotif.type.includes('komplain') || currentNotif.type.includes('report') || currentNotif.type.includes('rejected'))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <template x-if="currentNotif && (!currentNotif.type || (!currentNotif.type.includes('topup') && !currentNotif.type.includes('withdraw') && !currentNotif.type.includes('ktp') && !currentNotif.type.includes('registration') && !currentNotif.type.includes('user') && !currentNotif.type.includes('komplain') && !currentNotif.type.includes('report') && !currentNotif.type.includes('rejected')))">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 cursor-pointer" @click="openUrl()">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-gray-900 truncate" x-text="currentNotif ? currentNotif.title : ''"></span>
                        <template x-if="queue.length > 0">
                            <span class="text-[9px] font-bold text-primary-700 bg-primary-50 px-1.5 py-0.5 rounded-full border border-primary-200 flex-shrink-0"
                                  x-text="`+${queue.length}`"></span>
                        </template>
                    </div>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5" x-text="currentNotif ? currentNotif.message : ''"></p>
                </div>

                <!-- Action Button + Dismiss -->
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button 
                        @click="openUrl()" 
                        type="button" 
                        class="px-2.5 py-1 text-[11px] font-bold text-white rounded-lg shadow-2xs hover:opacity-90 transition cursor-pointer"
                        style="background: linear-gradient(to right, #0098e7, #0077cc);"
                        title="Buka notifikasi">
                        Lihat
                    </button>
                    <button 
                        @click="dismissCurrent()" 
                        type="button" 
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-md hover:bg-gray-100 transition cursor-pointer"
                        title="Tutup">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Slim Progress Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-100 overflow-hidden">
                <div class="h-full bg-primary-500 transition-all duration-100 ease-linear"
                     :style="`width: ${progress}%`"></div>
            </div>
        </div>
    </div>
</div>
