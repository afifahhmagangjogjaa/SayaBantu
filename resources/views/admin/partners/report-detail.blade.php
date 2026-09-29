@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <!-- Top Back Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:px-6 sm:py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.partners.report') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Laporan</span>
                </a>
            </div>
            <div class="text-xs text-gray-400">
                ID Aduan: <span class="font-mono font-semibold text-gray-700">#{{ $report->id }}</span>
            </div>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 bg-emerald-100 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 bg-red-100 text-red-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Utama -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Laporan</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Judul</dt>
                            <dd class="text-sm text-gray-900 font-medium">{{ $report->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Pesan</dt>
                            <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $report->message }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jenis Laporan</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $report->report_type_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori</dt>
                                <dd>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $report->isFromCustomer() ? 'bg-primary-100 text-primary-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $report->category_label }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                            <dd>
                                @if ($report->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Pending
                                    </span>
                                @elseif ($report->status === 'in_progress')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        In Progress
                                    </span>
                                @elseif ($report->status === 'resolved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Resolved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        Dismissed
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Dibuat</dt>
                            <dd class="text-sm text-gray-700">{{ $report->created_at->format('d F Y, H:i') }} WIB</dd>
                        </div>
                        @if ($report->resolved_at)
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Diselesaikan</dt>
                                <dd class="text-sm text-gray-700">{{ $report->resolved_at->format('d F Y, H:i') }} WIB</dd>
                                @if ($report->resolvedBy)
                                    <dd class="text-xs text-gray-500 mt-1">Oleh: {{ $report->resolvedBy->name }}</dd>
                                @endif
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Informasi Reporter -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Reporter</h2>
                    @if ($report->reporter)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('admin.users.show', $report->reporter) }}"
                                    class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                    {{ $report->reporter->name }}
                                </a>
                                <div class="text-sm text-gray-500 mt-1">{{ $report->reporter->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Role: <span class="font-semibold">{{ ucfirst($report->reporter->role) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $report->reporter) }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                Lihat Profil
                            </a>
                        </div>
                    @elseif ($report->user)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('admin.users.show', $report->user) }}"
                                    class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                    {{ $report->user->name }}
                                </a>
                                <div class="text-sm text-gray-500 mt-1">{{ $report->user->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Role: <span class="font-semibold">{{ ucfirst($report->user->role) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $report->user) }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                Lihat Profil
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Informasi reporter tidak tersedia.</p>
                    @endif
                </div>

                <!-- Informasi User/Help yang Dilaporkan -->
                @if ($report->reportedUser || $report->reportedHelp || $report->reported_user_text || $report->reported_help_text)
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Yang Dilaporkan</h2>
                        @if ($report->reportedUser)
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                        class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                        {{ $report->reportedUser->name }}
                                    </a>
                                    <div class="text-sm text-gray-500 mt-1">{{ $report->reportedUser->email }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Role: <span class="font-semibold">{{ ucfirst($report->reportedUser->role) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                    class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                    Lihat Profil
                                </a>
                            </div>
                        @elseif ($report->reported_user_text)
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <span class="text-xs text-gray-500 font-semibold uppercase">Pihak / Pengguna:</span>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $report->reported_user_text }}</p>
                            </div>
                        @endif
                        @if ($report->reportedHelp)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">Bantuan #{{ $report->reportedHelp->id }}</div>
                                        <div class="text-sm text-gray-500 mt-1">{{ $report->reportedHelp->title }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $report->reportedHelp->status)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($report->reported_help_text)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <span class="text-xs text-gray-500 font-semibold uppercase">Bantuan Terkait:</span>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $report->reported_help_text }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Tinjau Riwayat Chat Customer & Mitra -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5" x-data="{
                    isOpen: true,
                    isExpanded: false,
                    showScrollBtn: false,
                    scrollToBottom(smooth = true) {
                        const container = this.$refs.chatContainer;
                        if (container) {
                            container.scrollTo({
                                top: container.scrollHeight,
                                behavior: smooth ? 'smooth' : 'instant'
                            });
                        }
                    },
                    handleScroll() {
                        const c = this.$refs.chatContainer;
                        if (!c) return;
                        this.showScrollBtn = (c.scrollHeight - c.scrollTop - c.clientHeight) > 80;
                    }
                }" x-init="$nextTick(() => { scrollToBottom(false); });">
                    
                    <div class="flex items-center justify-between cursor-pointer select-none" @click="isOpen = !isOpen">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-semibold text-gray-900">Tinjau Percakapan (Chat Log)</h2>
                                    <span class="px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700 rounded-md">
                                        {{ $chats ? $chats->count() : 0 }} Pesan
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">Bukti riwayat percakapan antara Customer dan Mitra</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5" @click.stop>
                            @if ($chats && $chats->count() > 0)
                                <button type="button" 
                                    x-show="isOpen"
                                    @click="isExpanded = !isExpanded" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                                    :title="isExpanded ? 'Kecilkan Tampilan' : 'Perbesar Tampilan'">
                                    <span x-text="isExpanded ? 'Kecilkan' : 'Perbesar'">Perbesar</span>
                                </button>
                            @endif
                            <button type="button" @click="isOpen = !isOpen" 
                                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"
                                :title="isOpen ? 'Sembunyikan' : 'Buka'">
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if ($chats && $chats->count() > 0)
                        <div x-show="isOpen" x-transition class="relative mt-3.5">
                            <div x-ref="chatContainer" 
                                @scroll.passive="handleScroll()"
                                :style="isExpanded ? 'max-height: 460px; overflow-y: auto;' : 'max-height: 220px; overflow-y: auto;'"
                                style="max-height: 220px; overflow-y: auto;"
                                class="bg-gray-50/80 rounded-xl p-3 border border-gray-100 space-y-2 scroll-smooth transition-all duration-200">
                                @php $lastDate = null; @endphp
                                @foreach ($chats as $chat)
                                    @php
                                        $isCustomer = $chat->sender_type === 'customer';
                                        $senderName = $isCustomer 
                                            ? ($chat->customer?->name ?? 'Customer') 
                                            : ($chat->mitra?->name ?? 'Mitra');
                                        $senderRole = $isCustomer ? 'Customer' : 'Mitra';
                                        $chatDate = $chat->created_at ? $chat->created_at->format('d M Y') : null;
                                        $showDateDivider = $chatDate && ($chatDate !== $lastDate);
                                        $lastDate = $chatDate;
                                    @endphp

                                    @if ($showDateDivider)
                                        <div class="flex items-center justify-center my-1.5">
                                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-200/80 text-gray-600 rounded-full select-none shadow-2xs">
                                                {{ $chatDate }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex {{ $isCustomer ? 'justify-start' : 'justify-end' }}">
                                        <div class="max-w-[80%] {{ $isCustomer ? 'bg-white text-gray-800 border border-gray-200/90 rounded-2xl rounded-tl-xs' : 'bg-blue-600 text-white rounded-2xl rounded-tr-xs' }} px-3 py-1.5 shadow-xs">
                                            <div class="flex items-center gap-1.5 mb-0.5 leading-none">
                                                <span class="text-[11px] font-bold {{ $isCustomer ? 'text-blue-600' : 'text-blue-100' }}">
                                                    {{ $senderName }}
                                                </span>
                                                <span class="text-[9px] px-1 py-0.2 rounded font-semibold {{ $isCustomer ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-white/20 text-white' }}">
                                                    {{ $senderRole }}
                                                </span>
                                            </div>
                                            <p class="text-xs leading-snug whitespace-pre-wrap break-words">{{ $chat->message }}</p>
                                            <div class="flex items-center justify-end gap-1 mt-0.5 text-[9px] {{ $isCustomer ? 'text-gray-400' : 'text-blue-200' }} leading-none">
                                                <span title="{{ $chat->created_at ? $chat->created_at->format('d M Y, H:i') : '' }}">
                                                    {{ $chat->created_at ? $chat->created_at->format('H:i') : '-' }}
                                                </span>
                                                @if ($chat->read_at)
                                                    <span title="Dibaca {{ $chat->read_at ? $chat->read_at->format('H:i') : '' }}" class="flex items-center">
                                                        <svg class="w-2.5 h-2.5 {{ $isCustomer ? 'text-emerald-500' : 'text-emerald-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Tombol Melayang Scroll ke Bawah (Pesan Terbaru) -->
                            <button type="button" 
                                x-show="showScrollBtn" 
                                x-transition
                                @click="scrollToBottom(true)"
                                class="absolute bottom-3 right-3 z-10 flex items-center gap-1 px-2.5 py-1 bg-gray-900/85 hover:bg-gray-900 text-white text-[11px] font-semibold rounded-full shadow-lg backdrop-blur-xs transition">
                                <span>Terbaru</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </button>
                        </div>
                    @else
                        <div x-show="isOpen" x-transition class="p-6 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200 mt-3.5">
                            <p class="text-xs font-medium text-gray-500">Tidak ada riwayat chat terkait</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-6">
                <!-- Update Status -->
                @php
                    $isResolved = $report->status === 'resolved';
                    $isDismissed = $report->status === 'dismissed';
                    $isClosed = $isResolved || $isDismissed;
                @endphp
                <div class="bg-white rounded-2xl shadow-md border {{ $isResolved ? 'border-emerald-200' : ($isDismissed ? 'border-gray-200' : 'border-gray-200') }} p-5 sm:p-6"
                    x-data="{ showForm: {{ $isClosed ? 'false' : 'true' }} }">
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 {{ $isResolved ? 'bg-emerald-100 text-emerald-600' : ($isDismissed ? 'bg-gray-100 text-gray-600' : ($report->status === 'in_progress' ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600')) }} rounded-lg">
                                @if ($isResolved)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif ($isDismissed)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @endif
                            </span>
                            <h2 class="text-base font-semibold text-gray-900">Status Penanganan</h2>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $isResolved ? 'bg-emerald-100 text-emerald-700' : ($isDismissed ? 'bg-gray-100 text-gray-700' : ($report->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                            @if ($report->status === 'pending')
                                Pending
                            @elseif ($report->status === 'in_progress')
                                In Progress
                            @elseif ($report->status === 'resolved')
                                Resolved
                            @else
                                Dismissed
                            @endif
                        </span>
                    </div>

                    @if ($isResolved)
                        <!-- Card Ringkasan Selesai -->
                        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl mb-3 text-xs text-emerald-900 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Aduan telah selesai ditangani
                            </div>
                            @if ($report->resolved_at)
                                <div class="text-emerald-700">Waktu: <span class="font-medium text-emerald-900">{{ $report->resolved_at->format('d F Y, H:i') }} WIB</span></div>
                            @endif
                            @if ($report->resolvedBy)
                                <div class="text-emerald-700">Oleh: <span class="font-medium text-emerald-900">{{ $report->resolvedBy->name }}</span></div>
                            @endif
                        </div>
                    @elseif ($isDismissed)
                        <!-- Card Ringkasan Ditutup -->
                        <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl mb-3 text-xs text-gray-700 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-gray-800">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Aduan telah ditutup / ditolak
                            </div>
                            <p class="text-gray-500 text-[11px]">Laporan tidak dilanjutkan atau dianggap tidak valid.</p>
                        </div>
                    @endif

                    @if ($isClosed)
                        <button type="button" @click="showForm = !showForm"
                            class="w-full mb-2.5 py-2 px-3 text-xs font-semibold rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-1.5 transition">
                            <span x-text="showForm ? 'Tutup Pengaturan Status' : 'Ubah Status / Buka Kembali Laporan'">Ubah Status / Buka Kembali Laporan</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    @endif

                    <div x-show="showForm" x-transition>
                        <form method="POST" action="{{ route('admin.partners.reports.update', $report) }}">
                            @csrf
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilih Status Penanganan:</label>
                            <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-2.5 bg-gray-50">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu Peninjauan (Pending)</option>
                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Ditangani (In Progress)</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai Ditangani (Resolved)</option>
                                <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Tolak / Tutup Laporan (Dismissed)</option>
                            </select>
                            <button type="submit"
                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                                Simpan Perubahan Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Catatan Admin -->
                @php
                    $notesRaw = trim($report->admin_notes ?? '');
                    $noteLines = $notesRaw !== '' ? array_filter(explode("\n", $notesRaw), fn($line) => trim($line) !== '') : [];
                @endphp
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </span>
                            <h2 class="text-base font-semibold text-gray-900">Catatan Internal Admin</h2>
                        </div>
                        @if (!empty($noteLines))
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 rounded-md">
                                {{ count($noteLines) }} Catatan
                            </span>
                        @endif
                    </div>

                    <!-- Riwayat Penanganan (Read-Only) -->
                    <div class="space-y-2">
                        @if (!empty($noteLines))
                            <div class="space-y-2 pr-1" style="max-height: 260px; overflow-y: auto;">
                                @foreach ($noteLines as $line)
                                    @php
                                        $isSanctionLog = str_contains($line, 'memberikan sanksi') || str_contains($line, 'mencabut');
                                    @endphp
                                    <div class="p-2.5 rounded-xl border {{ $isSanctionLog ? 'bg-red-50/60 border-red-100 text-red-900' : 'bg-gray-50 border-gray-100 text-gray-800' }} text-xs leading-relaxed">
                                        <div class="flex items-start gap-1.5">
                                            <span class="shrink-0 mt-0.5">
                                                @if ($isSanctionLog)
                                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                @endif
                                            </span>
                                            <div class="flex-1 break-words">{{ trim($line) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Belum ada riwayat penanganan.</p>
                        @endif
                    </div>
                </div>

                <!-- Beri Sanksi (SP / Banned) -->
                @php
                    $targetOptions = collect([
                        $report->reportedUser ? ['user' => $report->reportedUser, 'label' => 'Terlapor: ' . $report->reportedUser->name . ' (' . ucfirst($report->reportedUser->role) . ')'] : null,
                        $report->reporter ? ['user' => $report->reporter, 'label' => 'Pelapor: ' . $report->reporter->name . ' (' . ucfirst($report->reporter->role) . ')'] : null,
                    ])->filter();
                @endphp

                @if ($targetOptions->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-md border border-red-100 p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="p-1.5 bg-red-100 text-red-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <h2 class="text-lg font-semibold text-gray-900">Tindakan Sanksi (SP)</h2>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Berikan Surat Peringatan (SP 1 - 3). Akun dengan SP 3 akan otomatis diblokir/banned dari sistem.</p>

                        <form method="POST" action="{{ route('admin.partners.reports.sanction', $report) }}" onsubmit="return confirm('Apakah Anda yakin ingin memproses sanksi ini?');">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target Pengguna:</label>
                                    <select name="user_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50">
                                        @foreach ($targetOptions as $option)
                                            <option value="{{ $option['user']->id }}">
                                                {{ $option['label'] }} (Status: SP {{ $option['user']->warning_level ?? 0 }}{{ ($option['user']->is_banned || $option['user']->status === 'blocked') ? ' - Banned' : '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Sanksi / Level SP:</label>
                                    <select name="warning_level" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50">
                                        <option value="1">SP 1 (Peringatan Ringan)</option>
                                        <option value="2">SP 2 (Peringatan Sedang)</option>
                                        <option value="3">SP 3 (Peringatan Berat & Blokir/Banned)</option>
                                        <option value="0">Reset / Cabut SP (Kembali ke Normal)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Sanksi (Opsional):</label>
                                    <input type="text" name="sanction_reason" placeholder="Contoh: Terbukti melanggar aturan layanan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                </div>

                                <button type="submit"
                                    class="w-full mt-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                    Terapkan Sanksi
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

