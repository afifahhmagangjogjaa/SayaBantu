<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Semua Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $unreadCount }} belum dibaca &bull; {{ $totalCount }} total</p>
        </div>
        <div class="flex items-center gap-2">
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai Semua Dibaca
                </button>
            @endif
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if(session('message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div class="flex gap-2">
                <button type="button" wire:click="setFilter('all')"
                    class="px-4 py-1.5 rounded-lg text-sm font-semibold transition
                        {{ $filter === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua ({{ $totalCount }})
                </button>
                <button type="button" wire:click="setFilter('unread')"
                    class="px-4 py-1.5 rounded-lg text-sm font-semibold transition
                        {{ $filter === 'unread' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Belum Dibaca
                    @if($unreadCount > 0)
                        <span class="ml-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </button>
            </div>

            @if(count($selected) > 0)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">{{ count($selected) }} dipilih</span>
                    <button type="button" wire:click="bulkMarkAsRead"
                        class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-medium hover:bg-primary-700">
                        Tandai Dibaca
                    </button>
                    <button type="button" wire:click="bulkDelete"
                        class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700">
                        Hapus
                    </button>
                    <button type="button" wire:click="clearSelection" class="text-gray-400 hover:text-gray-600 text-xs">✕</button>
                </div>
            @elseif($notifications->count() > 0)
                <button type="button"
                    wire:click="selectAllOnPage({{ json_encode($notifications->pluck('id')->toArray()) }})"
                    class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Pilih Semua
                </button>
            @endif
        </div>

        {{-- Notifications List --}}
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($notifications as $notification)
                    @php
                        $data      = $notification->data ?? [];
                        $isUnread  = is_null($notification->read_at);
                        $type      = $data['type'] ?? 'general';
                        $helpId    = $data['help_id'] ?? null;
                        $reportId  = $data['report_id'] ?? null;

                        $detailUrl = match(true) {
                            in_array($type, ['new_topup_request','topup_request_submitted']) => route('admin.topup.approvals'),
                            in_array($type, ['new_withdraw_request','withdraw_status'])       => route('admin.withdraws.index'),
                            $type === 'new_ktp_verification'                                  => route('admin.verifications'),
                            in_array($type, ['new_registration','new_user'])                  => route('admin.customers'),
                            in_array($type, ['new_report','partner_report']) && $reportId     => route('admin.partners.reports.show', $reportId),
                            in_array($type, ['new_report','partner_report'])                  => route('admin.partners.report'),
                            $type === 'help_status' && $helpId                                => route('admin.helps.show', $helpId),
                            $type === 'help_status'                                           => route('admin.helps'),
                            !empty($data['url'])                                              => $data['url'],
                            default                                                           => '#',
                        };

                        $title = $data['title'] ?? match($type) {
                            'new_topup_request', 'topup_request_submitted' => '💰 Request Top-Up Saldo Baru',
                            'new_withdraw_request', 'withdraw_status'      => '💵 Permintaan Tarik Saldo',
                            'new_ktp_verification'                         => '🪪 Verifikasi KTP Baru',
                            'new_registration', 'new_user'                 => '👤 Pendaftaran Pengguna Baru',
                            'new_report', 'partner_report'                 => '⚠️ Laporan Aduan Baru',
                            'help_status'                                  => '📢 Status Bantuan Diperbarui',
                            default                                        => '📢 Notifikasi',
                        };

                        $body = $data['message'] ?? ($data['body'] ?? 'Ada pembaruan baru.');
                    @endphp

                    <div wire:key="notif-{{ $notification->id }}"
                        class="flex items-start gap-3 px-4 py-4 transition hover:bg-gray-50 {{ $isUnread ? 'bg-blue-50/40' : '' }}">

                        {{-- Checkbox --}}
                        <div class="flex-shrink-0 pt-0.5">
                            <input type="checkbox" wire:model.live="selected" value="{{ $notification->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-primary-600 cursor-pointer">
                        </div>

                        {{-- Unread dot --}}
                        <div class="flex-shrink-0 mt-1.5">
                            @if($isUnread)
                                <span class="block w-2 h-2 rounded-full bg-primary-500"></span>
                            @else
                                <span class="block w-2 h-2 rounded-full bg-transparent"></span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $title }}</p>
                                    <p class="text-sm text-gray-600 mt-0.5 leading-snug">{{ $body }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($detailUrl !== '#')
                                        <button type="button"
                                            wire:click="readAndRedirect('{{ $notification->id }}', '{{ $detailUrl }}')"
                                            class="text-xs font-semibold text-primary-600 hover:underline whitespace-nowrap">
                                            Lihat Detail →
                                        </button>
                                    @endif
                                    @if($isUnread)
                                        <button type="button"
                                            wire:click="markAsRead('{{ $notification->id }}')"
                                            class="text-xs font-semibold text-emerald-600 hover:underline whitespace-nowrap">
                                            Tandai Dibaca
                                        </button>
                                    @endif
                                    <button type="button"
                                        wire:click="deleteNotification('{{ $notification->id }}')"
                                        class="text-xs font-semibold text-red-500 hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $notifications->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">
                    {{ $filter === 'unread' ? 'Semua Notifikasi Sudah Dibaca' : 'Belum Ada Notifikasi' }}
                </h3>
                <p class="text-sm text-gray-500">Notifikasi aktivitas sistem akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
