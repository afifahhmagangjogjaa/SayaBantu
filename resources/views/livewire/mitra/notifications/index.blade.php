<div class="min-h-screen bg-white">
    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style Standar Bawaan -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <a href="{{ route('mitra.dashboard') }}" aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold">Notifikasi</h1>
                        <p class="text-xs text-white/90 mt-0.5">{{ $unreadCount }} belum dibaca</p>
                    </div>

                    @if($unreadCount > 0)
                        <button type="button" wire:click="markAllAsRead" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center text-white" title="Tandai semua dibaca">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    @else
                        <div class="w-9"></div>
                    @endif
                </div>
            </div>

            <!-- Curved separator (Garis Lengkung Bawaan Asli) -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24">
            <!-- Filter Tabs Standar Bawaan -->
            <div class="mb-5 flex items-center justify-between gap-2">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button type="button" wire:click="setFilter('all')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filter === 'all' ? 'text-white shadow-sm' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}"
                        style="{{ $filter === 'all' ? 'background: linear-gradient(to bottom right, #0098e7, #0060b0);' : '' }}">
                        Semua ({{ $totalCount }})
                    </button>
                    <button type="button" wire:click="setFilter('unread')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $filter === 'unread' ? 'text-white shadow-sm' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}"
                        style="{{ $filter === 'unread' ? 'background: linear-gradient(to bottom right, #0098e7, #0060b0);' : '' }}">
                        Belum Dibaca @if($unreadCount > 0) <span class="ml-1 bg-red-500 text-white px-1.5 py-0.5 rounded-full text-[10px] font-bold">{{ $unreadCount }}</span> @endif
                    </button>
                </div>

                @if(count($selected) > 0)
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="bulkMarkAsRead" class="px-2.5 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-medium hover:bg-blue-700 whitespace-nowrap">
                            Dibaca
                        </button>
                        <button type="button" wire:click="bulkDelete" class="px-2.5 py-1.5 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 whitespace-nowrap">
                            Hapus
                        </button>
                        <button type="button" wire:click="clearSelection" class="p-1 text-gray-400 hover:text-gray-600 text-xs">
                            ✕
                        </button>
                    </div>
                @elseif($notifications->count() > 0)
                    <button type="button" wire:click="selectAllOnPage({{ json_encode($notifications->pluck('id')->toArray()) }})" class="text-xs font-semibold text-blue-600 hover:text-blue-700 whitespace-nowrap">
                        Pilih Semua
                    </button>
                @endif
            </div>

            <!-- Notifications List -->
            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $isUnread = is_null($notification->read_at);
                            $type = $data['type'] ?? 'general';
                            $helpId = $data['help_id'] ?? null;

                            if ($type === 'chat_message') {
                                $titleText = 'Pesan dari ' . ($data['from_name'] ?? 'Customer');
                                $detailUrl = $helpId ? route('mitra.chat', $helpId) : '#';
                            } elseif ($type === 'help_request' || str_contains($type, 'help')) {
                                $titleText = $data['title'] ?? 'Permintaan Bantuan';
                                $detailUrl = $helpId ? route('mitra.helps.detail', $helpId) : '#';
                            } elseif ($type === 'withdraw' || str_contains($type, 'withdraw')) {
                                $titleText = $data['title'] ?? 'Status Penarikan Saldo';
                                $detailUrl = route('mitra.withdraw.history');
                            } else {
                                $titleText = $data['title'] ?? 'Notifikasi';
                                $detailUrl = '#';
                            }

                            $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                        @endphp

                        <div wire:key="notif-{{ $notification->id }}" class="bg-white rounded-2xl border {{ $isUnread ? 'border-blue-200 bg-blue-50/20' : 'border-gray-200' }} p-4 transition hover:border-blue-300 shadow-2xs">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $notification->id }}" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 cursor-pointer" aria-label="Pilih notifikasi">
                                </div>

                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border {{ $isUnread ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50' }}">
                                        @if($type === 'chat_message')
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        @elseif(str_contains($type, 'withdraw'))
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $titleText }}</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(isset($data['help_amount']))
                                            <div class="ml-2 text-right">
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold">Rp {{ number_format($data['help_amount'],0,',','.') }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 mt-2 leading-relaxed">{{ $bodyText }}</p>

                                    @if(isset($data['from_name']) || isset($data['customer_name']))
                                        <div class="text-xs text-gray-400 mt-1.5">Dari: {{ $data['from_name'] ?? $data['customer_name'] ?? '-' }}</div>
                                    @endif

                                    <div class="flex items-center gap-3 mt-3 pt-2 border-t border-gray-100">
                                        @if(isset($detailUrl) && $detailUrl !== '#')
                                            <button type="button" wire:click="readAndRedirect('{{ $notification->id }}', '{{ $detailUrl }}')" class="text-xs font-semibold text-blue-600 hover:underline text-left">Lihat Detail &rarr;</button>
                                        @endif
                                        @if($isUnread)
                                            <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-semibold text-emerald-600 hover:underline">Tandai Dibaca</button>
                                        @endif
                                        <button type="button" wire:click="deleteNotification('{{ $notification->id }}')" class="text-xs font-semibold text-red-500 hover:underline ml-auto">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($notifications->hasPages())
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-1">
                        {{ $filter === 'unread' ? 'Semua Notifikasi Sudah Dibaca' : 'Belum Ada Notifikasi' }}
                    </h3>
                    <p class="text-xs text-gray-500">Notifikasi aktivitas dan pesanan Anda akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
</div>
