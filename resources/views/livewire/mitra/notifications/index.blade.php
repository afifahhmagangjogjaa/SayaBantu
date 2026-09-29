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
            @if (session()->has('message'))
                <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-xs font-medium text-green-700 flex items-center justify-between">
                    <span>{{ session('message') }}</span>
                    <button type="button" class="text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs font-bold text-red-700 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 ml-2" onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @php
                $selectableIds = $notifications->reject(function($n) {
                    $t = $n->data['type'] ?? '';
                    return $t === 'sanction_warning' || str_contains($t, 'sanction');
                })->pluck('id')->toArray();
            @endphp

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
                @elseif(count($selectableIds) > 0)
                    <button type="button" wire:click="selectAllOnPage({{ json_encode($selectableIds) }})" class="text-xs font-semibold text-blue-600 hover:text-blue-700 whitespace-nowrap">
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
                            $isSanction = ($type === 'sanction_warning' || str_contains($type, 'sanction'));
                            $helpId = $data['help_id'] ?? null;

                            if ($isSanction) {
                                $warningLevel = (int) ($data['warning_level'] ?? 1);
                                if ($warningLevel === 0) {
                                    $titleText = '✅ Surat Peringatan Dicabut';
                                    $bodyText = $data['message'] ?? 'Surat Peringatan pada akun Anda telah resmi dicabut oleh Admin. Status akun Anda kini kembali normal. Ketuk Lihat Detail Surat untuk membaca surat pencabutan selengkapnya.';
                                } else {
                                    $titleText = match ($warningLevel) {
                                        1 => '⚠️ Surat Peringatan 1',
                                        2 => '⚠️ Surat Peringatan 2',
                                        3 => '⛔ Surat Peringatan 3 - Akun Dinonaktifkan',
                                        default => 'Surat Peringatan Resmi',
                                    };
                                    $bodyText = 'Anda menerima sanksi Surat Peringatan ' . $warningLevel . ' resmi dari SayaBantu. Ketuk Lihat Detail Surat untuk membaca surat selengkapnya.';
                                }
                                $detailUrl = route('notifications.sanction', $notification->id);
                            } elseif ($type === 'chat_message') {
                                $titleText = 'Pesan dari ' . ($data['from_name'] ?? 'Customer');
                                $detailUrl = $helpId ? route('mitra.chat', $helpId) : '#';
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                            } elseif (str_contains($type, 'ktp') || str_contains($type, 'verification')) {
                                $titleText = $data['title'] ?? 'Verifikasi KTP';
                                $detailUrl = $data['url'] ?? route('profile.settings.verification');
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                            } elseif ($type === 'rating_received') {
                                $titleText = $data['title'] ?? 'Ulasan & Rating Baru';
                                $detailUrl = route('mitra.ratings');
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                            } elseif ($type === 'help_request' || str_contains($type, 'help')) {
                                $titleText = $data['title'] ?? 'Permintaan Bantuan';
                                $detailUrl = $helpId ? route('mitra.helps.detail', $helpId) : ($data['url'] ?? '#');
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                            } elseif ($type === 'withdraw' || str_contains($type, 'withdraw')) {
                                $titleText = $data['title'] ?? 'Status Penarikan Saldo';
                                $detailUrl = route('mitra.withdraw.history');
                            } elseif ($type === 'report_status') {
                                $titleText = $data['title'] ?? 'Update Status Laporan Aduan';
                                $detailUrl = isset($data['report_id']) ? route('mitra.reports.show', $data['report_id']) : ($data['url'] ?? '#');
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Status laporan aduan Anda telah diperbarui oleh Admin.');
                            } else {
                                $titleText = $data['title'] ?? 'Notifikasi';
                                $detailUrl = $data['url'] ?? '#';
                                $bodyText = $data['message'] ?? ($data['body'] ?? 'Notifikasi baru');
                            }
                        @endphp

                        <div wire:key="notif-{{ $notification->id }}" 
                            class="rounded-2xl border transition shadow-2xs overflow-hidden relative p-4 {{ $isSanction ? 'border-red-200 bg-red-50/40 hover:border-red-300' : ($isUnread ? 'border-blue-200 bg-blue-50/20' : 'border-gray-200 bg-white hover:border-blue-300') }}">

                            <div class="flex items-start gap-3">
                                @if($isSanction)
                                    <div class="flex-shrink-0 mt-1" title="Surat Peringatan resmi tidak dapat dihapus">
                                        <div class="w-4 h-4 flex items-center justify-center text-red-400">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $notification->id }}" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 cursor-pointer" aria-label="Pilih notifikasi">
                                    </div>
                                @endif

                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border {{ $isSanction ? 'border-red-200 bg-red-100/70 text-red-600' : ($type === 'ktp_approved' ? 'border-green-200 bg-green-50' : ($type === 'ktp_rejected' ? 'border-red-200 bg-red-50' : (str_contains($type, 'ktp') ? 'border-amber-200 bg-amber-50' : ($isUnread ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50')))) }}">
                                        @if($isSanction)
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        @elseif($type === 'chat_message')
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        @elseif($type === 'ktp_approved')
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($type === 'ktp_rejected')
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif(str_contains($type, 'ktp') || str_contains($type, 'verification'))
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                            </svg>
                                        @elseif($type === 'rating_received')
                                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
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
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h3 class="text-sm font-semibold {{ $isSanction ? 'text-red-900' : 'text-gray-900' }}">{{ $titleText }}</h3>
                                                @if($isSanction)
                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                        Sanksi
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(isset($data['help_amount']))
                                            <div class="ml-2 text-right">
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold">Rp {{ number_format($data['help_amount'],0,',','.') }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <p class="text-xs {{ $isSanction ? 'text-red-800/80 font-medium' : 'text-gray-600' }} mt-1.5 leading-relaxed line-clamp-2">{{ $bodyText }}</p>

                                    @if(isset($data['from_name']) || isset($data['customer_name']))
                                        <div class="text-xs text-gray-400 mt-1.5">Dari: {{ $data['from_name'] ?? $data['customer_name'] ?? '-' }}</div>
                                    @endif

                                    <div class="flex items-center gap-3 mt-3 pt-2 border-t {{ $isSanction ? 'border-red-100' : 'border-gray-100' }}">
                                        @if($isSanction)
                                            <a href="{{ $detailUrl }}" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Lihat Detail Surat &rarr;</a>
                                            @if($isUnread)
                                                <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-medium text-gray-500 hover:underline">Tandai Dibaca</button>
                                            @endif
                                            <span class="text-[10px] font-medium text-red-400 ml-auto flex items-center gap-1">
                                                <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                                Tidak dapat dihapus
                                            </span>
                                        @else
                                            @if(isset($detailUrl) && $detailUrl !== '#')
                                                <button type="button" wire:click="readAndRedirect('{{ $notification->id }}', '{{ $detailUrl }}')" class="text-xs font-semibold text-blue-600 hover:underline text-left">Lihat Detail &rarr;</button>
                                            @endif
                                            @if($isUnread)
                                                <button type="button" wire:click="markAsRead('{{ $notification->id }}')" class="text-xs font-semibold text-emerald-600 hover:underline">Tandai Dibaca</button>
                                            @endif
                                            <button type="button" wire:click="deleteNotification('{{ $notification->id }}')" class="text-xs font-semibold text-red-500 hover:underline ml-auto">Hapus</button>
                                        @endif
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
