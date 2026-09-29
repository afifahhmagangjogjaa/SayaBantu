<div class="min-h-screen bg-white" wire:poll.10s>
    <style>
        .header-pattern {
            position: relative;
            overflow: hidden;
        }
        .header-pattern::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .header-pattern::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
    </style>

    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden header-pattern" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <button onclick="window.history.back()" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold">Pekerjaan Saya</h1>
                        <p class="text-xs text-white/90 mt-0.5">Kelola tugas aktif & riwayat pesanan</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('mitra.notifications.index')])
                    </div>
                </div>

                <!-- 3 Tabs Utama: Sedang Dikerjakan, Selesai, Dibatalkan -->
                <div class="mt-3 flex items-center justify-between gap-1.5 p-1 bg-white/15 backdrop-blur-md rounded-full shadow-inner">
                    <button type="button" wire:click="switchTab('diproses')"
                        class="flex-1 text-center py-1.5 px-2 rounded-full text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $tab === 'diproses' ? 'bg-white text-red-600 shadow-md' : 'text-white hover:bg-white/20' }}">
                        <span>Dikerjakan</span>
                        @if($processingCount > 0)
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        @endif
                    </button>

                    <button type="button" wire:click="switchTab('selesai')"
                        class="flex-1 text-center py-1.5 px-2 rounded-full text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $tab === 'selesai' ? 'bg-white text-[#0098e7] shadow-md' : 'text-white hover:bg-white/20' }}">
                        <span>Selesai</span>
                    </button>

                    <button type="button" wire:click="switchTab('dibatalkan')"
                        class="flex-1 text-center py-1.5 px-2 rounded-full text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $tab === 'dibatalkan' ? 'bg-white text-red-600 shadow-md' : 'text-white hover:bg-white/20' }}">
                        <span>Dibatalkan</span>
                    </button>
                </div>
            </div>

            <!-- Curved separator (SVG) -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24 min-h-[60vh]">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-100 rounded-lg text-green-700 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search Bar -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari bantuan, pelanggan, atau lokasi..."
                        class="w-full pl-9 pr-9 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#0098e7] focus:bg-white focus:outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(!empty($search))
                        <button type="button" wire:click="$set('search', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1" title="Hapus pencarian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Empty State -->
            @if($helps->isEmpty())
                <div class="text-center py-16 bg-gray-50/60 rounded-2xl border border-gray-100 p-6">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white flex items-center justify-center shadow-sm">
                        @if(!empty($search))
                            <span class="text-2xl">🔍</span>
                        @elseif($tab === 'diproses')
                            <span class="text-2xl">⚡</span>
                        @elseif($tab === 'selesai')
                            <span class="text-2xl">🎉</span>
                        @else
                            <span class="text-2xl">📋</span>
                        @endif
                    </div>
                    @if(!empty($search))
                        <h3 class="text-sm font-bold text-gray-800">Tidak Ada Hasil Ditemukan</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">Tidak ditemukan pekerjaan dengan kata kunci "{{ $search }}".</p>
                        <button type="button" wire:click="$set('search', '')" class="inline-flex items-center gap-1.5 mt-3.5 px-3.5 py-1.5 bg-white text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition shadow-2xs">
                            Reset Pencarian
                        </button>
                    @elseif($tab === 'diproses')
                        <h3 class="text-sm font-bold text-gray-800">Tidak Ada Tugas Berjalan</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">Anda belum mengambil tugas baru. Jelajahi menu Cari untuk menemukan bantuan tersedia!</p>
                        <a href="{{ route('mitra.helps.all') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-[#0098e7] text-white text-xs font-semibold rounded-xl shadow-sm hover:bg-[#0086cc] transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Cari Bantuan Tersedia
                        </a>
                    @elseif($tab === 'selesai')
                        <h3 class="text-sm font-bold text-gray-800">Belum Ada Riwayat Selesai</h3>
                        <p class="text-xs text-gray-500 mt-1">Pekerjaan yang telah berhasil Anda selesaikan akan tercatat rapi di sini.</p>
                    @else
                        <h3 class="text-sm font-bold text-gray-800">Tidak Ada Bantuan Dibatalkan</h3>
                        <p class="text-xs text-gray-500 mt-1">Bagus! Belum ada tugas yang dibatalkan atau ditolak.</p>
                    @endif
                </div>
            @else
                <!-- Help Cards List -->
                <div class="space-y-3">
                    @foreach($helps as $help)
                        @php
                            $mitraFeePercent = (float)($help->mitra_fee_percent ?? 10);
                            $baseWage = (float)($help->base_amount > 0 ? $help->base_amount : ($help->amount ?? 0));
                            $netMitra = (float)($help->net_mitra_amount > 0 ? $help->net_mitra_amount : max(0, $baseWage - round($baseWage * $mitraFeePercent / 100)));
                        @endphp
                        
                        <div class="bg-white rounded-xl p-3.5 shadow-xs hover:shadow-md transition-all border border-gray-100 {{ $help->isUrgent() ? 'border-l-4 border-l-red-500' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 flex items-center justify-center">
                                    @if($help->photo)
                                        <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl">
                                            @if($tab === 'dibatalkan')
                                                🚫
                                            @elseif($tab === 'selesai')
                                                ✅
                                            @else
                                                {{ $help->category?->icon ?? '📦' }}
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <h3 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $help->title }}</h3>
                                            @if($help->isUrgent())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                                            @endif
                                        </div>
                                        <span class="text-xs font-bold whitespace-nowrap text-emerald-600">
                                            Rp {{ number_format($netMitra, 0, ',', '.') }}
                                            <span class="text-[10px] text-gray-400 font-normal">(Netto)</span>
                                        </span>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        @if($tab === 'diproses')
                                            @if($help->status === 'waiting_customer_confirmation')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    ⏳ Menunggu Konfirmasi Customer
                                                </span>
                                                @php
                                                    $mDeadline = $help->getCustomerConfirmationDeadline();
                                                @endphp
                                                @if($mDeadline)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-orange-50 text-orange-600 border border-orange-200" title="Batas waktu 24 jam untuk pencairan otomatis">
                                                        ⚡ Cair otomatis {{ $mDeadline->diffForHumans() }}
                                                    </span>
                                                @endif
                                            @elseif($help->status === 'partner_cancel_requested')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                    ⚠️ Permintaan Batal Diajukan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                    Sedang Berjalan
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400">{{ optional($help->taken_at)->diffForHumans() ?? optional($help->created_at)->diffForHumans() }}</span>
                                        @elseif($tab === 'selesai')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                ✓ Selesai
                                            </span>
                                            @php
                                                $customerRatedCard = \App\Models\Rating::hasRated($help->id, auth()->id(), 'mitra_to_customer');
                                            @endphp
                                            @if($customerRatedCard)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-medium">✓ Dinilai</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-[11px] font-medium">Belum dinilai</span>
                                            @endif
                                            <span class="text-xs text-gray-400">{{ optional($help->service_completed_at ?? $help->updated_at)->format('d M Y') }}</span>
                                        @else
                                            @if(in_array($help->status, ['rejected', 'ditolak']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-100">
                                                    Ditolak Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                                    Dibatalkan
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400">{{ optional($help->updated_at)->diffForHumans() }}</span>
                                        @endif
                                    </div>

                                    @if($tab === 'dibatalkan')
                                        @if(in_array($help->status, ['rejected', 'ditolak']) && $help->admin_notes)
                                            <div class="mt-1 mb-2 bg-red-50 rounded-lg px-2.5 py-1.5 border border-red-100 text-xs">
                                                <span class="font-semibold text-red-700">Alasan Admin:</span>
                                                <span class="text-red-800">{{ $help->admin_notes }}</span>
                                            </div>
                                        @elseif(!empty($help->customer_cancel_reason))
                                            <div class="mt-1 mb-2 bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-100 text-xs">
                                                <span class="font-semibold text-gray-700">Alasan Customer:</span>
                                                <span class="text-gray-600">{{ $help->customer_cancel_reason }}</span>
                                            </div>
                                        @elseif(!empty($help->partner_cancel_reason))
                                            <div class="mt-1 mb-2 bg-amber-50 rounded-lg px-2.5 py-1.5 border border-amber-100 text-xs">
                                                <span class="font-semibold text-amber-700">Alasan Mitra:</span>
                                                <span class="text-amber-800">{{ $help->partner_cancel_reason }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    @php
                                        $displayDate = $help->scheduled_at ?? $help->created_at;
                                    @endphp
                                    @if($displayDate)
                                        <div class="text-xs text-gray-500 mb-1.5 flex items-center gap-1.5">
                                            <span>📅 {{ \Carbon\Carbon::parse($displayDate)->translatedFormat('d M Y, H:i') }}</span>
                                            @if($help->isUrgent())
                                                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">Urgent</span>
                                            @elseif($help->scheduled_at)
                                                <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Terjadwal</span>
                                            @endif
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-600 line-clamp-1 mb-2">📍 {{ $help->location ?? optional($help->city)->name ?? '-' }}</p>

                                    <!-- Bottom Action Strip -->
                                    <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                                        <span class="text-xs text-gray-500 truncate max-w-[150px]">👤 {{ optional($help->user)->name ?? 'Customer' }}</span>
                                        <div class="flex items-center gap-1.5">
                                            @if($tab === 'diproses')
                                                <a href="{{ route('mitra.chat', ['help' => $help->id]) }}" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-[#0098e7] hover:bg-blue-100 border border-blue-100 transition active:scale-95 shadow-xs" 
                                                    title="Chat Customer" aria-label="Chat Customer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('mitra.helps.detail', $help->id) }}" class="inline-flex items-center justify-center px-3.5 h-8 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition active:scale-95">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-5">
                    {{ $helps->links() }}
                </div>
            @endif
        </div>
    </div>
</div>