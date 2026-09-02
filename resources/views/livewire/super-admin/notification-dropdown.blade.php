<div class="relative" 
    x-data="{ 
        open: false,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                @this.call('loadNotifications');
            }
        }
    }" 
    @click.away="open = false"
    wire:poll.20s="loadNotifications">
    
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
            @if($unreadCount > 0)
                <button 
                    wire:click="markAllAsRead"
                    type="button"
                    class="text-[11px] text-primary-600 hover:text-primary-700 font-semibold transition cursor-pointer hover:underline">
                    Tandai dibaca
                </button>
            @endif
        </div>

        <!-- Notifications List (Compact Rows) -->
        <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $type = $data['type'] ?? '';
                    $isUnread = is_null($notification->read_at);
                    
                    if ($type === 'new_topup_request' || $type === 'topup_request_submitted') {
                        $iconBg = 'bg-emerald-100 text-emerald-600';
                    } elseif ($type === 'new_withdraw_request' || $type === 'withdraw_status') {
                        $iconBg = 'bg-amber-100 text-amber-600';
                    } elseif ($type === 'new_registration' || $type === 'new_user') {
                        $iconBg = 'bg-indigo-100 text-indigo-600';
                    } elseif ($type === 'help_taken' || $type === 'help_status') {
                        $iconBg = 'bg-purple-100 text-purple-600';
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
                        @if($type === 'new_topup_request' || $type === 'topup_request_submitted')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($type === 'new_withdraw_request' || $type === 'withdraw_status')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        @elseif($type === 'new_registration' || $type === 'new_user')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        @elseif($type === 'help_taken' || $type === 'help_status')
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
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
</div>
