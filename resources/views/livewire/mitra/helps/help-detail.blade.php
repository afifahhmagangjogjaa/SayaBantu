<div class="min-h-screen bg-gray-50"
    @if(!$showCompletionModal) wire:poll.3s="loadHelp" @endif
    x-data="{ 
        showNotification: false, 
        notificationMessage: '',
        previousStatus: '{{ $help->status }}'
    }"
    x-init="
        // Update status setiap kali Livewire refresh
        Livewire.hook('morph.updated', () => {
            const currentStatus = '{{ $help->status }}';
            
            // Jika status berubah, tampilkan notifikasi
            if (previousStatus !== currentStatus) {
                console.log('📍 Status berubah:', {
                    old: previousStatus,
                    new: currentStatus
                });
                
                notificationMessage = 'Status pesanan diperbarui';
                showNotification = true;
                setTimeout(() => showNotification = false, 5000);
                
                previousStatus = currentStatus;
            }
        });
    "
    @show-status-notification.window="
        notificationMessage = $event.detail.message;
        showNotification = true;
        setTimeout(() => showNotification = false, 5000);
    "
>
    {{-- Status Notification --}}
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-sm w-full px-4"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900" x-text="notificationMessage"></p>
                    <p class="text-xs text-gray-500 mt-0.5">Status pesanan diperbarui</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Header - BRImo Style --}}
    <div class="px-5 pt-5 pb-8 relative overflow-hidden"
        style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

        <div class="relative z-10 max-w-md mx-auto">
            <div class="flex items-center justify-between text-white mb-6">
                <button onclick="window.history.back()" aria-label="Kembali"
                    class="p-2 hover:bg-white/20 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-bold">Detail Pesanan</h1>
                    <p class="text-xs text-white/90 mt-0.5">Informasi lengkap pesanan Anda</p>
                </div>

                <div class="w-9"></div>
            </div>
        </div>

        <!-- Curved separator -->
        <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none"
            aria-hidden="true">
            <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f9fafb"></path>
        </svg>
    </div>

    <!-- Content -->
    <div class="bg-gray-50 -mt-6 px-5 pt-6 pb-28 max-w-md mx-auto">
        {{-- GPS Tracker - Auto tracking untuk status aktif --}}
        @if (in_array($help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way', 'partner_arrived']))
            {{-- <div class="mb-3">
                <livewire:mitra.gps-tracker :helpId="$help->id" :key="'gps-tracker-'.$help->id" />
            </div> --}}
        @endif

        @if (session('message'))
            <div
                class="mb-4 p-3 bg-green-50 border border-green-100 rounded-lg text-green-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('message') }}
            </div>
        @endif

        {{-- Service Info --}}
        <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
            <div class="flex items-start gap-3">
                <div class="w-14 h-14 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-3xl">
                    @if ($help->photo)
                        <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}"
                            class="w-full h-full object-cover rounded-lg">
                    @else
                        {{ $help->category?->icon ?? '📦' }}
                    @endif
                </div>
                <div class="flex-1">
                    <div class="text-xs font-semibold text-blue-600 mb-0.5">{{ $help->category?->name ?? 'Lainnya' }}</div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-semibold text-base text-gray-900 leading-tight">{{ $help->title }}</h2>
                        @if($help->isUrgent())
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 flex-shrink-0">⚡ URGENT</span>
                        @endif
                    </div>
                    @php
                        $mitraFeePercent = (float)($help->mitra_fee_percent ?? 10);
                        $baseWage = (float)($help->base_amount > 0 ? $help->base_amount : $help->amount);
                        $platformFee = (float)($help->mitra_fee_amount > 0 ? $help->mitra_fee_amount : round(($baseWage * $mitraFeePercent) / 100));
                        $netMitra = (float)($help->net_mitra_amount > 0 ? $help->net_mitra_amount : max(0, $baseWage - $platformFee));
                    @endphp
                    <div class="mt-1">
                        <span class="text-xs text-gray-500 font-medium">Pendapatan Bersih Mitra:</span>
                        <div class="flex items-center gap-2">
                            <p class="text-lg font-bold text-emerald-600">
                                Rp {{ number_format($netMitra, 0, ',', '.') }}
                            </p>
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">
                                Netto (-{{ $mitraFeePercent }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Breakdown Biaya Platform Mitra (Transparan) --}}
            <div class="mt-3 pt-3 border-t border-gray-100 bg-emerald-50/50 rounded-xl p-3.5 border border-emerald-100 text-xs space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-emerald-100/80">
                    <span class="font-bold text-gray-800 text-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Rincian Transparansi Upah
                    </span>
                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full">
                        Potongan {{ $mitraFeePercent }}%
                    </span>
                </div>

                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between text-gray-600">
                        <span>Upah Pokok dari Customer:</span>
                        <span class="font-semibold text-gray-900">
                            Rp {{ number_format($baseWage, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-red-600 font-medium">
                        <span>Biaya Platform ({{ $mitraFeePercent }}%):</span>
                        <span class="font-semibold">
                            - Rp {{ number_format($platformFee, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-bold border-t border-emerald-200/60 pt-1.5">
                        <span>Pendapatan Bersih Rekan Jasa:</span>
                        <span class="text-sm font-extrabold text-emerald-600">
                            Rp {{ number_format($netMitra, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="p-2.5 bg-white/90 rounded-lg border border-emerald-100 text-[11px] text-gray-600 flex items-start gap-2 leading-relaxed">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>
                        <strong>Keterangan:</strong> Upah dipotong <strong>{{ $mitraFeePercent }}%</strong> untuk biaya operasional platform. Saldo bersih sebesar <strong>Rp {{ number_format($netMitra, 0, ',', '.') }}</strong> akan otomatis masuk ke saldo akun Anda begitu pesanan diselesaikan.
                    </span>
                </div>
            </div>

            {{-- Order ID --}}
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-600">ID: <span class="font-semibold text-gray-900">{{ $help->order_id }}</span></span>
                <button wire:click="copyOrderId" class="text-blue-500 text-sm font-semibold flex items-center gap-1 cursor-pointer">
                    Salin
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Status Card (Rapi, Elegan, Bukan Mirip Tombol) --}}
        @php
            $status = $help->status;
            $statusLabel = match($status) {
                'menunggu_pembayaran' => 'Menunggu Pembayaran',
                'mencari_mitra', 'menunggu_mitra' => 'Mencari Mitra',
                'memperoleh_mitra' => 'Pesanan Diterima',
                'taken' => 'Pesanan Diambil',
                'partner_on_the_way' => 'Dalam Perjalanan Menuju Lokasi',
                'partner_arrived' => 'Tiba di Lokasi Customer',
                'in_progress', 'sedang_diproses' => 'Sedang Dikerjakan',
                'waiting_customer_confirmation' => 'Menunggu Konfirmasi Customer',
                'komplain', 'disputed' => 'Dalam Mediasi Komplain',
                'selesai', 'completed' => ($help->complaint_resolution === 'rejected' ? 'Selesai (Komplain Ditolak)' : 'Pekerjaan Selesai'),
                'dibatalkan', 'cancelled' => ($help->complaint_resolution === 'refunded' ? 'Dibatalkan (Hasil Mediasi Refund)' : 'Pesanan Dibatalkan'),
                'partner_cancel_requested' => 'Permintaan Pembatalan Diajukan',
                default => ucfirst(str_replace('_', ' ', $status)),
            };

            $mitraStatusTheme = match($status) {
                'menunggu_pembayaran' => [
                    'card' => 'bg-amber-50/70 border-amber-200/80',
                    'icon_bg' => 'bg-amber-100 text-amber-600',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'dot' => 'bg-amber-500',
                    'badge_label' => 'Menunggu',
                    'pulse' => true,
                ],
                'memperoleh_mitra', 'taken' => [
                    'card' => 'bg-blue-50/70 border-blue-200/80',
                    'icon_bg' => 'bg-blue-100 text-blue-600',
                    'badge' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'dot' => 'bg-blue-600',
                    'badge_label' => 'Diambil',
                    'pulse' => true,
                ],
                'partner_on_the_way' => [
                    'card' => 'bg-indigo-50/70 border-indigo-200/80',
                    'icon_bg' => 'bg-indigo-100 text-indigo-600',
                    'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'dot' => 'bg-indigo-600',
                    'badge_label' => 'Menuju Lokasi',
                    'pulse' => true,
                ],
                'partner_arrived' => [
                    'card' => 'bg-emerald-50/70 border-emerald-200/80',
                    'icon_bg' => 'bg-emerald-100 text-emerald-600',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'dot' => 'bg-emerald-600',
                    'badge_label' => 'Tiba',
                    'pulse' => false,
                ],
                'in_progress', 'sedang_diproses' => [
                    'card' => 'bg-sky-50/70 border-sky-200/80',
                    'icon_bg' => 'bg-sky-100 text-sky-600',
                    'badge' => 'bg-sky-100 text-sky-800 border-sky-200',
                    'dot' => 'bg-sky-600',
                    'badge_label' => 'Proses',
                    'pulse' => true,
                ],
                'waiting_customer_confirmation' => [
                    'card' => 'bg-orange-50/70 border-orange-200/80',
                    'icon_bg' => 'bg-orange-100 text-orange-600',
                    'badge' => 'bg-orange-100 text-orange-800 border-orange-200',
                    'dot' => 'bg-orange-600',
                    'badge_label' => 'Konfirmasi',
                    'pulse' => true,
                ],
                'komplain', 'disputed' => [
                    'card' => 'bg-amber-50/70 border-amber-200/80',
                    'icon_bg' => 'bg-amber-100 text-amber-600',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'dot' => 'bg-amber-500',
                    'badge_label' => 'Mediasi Komplain',
                    'pulse' => true,
                ],
                'selesai', 'completed' => [
                    'card' => 'bg-emerald-50/70 border-emerald-200/80',
                    'icon_bg' => 'bg-emerald-100 text-emerald-600',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'dot' => 'bg-emerald-600',
                    'badge_label' => 'Selesai',
                    'pulse' => false,
                ],
                'dibatalkan', 'cancelled' => [
                    'card' => 'bg-rose-50/70 border-rose-200/80',
                    'icon_bg' => 'bg-rose-100 text-rose-600',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'dot' => 'bg-rose-500',
                    'badge_label' => 'Batal',
                    'pulse' => false,
                ],
                'partner_cancel_requested' => [
                    'card' => 'bg-amber-50/70 border-amber-200/80',
                    'icon_bg' => 'bg-amber-100 text-amber-600',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'dot' => 'bg-amber-500',
                    'badge_label' => 'Pengajuan Batal',
                    'pulse' => true,
                ],
                default => [
                    'card' => 'bg-gray-50 border-gray-200',
                    'icon_bg' => 'bg-gray-100 text-gray-600',
                    'badge' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'dot' => 'bg-gray-500',
                    'badge_label' => 'Status',
                    'pulse' => false,
                ]
            };
        @endphp

        <div class="mt-3 p-3 rounded-2xl border {{ $mitraStatusTheme['card'] }} shadow-xs bg-white">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-xl {{ $mitraStatusTheme['icon_bg'] }} flex items-center justify-center shrink-0">
                        @if(in_array($help->status, ['memperoleh_mitra', 'taken']))
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($help->status === 'partner_on_the_way')
                            <svg class="w-4 h-4 animate-bounce" style="animation-duration: 2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        @elseif($help->status === 'partner_arrived')
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif(in_array($help->status, ['in_progress', 'sedang_diproses']))
                            <svg class="w-4 h-4 text-sky-600 animate-spin" style="animation-duration: 4s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @elseif(in_array($help->status, ['selesai', 'completed']))
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block leading-tight">Status Pesanan</span>
                        <span class="text-xs sm:text-sm font-bold text-gray-900 block leading-snug truncate">{{ $statusLabel }}</span>
                    </div>
                </div>

                <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $mitraStatusTheme['badge'] }}">
                    @if($mitraStatusTheme['pulse'])
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $mitraStatusTheme['dot'] }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $mitraStatusTheme['dot'] }}"></span>
                        </span>
                    @else
                        <span class="w-1.5 h-1.5 rounded-full {{ $mitraStatusTheme['dot'] }}"></span>
                    @endif
                    <span>{{ $mitraStatusTheme['badge_label'] }}</span>
                </span>
            </div>
        </div>

        {{-- Banner Status Khusus Mediasi / Komplain (Mitra) --}}
        @if(in_array($help->status, ['komplain', 'disputed']))
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-3 mb-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shrink-0 text-base">
                        ⚠️
                    </div>
                    <div class="flex-1 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-amber-900 text-sm">Pesanan Dalam Mediasi Komplain</h4>
                            <span class="text-[10px] text-amber-700">{{ $help->complaint_submitted_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-amber-800 leading-relaxed">
                            Customer mengajukan komplain penolakan hasil kerja. Tim Admin sedang memediasi bukti dari kedua pihak. Dana imbalan ditahan sementara hingga keputusan diambil.
                        </p>
                        @if($help->complaint_reason)
                            <div class="bg-white p-2.5 rounded-lg border border-amber-200/80 text-gray-800">
                                <span class="font-semibold text-gray-900 block mb-0.5">Alasan Komplain Customer:</span>
                                <p class="italic">"{{ $help->complaint_reason }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($help->complaint_resolution === 'refunded')
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mt-3 mb-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 text-base">
                        ⚖️
                    </div>
                    <div class="flex-1 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-rose-900 text-sm">Hasil Mediasi: Komplain Disetujui (Refund Customer)</h4>
                            <span class="text-[10px] text-rose-600 font-semibold">{{ $help->complaint_resolved_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-rose-800 leading-relaxed">
                            Admin telah menyelesaikan mediasi dengan keputusan <strong>mengembalikan dana penuh ke customer</strong>. Saldo imbalan untuk pesanan ini tidak dicairkan. Saldo dompet Anda saat ini aman dan tidak dipotong/berkurang.
                        </p>
                        @if($help->complaint_admin_notes)
                            <div class="bg-white p-2.5 rounded-lg border border-rose-200/80 text-gray-800">
                                <span class="font-semibold text-gray-900 block mb-0.5">Catatan / Alasan Keputusan Admin:</span>
                                <p class="italic text-gray-700">"{{ $help->complaint_admin_notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($help->complaint_resolution === 'rejected')
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-3 mb-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 text-base">
                        🎉
                    </div>
                    <div class="flex-1 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-emerald-900 text-sm">Hasil Mediasi: Komplain Ditolak & Dana Dicairkan</h4>
                            <span class="text-[10px] text-emerald-600 font-semibold">{{ $help->complaint_resolved_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-emerald-800 leading-relaxed">
                            Admin telah meninjau bukti pekerjaan dan memutuskan <strong>menolak komplain customer</strong>. Pekerjaan Anda dinyatakan selesai dan dana imbalan telah dicairkan ke saldo akun Anda.
                        </p>
                        @if($help->complaint_admin_notes)
                            <div class="bg-white p-2.5 rounded-lg border border-emerald-200/80 text-gray-800">
                                <span class="font-semibold text-gray-900 block mb-0.5">Catatan Putusan Admin:</span>
                                <p class="italic text-gray-700">"{{ $help->complaint_admin_notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif(in_array($help->status, ['dibatalkan', 'cancelled']))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-3 mb-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-xs">
                        <h4 class="font-bold text-red-900 text-sm">Pesanan Dibatalkan</h4>
                        @if($help->customer_cancel_reason)
                            <p class="text-red-700 mt-1">
                                Alasan dari Customer: <span class="font-semibold italic">"{{ $help->customer_cancel_reason }}"</span>
                            </p>
                        @endif
                        <p class="text-red-600 text-[11px] mt-1">Pesanan ini telah dibatalkan dan tidak perlu dilanjutkan.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Payment note removed - moved to customer view per request --}}

        {{-- Schedule --}}
        <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-sm text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jadwal Permintaan
                </h3>
                @if($help->isUrgent())
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700">⚡ URGENT</span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">📅 TERJADWAL</span>
                @endif
            </div>
            <p class="text-sm text-gray-700">
                {{ \Carbon\Carbon::parse($help->scheduled_at ?? $help->created_at)->translatedFormat('l, d F Y') }}
                (Jam {{ \Carbon\Carbon::parse($help->scheduled_at ?? $help->created_at)->format('H:i') }})
            </p>
            @if($help->isUrgent())
                <p class="text-xs text-red-600 font-semibold mt-1">Pesanan mendesak, mohon segera ditindaklanjuti.</p>
            @endif
        </div>

        {{-- Customer Info --}}
        <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-sm text-gray-900">Informasi Customer</h3>
            </div>
            <div class="flex items-center gap-3 mb-3">
                @if ($help->user->profile_photo)
                    <img src="{{ asset('storage/' . $help->user->profile_photo) }}" alt="{{ $help->user->name }}"
                        class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($help->user->name ?? 'C', 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-sm text-gray-900">{{ $help->user->name }}</h4>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $help->city->name ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- @if ($help->user->phone)
                    <a href="tel:{{ $help->user->phone }}"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Telepon</span>
                    </a>
                @endif --}}
                <a href="{{ route('mitra.chat', ['help' => $help->id]) }}"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition overflow-hidden">
                    <div class="relative flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        @php
                            $unreadCount = \App\Models\Chat::where('help_id', $help->id)
                                ->where('sender_type', 'customer')
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1.5 -right-2 flex items-center justify-center min-w-[16px] h-[16px] px-1 text-[9px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white shadow-xs">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Chat</span>
                </a>
            </div>
        </div>

        {{-- Location --}}
        <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
            <h3 class="font-semibold text-sm text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lokasi
            </h3>
            <div class="space-y-2 text-sm">
                @if ($help->location)
                    <p class="font-medium text-gray-900">{{ $help->location }}</p>
                @endif
                @if ($help->full_address)
                    <p class="text-gray-600">{{ $help->full_address }}</p>
                @endif
                @if ($help->latitude && $help->longitude)
                    <a href="https://www.google.com/maps?q={{ $help->latitude }},{{ $help->longitude }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 mt-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Buka Peta
                    </a>
                @endif
            </div>
        </div>

        {{-- Additional Details (equipment, coords, timestamps, voucher) --}}
        <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
            <h3 class="font-semibold text-sm text-gray-900 mb-2">Deskripsi & Detail</h3>

            @if(!empty($help->description))
                <div class="mb-3 text-sm text-gray-700 whitespace-pre-line break-words break-all">{{ $help->description }}</div>
            @endif

            <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                @if(!empty($help->equipment_provided))
                    <div>
                        <div class="text-xs text-gray-500">Perlengkapan</div>
                        <div class="font-semibold break-words break-all">{{ $help->equipment_provided }}</div>
                    </div>
                @endif

                {{-- <div>
                    <div class="text-xs text-gray-500">Koordinat</div>
                    <div class="font-semibold">{{ $help->latitude ?? '-' }}, {{ $help->longitude ?? '-' }}</div>
                </div> --}}

                @if(!empty($help->voucher_code))
                    <div>
                        <div class="text-xs text-gray-500">Voucher</div>
                        <div class="font-semibold text-red-600">{{ $help->voucher_code }} @if($help->discount_amount) ( -Rp{{ number_format($help->discount_amount,0,',','.') }})@endif</div>
                    </div>
                @endif
            </div>

            @if(!empty($help->photo))
                <div class="mt-3">
                    <div class="text-xs text-gray-500">Foto Pesanan</div>
                    <img src="{{ asset('storage/' . $help->photo) }}" alt="Foto bantuan" class="w-full mt-2 rounded-lg object-cover">
                </div>
            @endif

            <div class="mt-3 text-xs text-gray-500">
                <div>Dibuat: {{ \Carbon\Carbon::parse($help->created_at)->translatedFormat('d F Y, H:i') }}</div>
                <div>Terakhir diperbarui: {{ \Carbon\Carbon::parse($help->updated_at)->translatedFormat('d F Y, H:i') }}</div>
                @if(!empty($help->scheduled_at))
                    <div>Jadwal: {{ \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('d F Y, H:i') }}</div>
                @endif
            </div>
        </div>

        {{-- GPS Simulator (toggle via GPS_SIMULATOR env) --}}
        @if (config('app.gps_simulator', true) &&
                $help->mitra_id === auth()->id() &&
                !in_array($help->status, ['selesai', 'dibatalkan']))
            @if($help->canPartnerStartJourney() || !in_array($help->status, ['memperoleh_mitra', 'taken']))
                <div class="mb-3">
                    <livewire:mitra.gps-simulator :help-id="$help->id" :key="'gps-simulator-' . $help->id" />
                </div>
            @endif
        @endif

        {{-- Update Status Section --}}

        @if (in_array($help->status, ['memperoleh_mitra', 'taken']))
            @if ($help->canPartnerStartJourney())
                <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                    <button wire:click="markPartnerStarted"
                        class="w-full py-3.5 text-white rounded-xl font-bold text-sm hover:opacity-95 transition flex items-center justify-center gap-2 shadow-sm"
                        style="background: linear-gradient(135deg, #0098e7, #0077cc); box-shadow: 0 4px 14px rgba(0, 152, 231, 0.35);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Menuju ke Lokasi Customer
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">Klik tombol ini saat Anda mulai berangkat menuju lokasi customer</p>
                </div>
            @else
                @php
                    $schedCarbon = \Carbon\Carbon::parse($help->scheduled_at)->locale('id');
                    $availableAt = $schedCarbon->copy()->subHour();
                    $daysRemaining = (int) now()->startOfDay()->diffInDays($schedCarbon->copy()->startOfDay(), false);
                    $formattedDateWithYear = $schedCarbon->isoFormat('D MMMM Y');
                    $formattedFull = $schedCarbon->isoFormat('dddd, D MMMM Y');

                    if ($availableAt->isToday()) {
                        $availableText = 'Hari ini pukul ' . $availableAt->format('H:i') . ' WIB';
                        $btnAvailableText = 'Tersedia pukul ' . $availableAt->format('H:i') . ' WIB';
                    } elseif ($availableAt->isTomorrow()) {
                        $availableText = 'Besok pukul ' . $availableAt->format('H:i') . ' WIB';
                        $btnAvailableText = 'Tersedia Besok, ' . $availableAt->format('H:i') . ' WIB';
                    } else {
                        $availableText = $availableAt->isoFormat('dddd, D MMMM Y [pukul] H:i') . ' WIB';
                        $btnAvailableText = 'Tersedia ' . $availableAt->isoFormat('D MMM Y, H:i') . ' WIB';
                    }
                @endphp
                <div class="bg-gradient-to-br from-amber-50/70 to-blue-50/40 p-4 rounded-2xl border border-amber-200/80 shadow-xs mb-3 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-amber-900">Pesanan Terjadwal</span>
                                <span class="text-[10px] font-semibold bg-amber-200/70 text-amber-800 px-2 py-0.5 rounded-full">
                                    {{ $daysRemaining > 0 ? "H-{$daysRemaining}" : 'Hari Ini' }}
                                </span>
                            </div>
                            <p class="text-xs text-amber-900/90 mt-1 font-medium">
                                Jadwal: <span class="font-bold text-gray-900">{{ $formattedFull }}</span> (Jam {{ $schedCarbon->format('H:i') }} WIB)
                            </p>
                        </div>
                    </div>

                    <div class="p-3 bg-white/90 rounded-xl border border-amber-200/60 text-xs text-gray-600 leading-relaxed">
                        Pesanan telah berhasil Anda ambil. Tombol <strong>Menuju ke Lokasi</strong> akan aktif pada <strong>H-1 jam sebelum jadwal</strong> (<strong>{{ $availableText }}</strong>) agar Anda dapat tiba tepat waktu.
                    </div>

                    <button type="button" disabled
                        class="w-full py-3 bg-gray-100 border border-gray-200 text-gray-400 rounded-xl font-bold text-sm flex items-center justify-center gap-2 cursor-not-allowed select-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Menuju ke Lokasi ({{ $btnAvailableText }})</span>
                    </button>
                </div>
            @endif
        @endif

        @if ($help->status === 'partner_on_the_way')
            @php
                $distToCust = $this->distanceToCustomer;
                $isWithinArrivalRange = $distToCust !== null && $distToCust <= 50;
            @endphp

            <div class="bg-blue-50 px-4 py-3.5 rounded-xl border border-blue-200 mb-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-xs text-blue-900">🛵 Sedang Menuju Lokasi Customer</h4>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            @if($distToCust !== null)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isWithinArrivalRange ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                    📍 Jarak: {{ $distToCust >= 1000 ? number_format($distToCust / 1000, 1) . ' km' : round($distToCust) . ' meter' }}
                                </span>
                                @if($isWithinArrivalRange)
                                    <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-0.5">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Dalam Radius (≤ 50m)
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-500">Maksimal radius tiba: 50m</span>
                                @endif
                            @else
                                <span class="text-[11px] text-blue-700">Mendeteksi koordinat GPS...</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                @if($isWithinArrivalRange)
                    {{-- Jarak <= 50 meter: Tombol aktif & siap diklik --}}
                    <button wire:click="markPartnerArrived"
                        class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-lg animate-pulse"
                        style="box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        📍 Saya Sudah Tiba di Lokasi
                    </button>
                    <p class="text-xs text-emerald-600 font-semibold text-center mt-2">
                        ✓ Anda sudah berada dalam radius 50m! Klik tombol di atas untuk konfirmasi kedatangan.
                    </p>
                @elseif($distToCust !== null)
                    {{-- Jarak > 50 meter: Tombol terkunci / belum aktif --}}
                    <button type="button" disabled
                        class="w-full py-3.5 bg-gray-100 border border-gray-200 text-gray-400 rounded-xl font-bold text-sm flex items-center justify-center gap-2 cursor-not-allowed select-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Telah Tiba di Lokasi (Terkunci)</span>
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2 leading-relaxed">
                        Tombol akan aktif otomatis saat jarak Anda <strong>≤ 50 meter</strong> dari customer.<br>
                        <span class="text-gray-400">(Saat ini masih berjarak <strong>{{ $distToCust >= 1000 ? number_format($distToCust / 1000, 1) . ' km' : round($distToCust) . ' meter' }}</strong>)</span>
                    </p>
                @else
                    {{-- Menunggu koordinat GPS --}}
                    <button type="button" disabled
                        class="w-full py-3.5 bg-gray-100 border border-gray-200 text-gray-400 rounded-xl font-bold text-sm flex items-center justify-center gap-2 cursor-not-allowed select-none">
                        <svg class="w-5 h-5 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Mendeteksi Sinyal GPS...</span>
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">Menunggu koordinat lokasi Anda terdeteksi...</p>
                @endif
            </div>
        @endif

        {{-- Action Buttons --}}

        @if ($help->status === 'partner_arrived')
            <div class="bg-emerald-50 px-4 py-3.5 rounded-xl border border-emerald-200 mb-3 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-xs text-emerald-900">📍 Sudah Tiba di Lokasi</h4>
                        <p class="text-[11px] text-emerald-700 mt-0.5">Silakan temui customer dan klik tombol <strong>Mulai Pekerjaan</strong> untuk memulai.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                <button wire:click="startService"
                    class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 shadow-sm"
                    style="box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ⚙️ Mulai Pekerjaan
                </button>
                <p class="text-xs text-gray-500 text-center mt-2">Klik tombol ini setelah Anda siap mengerjakan bantuan</p>
            </div>
        @endif

        @if ($help->status === 'in_progress' || $help->status === 'sedang_diproses')
            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                <button wire:click="openCompletionModal"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Selesai & Unggah Bukti Pekerjaan
                </button>
                <p class="text-xs text-gray-500 text-center mt-2">Unggah foto hasil pekerjaan sebagai bukti bahwa pekerjaan telah selesai</p>
            </div>
        @endif

        @if ($help->status === 'waiting_customer_confirmation')
            @php
                $mitraDeadline = $help->getCustomerConfirmationDeadline();
                $diffInSeconds = $mitraDeadline ? max(0, (int) round(now()->diffInSeconds($mitraDeadline, false))) : 0;
                $netPayout = $help->getMitraPayoutAmount();
            @endphp
            <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 px-4 py-4 rounded-xl border border-orange-200 mb-3 shadow-xs"
                 x-data="{
                    remainingSeconds: Math.max(0, Math.floor(Number({{ $diffInSeconds }}))),
                    formattedTime: '',
                    timer: null,
                    init() {
                        this.updateFormattedTime();
                        this.timer = setInterval(() => {
                            if (this.remainingSeconds > 0) {
                                this.remainingSeconds--;
                                this.updateFormattedTime();
                            } else {
                                clearInterval(this.timer);
                                $wire.$refresh();
                            }
                        }, 1000);
                    },
                    updateFormattedTime() {
                        const totalSec = Math.max(0, Math.floor(Number(this.remainingSeconds)));
                        if (totalSec <= 0) {
                            this.formattedTime = 'Waktu habis (otomatis cair)';
                            return;
                        }
                        const h = Math.floor(totalSec / 3600);
                        const m = Math.floor((totalSec % 3600) / 60);
                        const s = Math.floor(totalSec % 60);
                        this.formattedTime = `${h}j ${m}m ${s}d`;
                    }
                 }">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                            <h4 class="font-bold text-sm text-gray-900">Menunggu Konfirmasi Customer</h4>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="formattedTime">Menghitung...</span>
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">Anda telah menandai pekerjaan selesai dan mengunggah bukti pengerjaan.</p>
                        
                        <div class="mt-2.5 bg-white/80 rounded-lg p-2.5 border border-orange-200/70 text-xs text-gray-700 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Estimasi Pencairan:</span>
                                <span class="font-bold text-green-700">Rp {{ number_format($netPayout, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[11px] text-gray-500 leading-relaxed pt-1 border-t border-gray-100">
                                💡 <strong>Pencairan Otomatis:</strong> Jika customer tidak merespon dalam 24 jam 
                                @if($mitraDeadline)
                                    (maksimal <span class="font-semibold text-gray-700">{{ $mitraDeadline->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</span>),
                                @endif
                                sistem akan <strong>langsung menyelesaikan pesanan secara otomatis dan dana cair ke saldo dompet Anda</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array($help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way', 'partner_arrived']) &&
                $help->mitra_id === auth()->id())
            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                <button wire:click="openPartnerCancelModal"
                    class="w-full py-3 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batalkan Bantuan
                </button>
                <p class="text-xs text-gray-500 text-center mt-2">Batalkan pesanan dan alihkan langsung ke antrean untuk Rekan Jasa lain.</p>
            </div>
        @endif

        {{-- Bukti Pengerjaan / Completion Proof --}}
        @if ($help->completion_photo)
            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100 mb-3">
                <h3 class="font-semibold text-sm text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Bukti Pekerjaan Selesai
                </h3>
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                    <a href="{{ asset('storage/' . $help->completion_photo) }}" target="_blank" class="block group relative">
                        <img src="{{ asset('storage/' . $help->completion_photo) }}" alt="Bukti Selesai" class="w-full h-48 object-cover group-hover:opacity-95 transition">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Lihat Foto Asli
                        </div>
                    </a>
                    @if ($help->completion_notes)
                        <div class="p-3 bg-gray-50 text-xs text-gray-700 border-t border-gray-100">
                            <span class="font-semibold text-gray-900 block mb-0.5">Catatan Pekerjaan:</span>
                            <p class="italic">"{{ $help->completion_notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Status Timeline --}}
        @if (
            $help->partner_started_at ||
                $help->partner_arrived_at ||
                $help->service_started_at ||
                $help->service_completed_at ||
                $help->completed_at)
            <div class="bg-white px-4 py-4 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-semibold text-sm text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Timeline
                </h3>
                <div class="space-y-2 text-xs">
                    @if ($help->partner_started_at)
                        <div class="flex items-center justify-between text-gray-600 py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <span>Mulai Perjalanan</span>
                            </div>
                            <span
                                class="text-gray-500">{{ \Carbon\Carbon::parse($help->partner_started_at)->format('d M, H:i') }}</span>
                        </div>
                    @endif
                    @if ($help->partner_arrived_at)
                        <div class="flex items-center justify-between text-gray-600 py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <span>Tiba di Lokasi</span>
                            </div>
                            <span
                                class="text-gray-500">{{ \Carbon\Carbon::parse($help->partner_arrived_at)->format('d M, H:i') }}</span>
                        </div>
                    @endif
                    @if ($help->service_started_at)
                        <div class="flex items-center justify-between text-gray-600 py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <span>Mulai Pengerjaan</span>
                            </div>
                            <span
                                class="text-gray-500">{{ \Carbon\Carbon::parse($help->service_started_at)->format('d M, H:i') }}</span>
                        </div>
                    @endif
                    @if ($help->service_completed_at)
                        <div class="flex items-center justify-between text-gray-600 py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <span>Selesai Pengerjaan</span>
                            </div>
                            <span
                                class="text-gray-500">{{ \Carbon\Carbon::parse($help->service_completed_at)->format('d M, H:i') }}</span>
                        </div>
                    @endif
                    @if ($help->status === 'waiting_customer_confirmation')
                        <div class="flex items-center justify-between text-orange-700 font-semibold py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                <span>Menunggu Konfirmasi Customer</span>
                            </div>
                            <span
                                class="text-orange-600">{{ \Carbon\Carbon::parse($help->service_completed_at ?? now())->format('d M, H:i') }}</span>
                        </div>
                    @endif
                    @if ($help->completed_at)
                        <div class="flex items-center justify-between text-green-700 font-semibold py-1">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div>
                                <span>Pesanan Selesai</span>
                            </div>
                            <span
                                class="text-green-600">{{ \Carbon\Carbon::parse($help->completed_at)->format('d M, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Rating Customer Form --}}
        @if (in_array($help->status, ['selesai', 'completed']))
            @php
                $mitraRating = \App\Models\Rating::where('help_id', $help->id)
                    ->where('rater_id', auth()->id())
                    ->where('type', 'mitra_to_customer')
                    ->first();
            @endphp

            @if ($mitraRating)
                {{-- Already Rated Customer --}}
                <div
                    class="bg-gradient-to-r from-green-50 to-emerald-50 mt-3 px-4 py-4 border border-green-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-gray-900 mb-2">Rating untuk Customer</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $mitraRating->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                                <span
                                    class="ml-2 text-sm font-semibold text-gray-900">{{ $mitraRating->rating }}/5</span>
                            </div>
                            @if ($mitraRating->review)
                                <p class="text-sm text-gray-700 italic">"{{ $mitraRating->review }}"</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-2">{{ $mitraRating->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Rating Form for Customer --}}
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 mt-3 px-4 py-4 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-sm text-gray-900">Beri Rating untuk Customer</h3>
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-full">Internal</span>
                            </div>
                            <p class="text-xs text-gray-700">Bagaimana pengalaman Anda dengan
                                {{ $help->user->name ?? 'customer ini' }}?</p>
                            <p class="text-[11px] text-blue-700 font-medium mt-1.5 bg-blue-50/80 rounded-md px-2 py-1 border border-blue-200 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Rating ini bersifat internal dan hanya dapat dilihat oleh Super Admin.</span>
                            </p>
                        </div>
                    </div>

                    {{-- Star Rating --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Rating</label>
                        <div class="flex items-center gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="setRating({{ $i }})"
                                    class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-10 h-10 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                            @if ($rating > 0)
                                <span class="ml-2 text-sm font-semibold text-gray-900">{{ $rating }}/5</span>
                            @endif
                        </div>
                        @error('rating')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Review Text --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Ulasan (Opsional)</label>
                        <textarea wire:model="review" rows="3" placeholder="Bagikan pengalaman Anda dengan customer ini..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            maxlength="500"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            @error('review')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-xs text-gray-500">Maksimal 500 karakter</p>
                            @enderror
                            <p class="text-xs text-gray-500">{{ strlen($review ?? '') }}/500</p>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button wire:click="submitCustomerRating" wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all duration-200 shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Kirim Rating</span>
                        <span wire:loading>Mengirim...</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
    {{-- Partner Cancel Modal - Bottom Sheet Style --}}
    @if ($showPartnerCancelModal)
        <div class="modal-overlay fixed inset-0 z-[9999] flex items-end justify-center animate-fade-in" 
             style="background: rgba(0,0,0,0.5);" 
             wire:click="$set('showPartnerCancelModal', false)">
            <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl animate-slide-up relative" 
                 wire:click.stop 
                 style="padding-bottom: env(safe-area-inset-bottom,24px);">
                
                {{-- Header --}}
                <div class="sticky top-0 bg-white border-b px-5 py-4 rounded-t-3xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Ajukan Pembatalan</h3>
                        <button type="button" 
                                wire:click="$set('showPartnerCancelModal', false)" 
                                class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5 pb-6">
                    {{-- Info Icon --}}
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>

                    <p class="text-sm text-gray-700 text-center mb-3">
                        Tuliskan alasan pembatalan bantuan ini. Pesanan akan dialihkan kembali untuk mencari Rekan Jasa pengganti agar customer tidak terlantar.
                    </p>
                    <div class="mb-4 p-2.5 bg-amber-50 rounded-xl border border-amber-200 text-center">
                        <p class="text-xs text-amber-800 font-medium">
                            ⚠️ <strong>Perhatian:</strong> Pembatalan akan dicatat ke rekam jejak performa akun Anda dan dapat dikenakan sanksi jika terlalu sering.
                        </p>
                    </div>

                    {{-- Textarea --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Alasan Pembatalan</label>
                        <textarea wire:model.defer="partnerCancelReason" 
                                  rows="4" 
                                  class="w-full p-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                  placeholder="Contoh: kendaraan rusak, ban bocor, kendala darurat..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">*Wajib/disarankan mengisi alasan yang jelas</p>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3">
                        <button wire:click="$set('showPartnerCancelModal', false)"
                                class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                            Kembali
                        </button>
                        <button wire:click="requestPartnerCancel" 
                                wire:loading.attr="disabled"
                                class="flex-1 px-5 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="requestPartnerCancel">Konfirmasi Batalkan</span>
                            <span wire:loading wire:target="requestPartnerCancel">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Styles --}}
        <style>
            @keyframes fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slide-up {
                from { 
                    transform: translateY(100%);
                    opacity: 0;
                }
                to { 
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }

            .animate-slide-up {
                animation: slide-up 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
        </style>
    @endif

    {{-- MODAL UPLOAD BUKTI PEKERJAAN SELESAI --}}
    @if ($showCompletionModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col max-h-[85vh]"
                @click.away="$wire.closeCompletionModal()">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3.5 text-white flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-base">Bukti Pekerjaan Selesai</h3>
                    </div>
                    <button wire:click="closeCompletionModal" class="text-white/80 hover:text-white p-1 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="markCompleted" class="p-5 space-y-4 overflow-y-auto flex-1">
                    <p class="text-xs text-gray-600">Unggah foto hasil pengerjaan sebagai bukti bahwa pekerjaan telah selesai dikerjakan.</p>

                    {{-- Upload Foto --}}
                    <div x-data="{ uploading: false, progress: 0, uploadError: null }"
                         x-on:livewire-upload-start="uploading = true; uploadError = null"
                         x-on:livewire-upload-finish="uploading = false"
                         x-on:livewire-upload-error="uploading = false; uploadError = 'Gagal mengunggah foto. Pastikan ukuran file maksimal 5 MB dan format gambar valid.'"
                         x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <label class="block text-xs font-semibold text-gray-800 mb-1.5">
                            Foto Bukti Pengerjaan <span class="text-red-500">*</span>
                        </label>

                        @if ($completion_photo)
                            @php
                                $previewUrl = null;
                                try {
                                    $previewUrl = $completion_photo->temporaryUrl();
                                } catch (\Throwable $e) {
                                    $previewUrl = null;
                                }
                            @endphp
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-900 h-44 flex items-center justify-center group mb-2">
                                @if($previewUrl)
                                    <img src="{{ $previewUrl }}" alt="Preview Bukti" class="h-full w-full object-contain">
                                @else
                                    <div class="text-white text-xs p-3 text-center">
                                        <p class="font-bold text-emerald-400">✓ Foto Berhasil Dipilih</p>
                                        <p class="text-[11px] text-gray-300 mt-1">{{ is_object($completion_photo) && method_exists($completion_photo, 'getClientOriginalName') ? $completion_photo->getClientOriginalName() : 'Foto bukti' }}</p>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    <label for="completion_photo_input" class="px-3 py-1.5 bg-white text-gray-800 text-xs font-semibold rounded-lg cursor-pointer hover:bg-gray-100 transition shadow-xs">
                                        Ganti Foto
                                    </label>
                                </div>
                            </div>
                        @else
                            <label for="completion_photo_input" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50/50 hover:border-blue-300 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-gray-700">Ambil / Pilih Foto Hasil Pekerjaan</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Format: JPG, PNG, WebP (Maks 5 MB)</p>
                                </div>
                            </label>
                        @endif

                        <input type="file" id="completion_photo_input" wire:model="completion_photo" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">
                        
                        {{-- Upload Progress Bar --}}
                        <div x-show="uploading" class="mt-2" style="display: none;">
                            <div class="flex items-center justify-between text-[11px] text-blue-600 font-semibold mb-1">
                                <span>Mengunggah foto...</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-200" :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>

                        {{-- Client-side upload error --}}
                        <div x-show="uploadError" class="text-xs text-red-500 mt-1 font-medium" x-text="uploadError" style="display: none;"></div>

                        @error('completion_photo')
                            <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Catatan / Keterangan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-800 mb-1">
                            Catatan Pekerjaan <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea wire:model="completion_notes" rows="2"
                            placeholder="Contoh: Pekerjaan sudah selesai dengan rapi, area kerja sudah dibersihkan."
                            class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        @error('completion_notes')
                            <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2.5 pt-2">
                        <button type="button" wire:click="closeCompletionModal"
                            class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                            <span wire:loading.remove wire:target="markCompleted">Kirim Bukti & Selesai</span>
                            <span wire:loading wire:target="markCompleted" class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Pesanan Dialihkan ke Rekan Jasa Lain --}}
    @if($showReassignedModal)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[99999] p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-amber-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Pesanan Dialihkan</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-6">
                    Pesanan ini telah dialihkan oleh customer ke Rekan Jasa lain karena belum ada tanda pergerakan menuju lokasi dalam waktu 30 menit.
                </p>
                <a href="{{ route('mitra.dashboard') }}"
                   class="block w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif
</div>