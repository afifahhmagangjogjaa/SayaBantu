@php
    $title = 'Notifikasi';
    $subtitle = 'Daftar notifikasi dan pemberitahuan sistem';
    $breadcrumb = 'Super Admin / Notifikasi';
@endphp

<div class="space-y-6">
    <!-- Top Summary & Actions Bar -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Left: Filters -->
        <div class="flex flex-wrap items-center gap-2">
            <button 
                wire:click="$set('filter', 'all')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $filter === 'all' ? 'bg-primary-600 text-white shadow-sm ring-2 ring-primary-500/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $filter === 'all' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-600' }}">
                    {{ $totalCount ?? $notifications->total() }}
                </span>
            </button>
            <button 
                wire:click="$set('filter', 'unread')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $filter === 'unread' ? 'bg-primary-600 text-white shadow-sm ring-2 ring-primary-500/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span>Belum Dibaca</span>
                @if(($unreadCount ?? 0) > 0)
                    <span class="px-1.5 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-bold animate-pulse">
                        {{ $unreadCount }}
                    </span>
                @else
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $filter === 'unread' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-600' }}">0</span>
                @endif
            </button>
            <button 
                wire:click="$set('filter', 'read')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $filter === 'read' ? 'bg-primary-600 text-white shadow-sm ring-2 ring-primary-500/20' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <span>Sudah Dibaca</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $filter === 'read' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-600' }}">
                    {{ $readCount ?? 0 }}
                </span>
            </button>
        </div>

        <!-- Right: Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
            @if(($unreadCount ?? 0) > 0)
                <button 
                    wire:click="markAllAsRead"
                    type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Tandai Semua Dibaca</span>
                </button>
            @endif

            @if(($readCount ?? 0) > 0)
                <button 
                    wire:click="deleteAllRead"
                    wire:confirm="Apakah Anda yakin ingin menghapus semua notifikasi yang sudah dibaca?"
                    type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-200 hover:bg-red-50 hover:border-red-200 text-gray-700 hover:text-red-600 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Hapus Yang Sudah Dibaca</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden divide-y divide-gray-100">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $type = $data['type'] ?? '';
                $isUnread = is_null($notification->read_at);

                if ($type === 'new_topup_request' || $type === 'topup_request_submitted') {
                    $iconBg = 'bg-emerald-100 text-emerald-600 border-emerald-200';
                    $typeLabel = 'Top-Up';
                    $typeBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    $actionLabel = 'Review Top-Up';
                } elseif ($type === 'new_withdraw_request' || $type === 'withdraw_status') {
                    $iconBg = 'bg-amber-100 text-amber-600 border-amber-200';
                    $typeLabel = 'Withdraw';
                    $typeBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                    $actionLabel = 'Review Withdraw';
                } elseif ($type === 'new_registration' || $type === 'new_user') {
                    $iconBg = 'bg-indigo-100 text-indigo-600 border-indigo-200';
                    $typeLabel = 'User Baru';
                    $typeBadge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                    $actionLabel = 'Kelola User';
                } elseif ($type === 'help_taken' || $type === 'help_status') {
                    $iconBg = 'bg-purple-100 text-purple-600 border-purple-200';
                    $typeLabel = 'Bantuan';
                    $typeBadge = 'bg-purple-50 text-purple-700 border-purple-200';
                    $actionLabel = 'Detail Bantuan';
                } else {
                    $iconBg = 'bg-blue-100 text-blue-600 border-blue-200';
                    $typeLabel = 'Sistem';
                    $typeBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                    $actionLabel = 'Lihat Detail';
                }
            @endphp
            <div 
                wire:key="full-notif-{{ $notification->id }}"
                class="p-5 sm:p-6 hover:bg-gray-50/70 transition-all duration-150 {{ $isUnread ? 'bg-primary-50/20' : 'bg-white' }}">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-2xl {{ $iconBg }} border flex items-center justify-center shadow-xs">
                            @if($type === 'new_topup_request' || $type === 'topup_request_submitted')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($type === 'new_withdraw_request' || $type === 'withdraw_status')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            @elseif($type === 'new_registration' || $type === 'new_user')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            @elseif($type === 'help_taken' || $type === 'help_status')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold border rounded-md {{ $typeBadge }}">
                                    {{ $typeLabel }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900 leading-tight">
                                    {{ $data['title'] ?? 'Notifikasi' }}
                                </h3>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block" title="Belum dibaca"></span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 font-medium">
                                {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->translatedFormat('d M Y, H:i') }})
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                            {{ $data['message'] ?? $data['body'] ?? 'Tidak ada deskripsi pesan' }}
                        </p>

                        <!-- Extra Details Badge If Available -->
                        @if(isset($data['request_code']) || isset($data['amount']) || isset($data['customer_name']) || isset($data['user_name']))
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                @if(isset($data['request_code']))
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg font-mono font-semibold">
                                        Kode: {{ $data['request_code'] }}
                                    </span>
                                @endif
                                @if(isset($data['amount']))
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg font-semibold">
                                        Rp {{ number_format((float) $data['amount'], 0, ',', '.') }}
                                    </span>
                                @endif
                                @if(isset($data['customer_name']))
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg font-medium">
                                        User: {{ $data['customer_name'] }}
                                    </span>
                                @elseif(isset($data['user_name']))
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg font-medium">
                                        User: {{ $data['user_name'] }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons Row -->
                        <div class="mt-4 flex flex-wrap items-center gap-2.5">
                            <button 
                                wire:click="openNotification('{{ $notification->id }}')"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer">
                                <span>{{ $actionLabel }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>

                            @if($isUnread)
                                <button 
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer"
                                    title="Tandai sudah dibaca">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Tandai Dibaca</span>
                                </button>
                            @endif

                            <button 
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                wire:confirm="Hapus notifikasi ini?"
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 rounded-xl text-xs font-semibold transition cursor-pointer"
                                title="Hapus notifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-8 py-20 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-4 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Tidak ada notifikasi</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto">
                    @if($filter === 'unread')
                        Semua notifikasi sudah dibaca. Anda sudah up-to-date!
                    @elseif($filter === 'read')
                        Belum ada notifikasi yang telah dibaca.
                    @else
                        Belum ada notifikasi baru untuk Super Admin saat ini.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links('vendor.pagination.superadmin') }}
        </div>
    @endif
</div>
