    <div class="min-h-screen bg-gray-50" 
    wire:poll.8s="loadHelp"
    x-data="{ 
        showNotification: false, 
        notificationMessage: '',
        trackingData: {
            partnerLat: {{ !empty($help->partner_current_lat) ? (float)$help->partner_current_lat : (!empty($help->mitra?->latitude) ? (float)$help->mitra->latitude : (!empty($help->latitude) ? (float)$help->latitude : -6.2088)) }},
            partnerLng: {{ !empty($help->partner_current_lng) ? (float)$help->partner_current_lng : (!empty($help->mitra?->longitude) ? (float)$help->mitra->longitude : (!empty($help->longitude) ? (float)$help->longitude : 106.8456)) }},
            customerLat: {{ !empty($help->latitude) ? (float)$help->latitude : -6.2088 }},
            customerLng: {{ !empty($help->longitude) ? (float)$help->longitude : 106.8456 }},
            partnerName: '{{ $help->mitra->name ?? "Mitra" }}',
            location: '{{ $help->location ?? "Tujuan" }}'
        }
    }"
    x-init="
        // Update tracking data setiap kali Livewire refresh
        Livewire.hook('morph.updated', () => {
            const oldLat = trackingData.partnerLat;
            const oldLng = trackingData.partnerLng;
            
            trackingData.partnerLat = {{ !empty($help->partner_current_lat) ? (float)$help->partner_current_lat : (!empty($help->mitra?->latitude) ? (float)$help->mitra->latitude : (!empty($help->latitude) ? (float)$help->latitude : -6.2088)) }};
            trackingData.partnerLng = {{ !empty($help->partner_current_lng) ? (float)$help->partner_current_lng : (!empty($help->mitra?->longitude) ? (float)$help->mitra->longitude : (!empty($help->longitude) ? (float)$help->longitude : 106.8456)) }};
            trackingData.customerLat = {{ !empty($help->latitude) ? (float)$help->latitude : -6.2088 }};
            trackingData.customerLng = {{ !empty($help->longitude) ? (float)$help->longitude : 106.8456 }};
            
            // Log perubahan lokasi
            if (oldLat !== trackingData.partnerLat || oldLng !== trackingData.partnerLng) {
                console.log('📍 Lokasi mitra diperbarui:', {
                    old: { lat: oldLat, lng: oldLng },
                    new: { lat: trackingData.partnerLat, lng: trackingData.partnerLng }
                });
            }
            
            // Trigger update ke peta jika modal terbuka
            if (window.updateMapFromAlpine) {
                window.updateMapFromAlpine();
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

        {{-- Live tracking summary (updates continuously, visible without opening modal) --}}
        <div id="live-tracking-summary" wire:ignore class="bg-white mt-2 px-4 py-2 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Estimasi Tiba</p>
                    <p id="summary-eta" class="text-sm font-semibold text-blue-700">Menghitung...</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-600">Jarak</p>
                <p id="summary-distance" class="text-sm font-semibold text-blue-700">-</p>
            </div>
        </div>
    </div>

    {{-- Header - match other customer pages (gradient BRImo style) --}}
    <div class="px-5 pt-5 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

        <div class="relative z-10 max-w-md mx-auto">
            <div class="flex items-center justify-between text-white mb-6">
                <button onclick="window.history.back()" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-bold">Detail Pesanan</h1>
                    <p class="text-xs text-white/90 mt-0.5">Detail permintaan bantuan Anda</p>
                </div>

                {{-- <div class="w-9">
                    <button class="p-2 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                        </svg>
                    </button>
                </div> --}}
            </div>
        </div>        

            <!-- Curved separator (SVG) to create non-flat divider into content -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-28 max-w-md mx-auto">
        {{-- Order ID --}}
        <div class="bg-white px-4 py-3 flex items-center justify-between rounded-xl shadow-sm border border-gray-100">
            <span class="text-sm text-gray-600">ID Pesanan: <span class="font-semibold text-gray-900">{{ $help->order_id }}</span></span>
            <button wire:click="copyOrderId" class="text-blue-500 text-sm font-semibold flex items-center gap-1">
                Salin
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>

        {{-- Banner Countdown Auto-Batal untuk Urgent Help --}}
        @if($help->status === 'menunggu_mitra' && $help->help_type === 'urgent' && $help->auto_cancel_at)
            <div x-data="{
                targetTime: new Date('{{ $help->auto_cancel_at->toIso8601String() }}').getTime(),
                timeLeft: '',
                isExpired: false,
                updateTimer() {
                    const now = new Date().getTime();
                    const diff = this.targetTime - now;
                    if (diff <= 0) {
                        this.timeLeft = '00:00';
                        this.isExpired = true;
                        $wire.$refresh();
                        return;
                    }
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    this.timeLeft = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                }
            }" x-init="updateTimer(); setInterval(() => updateTimer(), 1000)"
            class="mt-3 p-4 bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-2xl shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-red-600 uppercase tracking-wider block">Batas Waktu Tunggu Mitra</span>
                            <span class="text-xs text-gray-600 font-medium">Bantuan Mendesak (Urgent)</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-black text-red-600 font-mono tracking-wider" x-text="timeLeft">--:--</span>
                        <span class="text-[10px] text-gray-500 block">menit : detik</span>
                    </div>
                </div>
                <div class="mt-2.5 pt-2.5 border-t border-red-200/60 text-[11px] text-red-700 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Jika waktu habis dan belum ada mitra yang mengambil, pesanan otomatis batal & saldo 100% kembali.</span>
                </div>
            </div>
        @endif

        {{-- Banner Peringatan Rekan Jasa Belum Berangkat (>= 30 Menit) --}}
        @if($help->isPartnerIdleOver30Minutes())
            <div id="idle-partner-warning-banner" class="mt-3 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 rounded-2xl flex items-start gap-3 shadow-sm animate-pulse-subtle">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-amber-600">
                    <svg class="w-6 h-6 animate-pulse text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 text-xs">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        <h4 class="font-bold text-amber-950 text-sm">Rekan Jasa Belum Menuju Lokasi</h4>
                    </div>
                    <p class="text-amber-800 leading-relaxed">
                        Rekan jasa <span class="font-semibold">{{ $help->mitra->name ?? 'Mitra' }}</span> belum bergerak menuju lokasi Anda lebih dari 30 menit. Anda dapat mengganti rekan jasa lain atau membatalkan pesanan tanpa biaya tambahan.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <button type="button" 
                                onclick="document.getElementById('customer-action-section')?.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                                class="px-3.5 py-1.5 bg-amber-600 text-white font-bold text-xs rounded-lg shadow-sm hover:bg-amber-700 transition cursor-pointer flex items-center gap-1">
                            <span>Lihat Opsi Tindakan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Banner Notifikasi Jika Rekan Jasa Membatalkan Langsung --}}
        @if($help->status === 'menunggu_mitra' && !empty($help->partner_cancel_requested_at))
            <div class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                    <h4 class="font-bold text-amber-900 text-sm">
                        @if(str_contains(strtolower($help->partner_cancel_reason), 'rekan jasa lain') || str_contains(strtolower($help->partner_cancel_reason), 'keberangkatan'))
                            Pengalihan Rekan Jasa
                        @else
                            Rekan Jasa Berhalangan Hadir
                        @endif
                    </h4>
                    <p class="text-amber-800 mt-1">
                        @if($help->lastCancelledMitra)
                            Rekan Jasa sebelumnya (<strong class="text-gray-900">{{ $help->lastCancelledMitra->name }}</strong>):
                        @else
                            Keterangan:
                        @endif
                        <span class="italic font-medium">"{{ $help->partner_cancel_reason ?: 'Kendala darurat di jalan' }}"</span>
                    </p>
                    @php
                        $cTime = $help->partner_cancel_requested_at ?? $help->updated_at;
                    @endphp
                    @if($cTime)
                        <p class="text-[11px] text-amber-700/80 mt-0.5">
                            Waktu: {{ $cTime->format('d M Y, H:i') }} WIB
                        </p>
                    @endif
                    <div class="mt-2 flex items-center gap-1.5 text-amber-900 font-semibold bg-amber-100/70 px-2.5 py-1.5 rounded-lg">
                        <svg class="w-4 h-4 animate-spin text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Sistem otomatis mencari Rekan Jasa pengganti baru untuk Anda...</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Banner Notifikasi Jika Pesanan Dibatalkan Customer atau Ditolak Admin (Kecuali jika dibatalkan karena refund mediasi) --}}
        @if(in_array($help->status, ['dibatalkan', 'cancelled']) && empty($help->complaint_resolution))
            <div class="mt-3 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="flex-1 text-xs">
                    <h4 class="font-bold text-red-900 text-sm">Pesanan Dibatalkan</h4>
                    @if(!empty($help->customer_cancel_reason))
                        <p class="text-red-700 mt-1">
                            Alasan pembatalan: <span class="italic font-semibold">"{{ $help->customer_cancel_reason }}"</span>
                        </p>
                    @endif
                    <p class="text-emerald-700 text-[11px] mt-1 font-medium">
                        ✓ Saldo pembayaran telah dikembalikan penuh ke dompet Anda.
                    </p>
                </div>
            </div>
        @elseif(in_array($help->status, ['rejected', 'ditolak']))
            <div class="mt-3 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="flex-1 text-xs">
                    <h4 class="font-bold text-red-900 text-sm">Pesanan Ditolak Admin</h4>
                    @if(!empty($help->admin_notes))
                        <p class="text-red-700 mt-1">
                            Alasan penolakan: <span class="italic font-semibold">"{{ $help->admin_notes }}"</span>
                        </p>
                    @else
                        <p class="text-red-700 mt-1">Permintaan bantuan ini tidak memenuhi kualifikasi atau kebijakan layanan.</p>
                    @endif
                    <p class="text-emerald-700 text-[11px] mt-1 font-medium">
                        ✓ Saldo pembayaran telah dikembalikan penuh ke dompet Anda.
                    </p>
                </div>
            </div>
        @endif

        {{-- Status Pengajuan Komplain / Refund (Jika customer telah mengajukan komplain atau dalam proses refund) --}}
        @if(in_array($help->status, ['komplain', 'disputed']) || !empty($help->complaint_reason) || !empty($help->complaint_resolution))
            @php
                $isRefunded = ($help->complaint_resolution === 'refunded');
                $isRejected = ($help->complaint_resolution === 'rejected');
                $isPendingMediation = (!$isRefunded && !$isRejected);
            @endphp
            <div class="mt-2 bg-white rounded-xl shadow-sm border {{ $isRefunded ? 'border-green-200' : ($isRejected ? 'border-gray-200' : 'border-red-200') }} overflow-hidden">
                <div class="px-4 py-3 {{ $isRefunded ? 'bg-green-50/90 border-green-100' : ($isRejected ? 'bg-gray-50 border-gray-200' : 'bg-red-50/90 border-red-100') }} border-b flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $isRefunded ? 'bg-green-500' : ($isRejected ? 'bg-gray-400' : 'bg-red-500 animate-pulse') }}"></span>
                        <h3 class="font-bold text-xs {{ $isRefunded ? 'text-green-900' : ($isRejected ? 'text-gray-800' : 'text-red-900') }}">
                            @if($isRefunded)
                                Pengembalian Dana (Refund) Disetujui
                            @elseif($isRejected)
                                Hasil Mediasi: Komplain Ditolak
                            @else
                                Status Pengajuan Komplain / Refund
                            @endif
                        </h3>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isRefunded ? 'bg-green-100 text-green-700' : ($isRejected ? 'bg-gray-200 text-gray-700' : 'bg-red-100 text-red-700') }}">
                        @if($isRefunded)
                            ✓ Refund Berhasil
                        @elseif($isRejected)
                            ✕ Komplain Ditolak
                        @else
                            ⚠️ Mediasi Admin
                        @endif
                    </span>
                </div>
                
                <div class="p-4 space-y-3">
                    @if($isRefunded)
                        <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-800">
                            <p class="font-bold mb-0.5">Dana Telah Dikembalikan ke Saldo Anda</p>
                            <p class="text-[11px] text-green-700 leading-relaxed">
                                Pengajuan refund Bantuan #{{ $help->id }} telah disetujui Admin pada {{ $help->complaint_resolved_at?->translatedFormat('d M Y, H:i') }}. Dana sebesar <strong>Rp {{ number_format($help->amount + ($help->admin_fee ?? 0), 0, ',', '.') }}</strong> telah masuk kembali 100% ke saldo akun Anda.
                            </p>
                            @if(!empty($help->complaint_admin_notes))
                                <div class="mt-2 pt-2 border-t border-green-200/60 text-[11px]">
                                    <span class="font-semibold">Catatan Admin:</span> {{ $help->complaint_admin_notes }}
                                </div>
                            @endif
                        </div>
                    @elseif($isRejected)
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800">
                            <p class="font-bold mb-0.5 text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Komplain Ditolak & Mediasi Telah Selesai
                            </p>
                            <p class="text-[11px] text-gray-600 leading-relaxed mt-1">
                                Pengajuan komplain Bantuan #{{ $help->id }} telah ditinjau oleh Admin pada {{ $help->complaint_resolved_at?->translatedFormat('d M Y, H:i') }} dan dinyatakan <strong>ditolak</strong>. Pekerjaan telah dinyatakan selesai dan dana telah dicairkan ke saldo rekan jasa.
                            </p>
                            @if(!empty($help->complaint_admin_notes))
                                <div class="mt-2 pt-2 border-t border-gray-200 text-[11px] text-gray-700">
                                    <span class="font-semibold text-gray-900">Catatan / Alasan Admin:</span> {{ $help->complaint_admin_notes }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                            <p class="font-bold mb-0.5">Sedang Ditinjau Admin & Super Admin</p>
                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                Anda telah mengajukan komplain pada {{ $help->complaint_submitted_at?->translatedFormat('d M Y, H:i') }}. Admin sedang memverifikasi bukti ketidaksesuaian. Jika disetujui, dana akan dikembalikan penuh ke saldo Anda.
                            </p>
                        </div>
                    @endif

                    {{-- Bukti & Alasan Komplain Yang Diajukan Customer --}}
                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-700 mb-1.5">Rincian Komplain yang Anda Ajukan:</h4>
                        
                        @if($help->complaint_reason)
                            <div class="bg-gray-50 p-3 rounded-lg text-xs text-gray-700 mb-2.5 border border-gray-100">
                                <span class="font-semibold text-gray-900 block mb-0.5">Alasan:</span>
                                {{ $help->complaint_reason }}
                            </div>
                        @endif

                        @if($help->complaint_photo)
                            <div>
                                <span class="text-xs font-semibold text-gray-600 block mb-1">Foto Bukti Ketidaksesuaian:</span>
                                <div class="relative inline-block">
                                    <a href="{{ asset('storage/' . $help->complaint_photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $help->complaint_photo) }}" alt="Foto Bukti Komplain" class="w-36 h-28 object-cover rounded-lg border border-gray-200 shadow-xs cursor-pointer hover:opacity-90 transition">
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Service Info --}}
        <div class="bg-white mt-2 px-4 py-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-2xl">
                    @if($help->photo)
                        <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}" class="w-full h-full object-cover rounded-lg">
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
                </div>
            </div>

                {{-- Partner Info --}}
            @if($help->mitra)
                <div class="mt-4 p-3 bg-white rounded-xl flex items-center justify-between shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        @if($help->mitra->selfie_photo)
                            <img src="{{ asset('storage/' . $help->mitra->selfie_photo) }}" alt="{{ $help->mitra->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-blue-100">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($help->mitra->name ?? 'M', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-sm text-gray-900">{{ $help->mitra->name ?? 'Mitra' }}</h3>
                            <div class="flex items-center gap-1 mt-0.5">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @php
                                    $mitra = $help->mitra;
                                    $avgRating = $mitra ? ($mitra->mitra_average_rating ?? ($mitra->rating ?? 0)) : 0;
                                    $ratingCount = $mitra ? ($mitra->mitra_rating_count ?? null) : null;
                                @endphp
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($avgRating, 2) }}</span>
                                @if($ratingCount)
                                    <span class="text-xs text-gray-400 ml-2">({{ $ratingCount }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- <a href="tel:{{ $help->mitra->phone ?? '' }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </a> --}}
                        <a href="{{ route('customer.chat', $help->id) }}" class="relative w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            @php
                                $unreadCount = \App\Models\Chat::where('help_id', $help->id)
                                    ->where('sender_type', 'mitra')
                                    ->whereNull('read_at')
                                    ->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[16px] h-[16px] px-1 text-[9px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white shadow-xs">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

            @endif

            {{-- Description & Additional Details --}}
            <div class="bg-white mt-2 px-4 py-4 rounded-lg shadow-sm border border-gray-100">
                <h3 class="font-bold text-sm text-gray-900 mb-3">Deskripsi & Detail</h3>

                @if(!empty($help->description))
                    <div class="mb-3">
                        <p class="text-sm text-gray-700 whitespace-pre-line break-words break-all">{{ $help->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 text-sm text-gray-700">
                    @if(!empty($help->equipment_provided))
                        <div>
                            <div class="text-xs text-gray-500">Perlengkapan</div>
                            <div class="font-semibold break-words break-all">{{ $help->equipment_provided }}</div>
                        </div>
                    @endif


                    @if(!empty($help->city->name) || !empty($help->province->name))
                        <div>
                            <div class="text-xs text-gray-500">Kota / Provinsi</div>
                            <div class="font-semibold">{{ $help->city->name ?? '-' }}{{ $help->province ? (', ' . $help->province->name) : '' }}</div>
                        </div>
                    @endif

                    {{-- <div>
                        <div class="text-xs text-gray-500">Koordinat</div>
                        <div class="font-semibold">{{ $help->latitude ? $help->latitude : '-' }}, {{ $help->longitude ? $help->longitude : '-' }}</div>
                    </div> --}}
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
                    <div class="mt-1">
                        Jenis: <span class="font-bold {{ $help->isUrgent() ? 'text-red-600' : 'text-blue-600' }}">{{ $help->isUrgent() ? '⚡ Mendesak (Urgent)' : '📅 Terjadwal' }}</span>
                    </div>
                </div>
            </div>

            @php
                $status = $help->status;
                $statusLabel = match($status) {
                    'menunggu_pembayaran' => 'Menunggu Pembayaran',
                    'mencari_mitra', 'menunggu_mitra', 'memperoleh_mitra' => 'Mencari Rekan Jasa',
                    'taken' => 'Menunggu Rekan Jasa',
                    'partner_on_the_way' => 'Menuju ke Lokasi',
                    'partner_arrived' => 'Tiba di Lokasi',
                    'in_progress', 'sedang_diproses' => 'Sedang Diproses',
                    'waiting_customer_confirmation' => 'Menunggu Konfirmasi',
                    'selesai', 'completed' => ($help->complaint_resolution === 'rejected' ? 'Pesanan Selesai (Mediasi Komplain)' : 'Pesanan Selesai'),
                    'dibatalkan', 'cancelled' => ($help->complaint_resolution === 'refunded' ? 'Dibatalkan (Refund Disetujui)' : 'Pesanan Dibatalkan'),
                    'komplain', 'disputed' => 'Dalam Mediasi Komplain',
                    default => ucfirst(str_replace('_', ' ', $status)),
                };

                $statusTheme = match($status) {
                    'menunggu_pembayaran' => [
                        'card' => 'bg-amber-50/80 border-amber-200/80 text-amber-900',
                        'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'dot' => 'bg-amber-500',
                        'icon_bg' => 'bg-amber-100 text-amber-600',
                        'pulse' => true,
                    ],
                    'mencari_mitra', 'menunggu_mitra', 'memperoleh_mitra' => [
                        'card' => 'bg-blue-50/80 border-blue-200/80 text-blue-900',
                        'badge' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'dot' => 'bg-blue-600',
                        'icon_bg' => 'bg-blue-100 text-blue-600',
                        'pulse' => true,
                    ],
                    'taken', 'partner_on_the_way' => [
                        'card' => 'bg-indigo-50/80 border-indigo-200/80 text-indigo-900',
                        'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'dot' => 'bg-indigo-600',
                        'icon_bg' => 'bg-indigo-100 text-indigo-600',
                        'pulse' => true,
                    ],
                    'partner_arrived' => [
                        'card' => 'bg-emerald-50/80 border-emerald-200/80 text-emerald-900',
                        'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'dot' => 'bg-emerald-600',
                        'icon_bg' => 'bg-emerald-100 text-emerald-600',
                        'pulse' => false,
                    ],
                    'in_progress', 'sedang_diproses' => [
                        'card' => 'bg-sky-50/80 border-sky-200/80 text-sky-900',
                        'badge' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'dot' => 'bg-sky-600',
                        'icon_bg' => 'bg-sky-100 text-sky-600',
                        'pulse' => true,
                    ],
                    'waiting_customer_confirmation' => [
                        'card' => 'bg-orange-50/80 border-orange-200/80 text-orange-900',
                        'badge' => 'bg-orange-100 text-orange-800 border-orange-200',
                        'dot' => 'bg-orange-600',
                        'icon_bg' => 'bg-orange-100 text-orange-600',
                        'pulse' => true,
                    ],
                    'selesai', 'completed' => [
                        'card' => 'bg-emerald-50/80 border-emerald-200/80 text-emerald-900',
                        'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'dot' => 'bg-emerald-600',
                        'icon_bg' => 'bg-emerald-100 text-emerald-600',
                        'pulse' => false,
                    ],
                    'dibatalkan', 'cancelled' => [
                        'card' => 'bg-rose-50/80 border-rose-200/80 text-rose-900',
                        'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                        'dot' => 'bg-rose-500',
                        'icon_bg' => 'bg-rose-100 text-rose-600',
                        'pulse' => false,
                    ],
                    'komplain', 'disputed' => [
                        'card' => 'bg-amber-50/80 border-amber-200/80 text-amber-900',
                        'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'dot' => 'bg-amber-500',
                        'icon_bg' => 'bg-amber-100 text-amber-600',
                        'pulse' => true,
                    ],
                    default => [
                        'card' => 'bg-gray-50 border-gray-200 text-gray-900',
                        'badge' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'dot' => 'bg-gray-500',
                        'icon_bg' => 'bg-gray-100 text-gray-600',
                        'pulse' => false,
                    ]
                };

                $isScheduledWaiting = $help->isScheduled() && !$help->canPartnerStartJourney() && in_array($help->status, ['taken', 'memperoleh_mitra']);
                $isTrackable = in_array($help->status, ['partner_on_the_way', 'partner_arrived']) || ($help->status === 'taken' && $help->canPartnerStartJourney());
            @endphp

            <!-- Status Card (Layout Bawaan Sesuai Timeline Bawah) -->
            <div class="mt-3 p-3 rounded-2xl border {{ $statusTheme['card'] }} shadow-xs transition-all bg-white">
                <div class="flex items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-9 h-9 rounded-xl {{ $statusTheme['icon_bg'] }} flex items-center justify-center shrink-0">
                            @if(in_array($help->status, ['mencari_mitra', 'menunggu_mitra', 'memperoleh_mitra']))
                                <svg class="w-4 h-4 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            @elseif($isTrackable)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                            <span class="text-xs sm:text-sm font-bold text-gray-900 block leading-snug">{{ $statusLabel }}</span>
                        </div>
                    </div>

                    @if($isTrackable)
                        <button type="button" wire:click="showTrackingMap" class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer">
                            <span>Lacak</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @elseif($isScheduledWaiting)
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border bg-blue-50 text-blue-700 border-blue-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            <span>Terjadwal</span>
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusTheme['badge'] }}">
                            @if($statusTheme['pulse'])
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusTheme['dot'] }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $statusTheme['dot'] }}"></span>
                                </span>
                            @else
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusTheme['dot'] }}"></span>
                            @endif
                            <span>{{ in_array($help->status, ['mencari_mitra', 'menunggu_mitra', 'memperoleh_mitra']) ? 'Mencari' : (in_array($help->status, ['selesai', 'completed']) ? 'Selesai' : (in_array($help->status, ['dibatalkan', 'cancelled']) ? ($help->complaint_resolution === 'refunded' ? 'Refund' : 'Batal') : (in_array($help->status, ['komplain', 'disputed']) ? 'Mediasi' : 'Aktif'))) }}</span>
                        </span>
                    @endif
                </div>
            </div>

            @if($isScheduledWaiting)
                @php
                    $schedCarbonCust = \Carbon\Carbon::parse($help->scheduled_at)->locale('id');
                    $availCarbonCust = $schedCarbonCust->copy()->subHour();
                @endphp
                <div class="mt-2.5 p-3 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-start gap-2.5 text-xs text-blue-900 leading-relaxed shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <span class="font-bold">Pesanan Terjadwal:</span> Rekan Jasa telah menerima pesanan Anda dan akan mulai menuju lokasi 1 jam sebelum jadwal (<strong>{{ $schedCarbonCust->translatedFormat('l, d F Y') }}</strong> - Jam {{ $schedCarbonCust->format('H:i') }} WIB).
                    </div>
                </div>
            @endif
        </div>

        {{-- Warning Info --}}
        {{-- <div class="bg-yellow-50 mt-2 px-4 py-3 rounded-lg shadow-sm border border-yellow-100">
            <div class="flex gap-3">
                <div class="w-9 h-9 rounded-md bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm text-gray-900 mb-1">Selalu jaga keamanan pesananmu</h3>
                    <p class="text-xs text-gray-700 leading-relaxed">Pastikan hanya transaksi di aplikasi agar pesananmu terlindungi asuransi. Laporkan Rekan Jasa yang meminta pembayaran di luar aplikasi untuk dapatkan pengembalian dana atau layanan gratis.</p>
                </div>
            </div>
        </div> --}}

        {{-- Location --}}
        <div class="bg-white mt-2 px-4 py-4 rounded-lg shadow-sm border border-gray-100">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-gray-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-sm text-gray-900 mb-1">Lokasi</h3>
                    <p class="text-sm text-gray-700">{{ $help->location ?? $help->full_address ?? 'Rumah warna coklat' }}</p>
                    @if($help->full_address)
                        <p class="text-xs text-gray-500 mt-1"><span class="font-semibold">Detail :</span> {{ $help->full_address }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Schedule (Hanya tampil untuk pesanan terjadwal) --}}
        @if($help->isScheduled() && !empty($help->scheduled_at))
            <div class="bg-white mt-2 px-4 py-4 rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm text-gray-900 mb-1">Jadwal Pesanan</h3>
                        <p class="text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('l, d F Y') }} 
                            (Jam {{ \Carbon\Carbon::parse($help->scheduled_at)->format('H:i') }} WIB)
                        </p>
                        <p class="text-xs text-gray-500 mt-1">*Jadwal tertera dalam WIB</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Status Timeline - Redesigned visual to match reference --}}
        <div class="bg-white mt-2 px-4 py-4 rounded-lg shadow-sm border border-gray-100" x-data="{ showProofModal: false }">
            <h3 class="font-bold text-sm text-gray-900 mb-4">Status Pesanan</h3>

            @php
                $statuses = [
                    [ 'key' => 'payment', 'title' => 'Pembayaran', 'time' => $help->created_at, 'active' => in_array($help->status, ['menunggu_pembayaran','mencari_mitra','menunggu_mitra','memperoleh_mitra','taken','partner_on_the_way','partner_arrived','in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => $help->status === 'menunggu_pembayaran' ],
                    [ 'key' => 'searching', 'title' => 'Mencari Rekan Jasa', 'time' => $help->mitra_assigned_at ?? $help->taken_at, 'active' => in_array($help->status, ['mencari_mitra','menunggu_mitra','memperoleh_mitra','taken','partner_on_the_way','partner_arrived','in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => in_array($help->status, ['mencari_mitra','menunggu_mitra','memperoleh_mitra']) ],
                    [ 'key' => 'accepted', 'title' => 'Menunggu Rekan Jasa berangkat', 'time' => $help->taken_at, 'active' => in_array($help->status, ['taken','partner_on_the_way','partner_arrived','in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => $help->status === 'taken' ],
                    [ 'key' => 'on_the_way', 'title' => 'Rekan Jasa menuju ke lokasi', 'time' => $help->partner_started_moving_at, 'active' => in_array($help->status, ['partner_on_the_way','partner_arrived','in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => $help->status === 'partner_on_the_way' ],
                    [ 'key' => 'arrived', 'title' => 'Rekan Jasa tiba di lokasi', 'time' => $help->partner_arrived_at, 'active' => in_array($help->status, ['partner_arrived','in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => $help->status === 'partner_arrived' ],
                    [ 'key' => 'in_progress', 'title' => 'Pelayanan dalam proses', 'time' => $help->service_started_at, 'active' => in_array($help->status, ['in_progress','sedang_diproses','waiting_customer_confirmation','selesai','completed']), 'current' => in_array($help->status, ['in_progress','sedang_diproses']) ],
                    [ 'key' => 'waiting_confirmation', 'title' => 'Menunggu konfirmasi Anda', 'time' => $help->service_completed_at, 'active' => in_array($help->status, ['waiting_customer_confirmation','selesai','completed']), 'current' => $help->status === 'waiting_customer_confirmation' ],
                    [ 'key' => 'completed', 'title' => 'Pesanan selesai', 'time' => $help->completed_at, 'active' => in_array($help->status, ['selesai','completed']), 'current' => in_array($help->status, ['selesai','completed']) ]
                ];
            @endphp

            <div>
                <div class="space-y-4">
                    @foreach($statuses as $index => $status)
                        <div class="flex items-start">
                            {{-- left column: dot + connector --}}
                            <div class="w-12 flex flex-col items-center">
                                {{-- dot --}}
                                <div class="relative z-10">
                                    @if($status['active'])
                                        @if($status['current'])
                                            <div class="w-5 h-5 rounded-full border-2 border-blue-500 bg-white flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @else
                                            <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                                        @endif
                                    @else
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 bg-white"></div>
                                    @endif
                                </div>

                                {{-- connector below dot (except last) --}}
                                @if(!$loop->last)
                                    <div class="flex-1 w-px mt-2 {{ $status['active'] ? 'bg-blue-200' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>

                            {{-- content --}}
                            <div class="flex-1 pl-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold {{ $status['active'] ? 'text-gray-900' : 'text-gray-400' }}">{{ $status['title'] }}</h4>
                                    <div class="text-xs {{ $status['active'] ? 'text-gray-600' : 'text-gray-400' }} whitespace-nowrap">
                                        @if($status['time'])
                                            {{ \Carbon\Carbon::parse($status['time'])->format('d M, H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>

                                @if($status['current'] || ($status['key'] === 'waiting_confirmation' && $help->completion_photo))
                                    <div class="mt-1 text-xs">
                                        @if($status['key'] === 'on_the_way' && $help->partner_current_lat && $help->latitude)
                                            @php
                                                $earthRadius = 6371000;
                                                $lat1 = deg2rad($help->partner_current_lat);
                                                $lat2 = deg2rad($help->latitude);
                                                $latDiff = deg2rad($help->latitude - $help->partner_current_lat);
                                                $lngDiff = deg2rad($help->longitude - $help->partner_current_lng);
                                                $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($lat1) * cos($lat2) * sin($lngDiff / 2) * sin($lngDiff / 2);
                                                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                                                $distance = round($earthRadius * $c);
                                            @endphp
                                            <p class="text-xs text-blue-600 flex items-center gap-1">Jarak: {{ $distance > 1000 ? number_format($distance/1000, 1) . ' km' : $distance . ' m' }}</p>
                                        @elseif($status['key'] === 'accepted')
                                            {{-- <p class="text-xs text-blue-600">GPS tracking aktif</p> --}}
                                        @elseif($status['key'] === 'arrived')
                                            <p class="text-xs text-green-600">Rekan jasa sudah sampai</p>
                                        @elseif($status['key'] === 'in_progress')
                                            <p class="text-xs text-blue-600">Pekerjaan sedang berlangsung</p>
                                        @elseif($status['key'] === 'waiting_confirmation')
                                            @if($status['current'])
                                                <p class="text-xs text-orange-600 font-semibold">Silakan konfirmasi pesanan selesai</p>
                                            @endif
                                            @if ($help->completion_photo)
                                                <button @click="showProofModal = true"
                                                    type="button"
                                                    class="mt-0.5 text-blue-600 hover:text-blue-700 text-xs font-bold hover:underline inline-flex items-center gap-0.5 transition cursor-pointer">
                                                    <span>Lihat Bukti Pekerjaan</span>
                                                    <svg class="w-3 h-3" style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Modal Popup Lihat Bukti Pekerjaan (Shopee Style) --}}
            @if ($help->completion_photo)
                <template x-teleport="body">
                    <div x-show="showProofModal"
                        x-transition.opacity
                        class="fixed inset-0 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
                        style="z-index: 999999; display: none;">
                        <div @click.away="showProofModal = false"
                            class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh] relative"
                            style="z-index: 1000000;">
                            
                            {{-- Modal Header --}}
                            <div class="bg-gray-900 px-4 py-3 text-white flex items-center justify-between flex-shrink-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-bold text-sm">Bukti Pekerjaan Selesai</span>
                                </div>
                                <button @click="showProofModal = false" type="button" class="text-gray-400 hover:text-white p-1 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Modal Body: Photo --}}
                            <div class="p-3 bg-gray-950 flex-1 overflow-y-auto flex flex-col items-center justify-center min-h-[250px] max-h-[60vh]">
                                <img src="{{ asset('storage/' . $help->completion_photo) }}"
                                    alt="Bukti Pekerjaan Selesai"
                                    class="w-full h-auto max-h-[55vh] object-contain rounded-xl shadow-md">
                            </div>

                            {{-- Modal Footer: Notes & Action Buttons --}}
                            <div class="p-4 bg-white border-t border-gray-100 flex-shrink-0 space-y-3">
                                @if ($help->completion_notes)
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200/80 text-xs">
                                        <span class="font-semibold text-gray-900 block mb-0.5">Catatan Rekan Jasa:</span>
                                        <p class="text-gray-700 italic">"{{ $help->completion_notes }}"</p>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $help->completion_photo) }}" target="_blank"
                                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded-xl text-center transition flex items-center justify-center gap-1 cursor-pointer">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Foto Asli
                                    </a>
                                    <button @click="showProofModal = false"
                                        type="button"
                                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            @endif
        </div>

        {{-- Confirmation & Complaint Action Section --}}
        @if($help->status === 'waiting_customer_confirmation')
            @php
                $deadline = $help->getCustomerConfirmationDeadline();
                $diffInSeconds = $deadline ? max(0, (int) round(now()->diffInSeconds($deadline, false))) : 0;
            @endphp
            <div class="bg-gradient-to-r from-orange-50/80 via-amber-50/60 to-yellow-50/80 mt-2 p-4 sm:p-5 border border-amber-200/90 rounded-2xl shadow-xs"
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
                            this.formattedTime = 'Waktu habis (otomatis selesai)';
                            return;
                        }
                        const h = Math.floor(totalSec / 3600);
                        const m = Math.floor((totalSec % 3600) / 60);
                        const s = Math.floor(totalSec % 60);
                        this.formattedTime = `${h}j ${m}m ${s}d`;
                    }
                 }">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center flex-shrink-0 shadow-xs">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-1">
                            <h3 class="font-bold text-sm text-gray-900">Konfirmasi Penyelesaian Pesanan</h3>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="formattedTime">Menghitung...</span>
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">Rekan jasa telah menyelesaikan pekerjaan dan mengirim bukti. Silakan periksa hasil kerja sebelum mengonfirmasi.</p>
                    </div>
                </div>

                {{-- Alert info 24 jam auto-confirm --}}
                <div class="bg-amber-100/70 border border-amber-300/80 rounded-xl p-3 mb-4 text-xs text-amber-900 flex items-start gap-2.5">
                    <span class="text-base flex-shrink-0">⏳</span>
                    <div>
                        <span class="font-bold">Batas Konfirmasi 24 Jam:</span>
                        Jika Anda tidak melakukan konfirmasi atau komplain dalam 24 jam
                        @if($deadline)
                            (sebelum <span class="font-semibold underline">{{ $deadline->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</span>),
                        @endif
                        sistem akan <strong>langsung menyelesaikan pesanan secara otomatis dan mencairkan dana ke mitra</strong>.
                    </div>
                </div>

                <div class="space-y-2.5">
                    {{-- Tombol Utama: Konfirmasi Selesai --}}
                    <button type="button" wire:click="confirmCompletion" 
                            class="w-full font-bold py-3 px-4 rounded-xl shadow-xs bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer transition-all duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Konfirmasi Pesanan Selesai</span>
                    </button>

                    {{-- Tombol Kedua: Komplain / Tolak Hasil --}}
                    <button type="button" wire:click="openComplaintModal" 
                            class="w-full font-semibold py-2.5 px-4 rounded-xl border border-red-200 bg-white hover:bg-red-50/80 active:bg-red-100 text-red-600 flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer transition-all duration-200 shadow-2xs group">
                        <svg class="w-4 h-4 text-red-500 shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Tolak Hasil & Ajukan Komplain (Refund)</span>
                    </button>
                </div>
                <p class="text-[11px] text-center text-gray-500 mt-2.5">Pekerjaan belum selesai atau bermasalah? Ajukan komplain dengan bukti foto untuk mediasi & refund oleh Admin.</p>
            </div>
        @endif

        {{-- Modal Pengajuan Komplain / Refund --}}
        @if($showComplaintModal)
            <div class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-4 pb-20 sm:pb-6 bg-black/70 backdrop-blur-xs animate-in fade-in duration-200">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[82vh] border border-gray-100">
                    {{-- Fixed Header --}}
                    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-red-50/90 shrink-0">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                ⚠️
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Ajukan Komplain & Refund</h3>
                                <p class="text-[11px] text-gray-500">Mediasi penyelesaian oleh Admin</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeComplaintModal" class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 transition text-xs cursor-pointer">✕</button>
                    </div>

                    {{-- Form with Scrollable Body & Fixed Sticky Footer --}}
                    <form wire:submit.prevent="submitComplaint" class="flex flex-col flex-1 overflow-hidden min-h-0">
                        {{-- Scrollable Form Content --}}
                        <div class="p-5 overflow-y-auto space-y-4 flex-1 overscroll-contain">
                            {{-- Info Alert --}}
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 leading-relaxed flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Dana pesanan akan ditahan oleh sistem. Admin akan memeriksa bukti foto penyelesaian mitra dan bukti komplain Anda secara adil.</span>
                            </div>

                            {{-- Upload Bukti Foto (Wajib) --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-800 mb-1">
                                    Foto Bukti Masalah / Hasil Tidak Sesuai <span class="text-red-500">*</span>
                                </label>
                                <p class="text-[11px] text-gray-500 mb-2">Unggah foto yang memperlihatkan kondisi hasil kerja yang belum selesai atau bermasalah.</p>
                                
                                <div class="border-2 border-dashed border-gray-300 hover:border-red-400 rounded-xl p-3.5 text-center transition bg-gray-50/50">
                                    @if($complaint_photo)
                                        <div class="mb-2 relative inline-block">
                                            <img src="{{ $complaint_photo->temporaryUrl() }}" alt="Preview Bukti Komplain" class="w-32 h-24 object-cover rounded-lg border border-gray-200 mx-auto shadow-xs">
                                            <button type="button" wire:click="$set('complaint_photo', null)" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs shadow-sm hover:bg-red-600 cursor-pointer">✕</button>
                                        </div>
                                        <p class="text-xs font-semibold text-green-600">✓ Foto bukti siap diunggah</p>
                                    @else
                                        <svg class="w-7 h-7 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <label class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer shadow-2xs">
                                            <span>Pilih Foto Bukti</span>
                                            <input type="file" wire:model="complaint_photo" accept="image/*" class="hidden">
                                        </label>
                                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WebP (Maks. 5 MB)</p>
                                    @endif
                                    
                                    <div wire:loading wire:target="complaint_photo" class="text-xs text-blue-600 mt-1.5">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            Mengunggah foto...
                                        </span>
                                    </div>
                                </div>
                                @error('complaint_photo')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Alasan Komplain --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-800 mb-1">
                                    Penjelasan Alasan Komplain <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="complaint_reason" rows="3" placeholder="Jelaskan secara detail bagian mana yang belum selesai atau tidak sesuai deskripsi bantuan..."
                                    class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50 focus:bg-white transition resize-none"></textarea>
                                @error('complaint_reason')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Fixed Sticky Actions Footer --}}
                        <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2.5 shrink-0 shadow-xs">
                            <button type="button" wire:click="closeComplaintModal" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="submitComplaint, complaint_photo"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <span wire:loading.remove wire:target="submitComplaint">Kirim Komplain & Minta Refund</span>
                                <span wire:loading wire:target="submitComplaint" class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Rating Form --}}

        {{-- Rating Form --}}
        @if(in_array($help->status, ['selesai', 'completed']))
            @php
                $customerRating = \App\Models\Rating::where('help_id', $help->id)
                    ->where('rater_id', auth()->id())
                    ->where('type', 'customer_to_mitra')
                    ->first();
            @endphp

            @if($customerRating)
                {{-- Already Rated - Show Rating --}}
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 mt-2 px-4 py-4 border border-green-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-gray-900 mb-2">Rating Anda</h3>
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $customerRating->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm font-semibold text-gray-900">{{ $customerRating->rating }}/5</span>
                            </div>
                            @if($customerRating->review)
                                <p class="text-sm text-gray-700 italic">"{{ $customerRating->review }}"</p>
                            @endif
                            <div class="flex items-center gap-2 mt-2 flex-wrap">
                                <p class="text-xs text-gray-500">{{ $customerRating->created_at->diffForHumans() }}</p>
                                @if($customerRating->is_anonymous)
                                    <span class="inline-flex items-center gap-1 text-[11px] bg-white/80 text-gray-600 px-2 py-0.5 rounded border border-gray-200 font-medium">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                        Dikirim sebagai Anonim
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Rating Form --}}
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 mt-2 px-4 py-4 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-sm text-gray-900 mb-1">Bagaimana Pengalaman Anda?</h3>
                            <p class="text-xs text-gray-700">Berikan rating untuk {{ $help->mitra->name ?? 'mitra' }}</p>
                        </div>
                    </div>

                    {{-- Star Rating --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Rating *</label>
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button 
                                    type="button"
                                    wire:click="setRating({{ $i }})"
                                    class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-10 h-10 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            @if($rating > 0)
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
                        <textarea 
                            wire:model="review"
                            rows="3"
                            placeholder="Ceritakan pengalaman Anda..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-sm"
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

                    {{-- Anonymous Checkbox Option --}}
                    <div class="mb-4 bg-white/70 p-2.5 rounded-lg border border-yellow-200/70">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" wire:model="is_anonymous" class="w-4 h-4 text-orange-500 rounded border-gray-300 focus:ring-orange-400">
                            <span class="text-xs text-gray-800 font-medium">Kirim sebagai Anonim (Sembunyikan nama saya)</span>
                        </label>
                        <p class="text-[11px] text-gray-500 mt-0.5 ml-6">Nama Anda tidak akan ditampilkan kepada rekan jasa.</p>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        wire:click="submitRating"
                        wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-gray-900 font-semibold py-3 px-4 rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all duration-200 shadow-md flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <span wire:loading.remove class="text-gray-900 font-semibold">Kirim Rating</span>
                        <span wire:loading class="text-gray-900 font-semibold">Mengirim...</span>
                    </button>
                </div>
            @endif
        @endif

        {{-- Payment Details --}}
        <div class="bg-white mt-2 px-4 py-4">
            <h3 class="font-bold text-sm text-gray-900 mb-4">Rincian Pembayaran</h3>

            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700">Nominal Pokok Bantuan</span>
                    <span class="font-semibold text-gray-900">Rp{{ number_format($help->base_amount > 0 ? $help->base_amount : $help->amount, 0, ',', '.') }}</span>
                </div>

                {{-- Perlengkapan ditampilkan di bagian "Deskripsi & Detail" di atas --}}

                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700">Biaya Layanan ({{ (float)($help->customer_fee_percent ?? 10) }}%)</span>
                    <span class="font-semibold text-gray-900">Rp{{ number_format($help->customer_fee_amount > 0 ? $help->customer_fee_amount : 0, 0, ',', '.') }}</span>
                </div>

                @if(($help->admin_fee ?? 0) > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-700">Biaya Admin</span>
                    <span class="font-semibold text-gray-900">Rp{{ number_format($help->admin_fee, 0, ',', '.') }}</span>
                </div>
                @endif

                @if(!empty($help->voucher_code) && ($help->discount_amount ?? 0) > 0)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-500 font-semibold">{{ $help->voucher_code }}</span>
                        </div>
                        <span class="font-semibold text-red-500">-Rp{{ number_format($help->discount_amount ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="border-t pt-3 flex items-center justify-between">
                    <span class="font-bold text-gray-900">Total Pembayaran</span>
                    <span class="font-bold text-gray-900">Rp{{ number_format($help->total_customer_paid > 0 ? $help->total_customer_paid : ($help->total_amount ?? ($help->amount + ($help->admin_fee ?? 0) - ($help->discount_amount ?? 0))), 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-gray-700 leading-relaxed">
                    Kamu dapat meminta tindakan tambahan selama sesi layanan berlangsung (mis. tambahan bahan atau tindakan kecil). Pastikan semua pembayaran dilakukan melalui aplikasi agar pesananmu tercatat dan terlindungi. Jika ada masalah kualitas, ajukan keluhan atau klaim garansi dalam waktu 1x24 jam setelah layanan selesai.
                </p>
            </div>
        </div>

        {{-- Cancel Button dengan Logika Pembatas Dinamis --}}
        @if(in_array($help->status, ['menunggu_pembayaran', 'mencari_mitra', 'menunggu_mitra', 'memperoleh_mitra', 'taken', 'partner_on_the_way', 'partner_arrived']))
            <div id="customer-action-section" class="bg-white mt-2 px-4 py-4">
                @if($this->canCustomerCancel)
                    <div class="space-y-2.5">
                        @if($help->mitra_id)
                            <button type="button" wire:click="confirmReassign"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); color: #ffffff !important;"
                                class="w-full py-3 text-white rounded-xl font-bold text-sm shadow-md hover:opacity-95 transition active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="text-white font-bold">Cari Rekan Jasa Lain</span>
                            </button>
                            <p class="text-[11px] text-gray-500 text-center -mt-1 mb-1">Lepas rekan jasa saat ini dan cari rekan jasa baru tanpa biaya tambahan</p>
                        @endif

                        <button type="button" wire:click="confirmCancel"
                            class="w-full py-2.5 border border-red-300 text-red-600 rounded-xl font-semibold text-xs hover:bg-red-50 transition cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batalkan Pesanan</span>
                        </button>
                    </div>
                @else
                    <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-center space-y-1">
                        <p class="text-xs text-amber-900 font-semibold flex items-center justify-center gap-1.5">
                            <span>⏳</span>
                            <span>Pesanan Sedang Ditangani Rekan Jasa</span>
                        </p>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            {{ $this->cancelRestrictionMessage }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Partner requested cancellation - DIGANTI DENGAN MODAL --}}

        {{-- Floating Help Card (mobile) - fixed above bottom nav --}}
        {{-- <div id="floating-help-card" class="md:hidden fixed left-1/2 transform -translate-x-1/2 w-full max-w-md px-4 z-50" style="bottom: calc(env(safe-area-inset-bottom, 0px) + 76px);">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-3 flex items-center justify-between gap-3">
                <div class="flex-1 text-sm text-gray-700">Butuh bantuan atau ada keluhan atas Rekan Jasa?</div>
                <a href="{{ route('customer.help-support') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg font-semibold hover:bg-blue-100 transition">
                    <span class="text-sm">Hubungi Kami</span>
                </a>
            </div>
        </div> --}}
    </div>

    {{-- Real-time Tracking Map Modal --}}
    @if($showMapModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" wire:click="closeMapModal" data-tracking-modal>
            <div class="bg-white rounded-2xl w-full max-w-md mx-auto flex flex-col shadow-2xl" style="max-height: 85vh;" wire:click.stop>
                {{-- Header --}}
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-sm">Tracking Real-time</h3>
                            <p class="text-white/80 text-xs" x-text="'Lokasi ' + trackingData.partnerName"></p>
                        </div>
                    </div>
                    <button wire:click="closeMapModal" class="text-white hover:bg-white/20 p-1.5 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- ETA Info Bar --}}
                <div wire:ignore class="px-4 py-2.5 bg-blue-50 border-b border-blue-100 shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-500 flex items-center justify-center animate-pulse">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Estimasi Tiba</p>
                                <p class="text-xs font-bold text-blue-700" id="eta-time">Menghitung...</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">Jarak</p>
                            <p class="text-xs font-bold text-blue-700" id="distance-text">0.0 km</p>
                        </div>
                    </div>
                </div>

                {{-- Map Container --}}
                <div class="relative shrink-0" style="height: 400px;" wire:ignore>
                    <div id="tracking-map" class="w-full h-full"></div>
                    
                    {{-- Loading Overlay --}}
                    <div id="map-loading" class="absolute inset-0 bg-white/90 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                            <p class="text-xs text-gray-600">Memuat peta...</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-4 py-2.5 border-t border-gray-200 bg-gray-50 rounded-b-2xl shrink-0">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Lokasi diperbarui setiap 5 detik</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel Confirmation Modal with Reason Selection --}}
    @if($showCancelConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background: rgba(0, 0, 0, 0.6);" wire:click="closeModal" data-confirm-modal>
            <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl relative my-auto" wire:click.stop>
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-900 leading-tight">Batalkan Pesanan?</h3>
                        <p class="text-xs text-gray-500">Saldo akan dikembalikan 100% penuh ke akun Anda.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Alert info refund --}}
                <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl mb-3 text-xs text-emerald-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pengembalian dana: <strong>Rp{{ number_format($help->total_customer_paid > 0 ? $help->total_customer_paid : ($help->total_amount ?? ($help->amount + ($help->admin_fee ?? 0))), 0, ',', '.') }}</strong></span>
                </div>

                @if($help->status === 'partner_on_the_way')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl mb-4 text-xs text-amber-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span><strong>Catatan:</strong> Rekan jasa saat ini sudah berada dalam perjalanan menuju lokasi Anda. Harap pertimbangkan sebelum membatalkan.</span>
                    </div>
                @endif

                {{-- Radio Reason Selection --}}
                <div class="mb-4 text-left">
                    <label class="text-xs font-bold text-gray-700 block mb-2">
                        Pilih Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($this->availableCancelReasons as $reason)
                            <label class="flex items-start gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $selectedCancelReason === $reason ? 'border-red-500 bg-red-50/50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" wire:model.live="selectedCancelReason" value="{{ $reason }}" class="mt-0.5 text-red-600 focus:ring-red-500">
                                <span class="text-xs font-medium text-gray-800 leading-snug">{{ $reason }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedCancelReason')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Custom reason textarea when Lainnya selected --}}
                @if($selectedCancelReason === 'Lainnya')
                    <div class="mb-4 text-left">
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Tuliskan Alasan Anda <span class="text-red-500">*</span></label>
                        <textarea wire:model="customCancelReason" rows="2" placeholder="Contoh: Perlu reschedule atau ada keperluan mendesak..." class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                        @error('customCancelReason')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="closeModal" class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition cursor-pointer">
                        Kembali
                    </button>
                    <button type="button" wire:click="cancelHelp" wire:loading.attr="disabled" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl font-semibold text-sm hover:bg-red-700 transition cursor-pointer flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="cancelHelp">Konfirmasi Batal</span>
                        <span wire:loading wire:target="cancelHelp">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Cari Rekan Jasa Lain (Ringan & Instan tanpa animasi berat) --}}
    @if($showReassignConfirm)
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
             style="background: rgba(0, 0, 0, 0.6);"
             x-data
             @click.self="$wire.closeReassignModal()">
            <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-xl relative border border-gray-100 text-center"
                 @click.stop>
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>

                <h3 class="font-bold text-base text-gray-900 mb-1.5">Cari Rekan Jasa Lain?</h3>
                <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                    Rekan Jasa saat ini (<strong>{{ $help->mitra->name ?? 'Mitra' }}</strong>) akan dilepas dan tidak dapat mengambil pesanan ini lagi. Pesanan akan otomatis dicarikan Rekan Jasa baru yang siap.
                </p>

                @if($help->status === 'partner_on_the_way')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl mb-3 text-xs text-amber-900 flex items-start gap-2 text-left">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span><strong>Catatan:</strong> Rekan jasa saat ini sudah dalam perjalanan. Mohon pertimbangkan sebelum mengalihkan pesanan ke rekan jasa lain.</span>
                    </div>
                @endif

                <div class="p-3 bg-amber-50/80 rounded-2xl border border-amber-200 text-left mb-4 text-xs text-amber-900 space-y-1.5">
                    <div class="flex items-center gap-1.5 font-semibold">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Tidak perlu bayar lagi (saldo tetap aman)</span>
                    </div>
                    <div class="flex items-center gap-1.5 font-semibold">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Tidak perlu mengisi form bantuan lagi</span>
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button type="button" wire:click="closeReassignModal"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-xl font-semibold text-xs hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="reassignPartner" wire:loading.attr="disabled"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); color: #ffffff !important;"
                        class="flex-1 py-2.5 text-white rounded-xl font-bold text-xs shadow-md hover:opacity-95 transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span wire:loading.remove wire:target="reassignPartner" class="text-white font-bold">Ya, Cari Baru</span>
                        <span wire:loading wire:target="reassignPartner" class="text-white">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Mitra Request Pembatalan - Bottom Sheet --}}
    @if($help->status === 'partner_cancel_requested')
    <div class="modal-overlay fixed inset-0 z-[9999] flex items-end justify-center animate-fade-in" 
         style="background: rgba(0,0,0,0.7);"
         x-data="{ showModal: true }"
         x-show="showModal"
         x-cloak
         @click.self="showModal = false">
        <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl animate-slide-up relative" 
             @click.stop
             style="padding-bottom: env(safe-area-inset-bottom,24px);">
            
            {{-- Header --}}
            <div class="sticky top-0 bg-gradient-to-r from-orange-500 to-red-500 px-5 py-4 rounded-t-3xl">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-base font-bold">Permintaan Pembatalan</h3>
                    </div>
                    <button type="button" @click="showModal = false" class="p-1 rounded-full text-white/80 hover:text-white hover:bg-white/20 transition" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-5 pb-6">
                {{-- Warning Icon --}}
                <div class="flex items-center justify-center mb-4">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center relative">
                        <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <h4 class="text-center font-bold text-lg text-gray-900 mb-2">Mitra Mengajukan Pembatalan</h4>
                <p class="text-sm text-gray-700 text-center mb-5">
                    Rekan Jasa mengajukan pembatalan untuk pesanan ini. Silakan pilih tindakan yang Anda inginkan.
                </p>

                {{-- Mitra Info --}}
                @if($help->mitra)
                    <div class="bg-gray-50 rounded-xl p-4 mb-5">
                        <div class="flex items-center gap-3 mb-3">
                            @if($help->mitra->selfie_photo)
                                <img src="{{ asset('storage/' . $help->mitra->selfie_photo) }}" alt="{{ $help->mitra->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                            @else
                                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($help->mitra->name ?? 'M', 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-sm text-gray-900">{{ $help->mitra->name ?? 'Rekan Jasa' }}</p>
                                <p class="text-xs text-gray-500">Mengajukan pembatalan</p>
                            </div>
                        </div>

                        @if($help->partner_cancel_reason)
                            <div class="border-t border-gray-200 pt-3">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Alasan Pembatalan:</p>
                                <p class="text-sm text-gray-900 italic">"{{ $help->partner_cancel_reason }}"</p>
                            </div>
                        @endif

                        @if($help->partner_cancel_requested_at)
                            <div class="flex items-center gap-2 text-xs text-gray-600 mt-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Diajukan: {{ \Carbon\Carbon::parse($help->partner_cancel_requested_at)->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-5">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-blue-900 mb-1">Pilihan Anda:</p>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li><strong>• Terima:</strong> Pesanan dibatalkan, dana dikembalikan penuh</li>
                                <li><strong>• Tolak:</strong> Rekan Jasa harus melanjutkan pekerjaan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-2">
                    <button wire:click="acceptPartnerCancellation" 
                            wire:loading.attr="disabled"
                            class="w-full px-5 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span wire:loading.remove wire:target="acceptPartnerCancellation">Terima Pembatalan</span>
                        <span wire:loading wire:target="acceptPartnerCancellation">Memproses...</span>
                    </button>
                    <button wire:click="rejectPartnerCancellation"
                            wire:loading.attr="disabled"
                            class="w-full px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span wire:loading.remove wire:target="rejectPartnerCancellation">Tolak Pembatalan</span>
                        <span wire:loading wire:target="rejectPartnerCancellation">Memproses...</span>
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
</div>

{{-- Leaflet Maps Script - Load once, pushed to head --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <script>
        (function() {
            console.log('🚀 Map script loaded');
            
            let map;
            let partnerMarker;
            let customerMarker;
            let routingControl;
            let routePolyline;
            let initAttempts = 0;
            const maxAttempts = 50;
            let mapInitialized = false; // Flag untuk track map status

            function showError(message) {
                console.error('❌ Error:', message);
                const loading = document.getElementById('map-loading');
                if (loading) {
                    loading.innerHTML = `
                        <div class="text-center p-4">
                            <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-red-600 mb-2">${message}</p>
                            <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600">Muat Ulang Halaman</button>
                        </div>
                    `;
                }
            }

            // Wait for both Leaflet and Alpine to be ready
            function waitAndInit() {
                // Jika map sudah ada, skip init (cegah reinit saat Livewire polling)
                if (map) {
                    console.log('✅ Map already initialized, skipping...');
                    return;
                }
                
                initAttempts++;
                
                const hasLeaflet = typeof L !== 'undefined';
                const hasAlpine = typeof Alpine !== 'undefined';
                const hasContainer = document.getElementById('tracking-map') !== null;
                
                console.log(`⏳ Attempt ${initAttempts}/${maxAttempts}:`, { 
                    Leaflet: hasLeaflet, 
                    Alpine: hasAlpine,
                    Container: hasContainer,
                    mapExists: !!map
                });
                
                if (hasLeaflet && hasAlpine && hasContainer) {
                    console.log('✅ All dependencies ready! Initializing map...');
                    setTimeout(() => {
                        try {
                            initializeMap();
                        } catch (err) {
                            console.error('❌ Init error:', err);
                            showError('Error: ' + err.message);
                        }
                    }, 100);
                } else if (initAttempts >= maxAttempts) {
                    console.error('❌ Timeout waiting for dependencies');
                    showError('Timeout: Gagal memuat library peta');
                } else {
                    setTimeout(waitAndInit, 100);
                }
            }

            function initializeMap() {
                // Cegah double init
                if (map) {
                    console.log('⚠️ Map already exists, skipping initialization');
                    return;
                }
                
                console.log('🗺️ Starting map initialization...');
                
                // Get tracking data from Alpine
                let trackingData;
                try {
                    const alpineEl = document.querySelector('[x-data]');
                    if (!alpineEl) {
                        throw new Error('Alpine element tidak ditemukan');
                    }
                    trackingData = Alpine.$data(alpineEl).trackingData;
                    if (!trackingData) {
                        throw new Error('Tracking data tidak tersedia');
                    }
                } catch (err) {
                    console.error('❌ Error getting Alpine data:', err);
                    showError('Error mengakses data tracking');
                    return;
                }
            
            let partnerLat = parseFloat(trackingData.partnerLat);
            let partnerLng = parseFloat(trackingData.partnerLng);
            let customerLat = parseFloat(trackingData.customerLat);
            let customerLng = parseFloat(trackingData.customerLng);

            // Sanitasi dan default fallback
            if (isNaN(customerLat) || !customerLat) customerLat = -6.2088;
            if (isNaN(customerLng) || !customerLng) customerLng = 106.8456;
            if (isNaN(partnerLat) || !partnerLat) partnerLat = customerLat;
            if (isNaN(partnerLng) || !partnerLng) partnerLng = customerLng;

            console.log('📍 Koordinat aktif:', { 
                partner: { lat: partnerLat, lng: partnerLng },
                customer: { lat: customerLat, lng: customerLng }
            });

            // Initialize map centered between partner and customer
            const centerLat = (partnerLat + customerLat) / 2;
            const centerLng = (partnerLng + customerLng) / 2;

            console.log('🎯 Center peta:', { lat: centerLat, lng: centerLng });

            try {
                // Initialize map without the default Leaflet prefix in attribution
                map = L.map('tracking-map', {
                    zoomControl: true,
                    attributionControl: false
                }).setView([centerLat, centerLng], 14);
                console.log('✓ Map object created');
            } catch (err) {
                console.error('❌ Error creating map:', err);
                showError('Gagal membuat peta');
                return;
            }

            // Add OpenStreetMap tiles
            try {
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Add attribution control explicitly without the default Leaflet prefix/link
                try {
                    L.control.attribution({ prefix: false }).addTo(map);
                } catch (err) {
                    console.warn('Unable to set attribution prefix:', err);
                }
                console.log('✓ Tiles loaded');
            } catch (err) {
                console.error('❌ Error loading tiles:', err);
                showError('Gagal memuat tiles peta');
                return;
            }

            // Custom icon for partner (blue pulse)
            const partnerIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `
                    <div style="position: relative;">
                        <div style="position: absolute; width: 40px; height: 40px; background: rgba(37, 99, 235, 0.3); border-radius: 50%; animation: pulse 2s infinite;"></div>
                        <div style="position: absolute; width: 24px; height: 24px; margin: 8px; background: #2563eb; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>
                    </div>
                    <style>
                        @keyframes pulse {
                            0% { transform: scale(1); opacity: 1; }
                            50% { transform: scale(1.3); opacity: 0.5; }
                            100% { transform: scale(1); opacity: 1; }
                        }
                    </style>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            // Custom icon for customer (red marker)
            const customerIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `
                    <div style="position: relative;">
                        <svg width="32" height="42" viewBox="0 0 32 42" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 0C9.37 0 4 5.37 4 12c0 7.07 12 30 12 30s12-22.93 12-30c0-6.63-5.37-12-12-12z" fill="#dc2626"/>
                            <circle cx="16" cy="12" r="5" fill="white"/>
                        </svg>
                    </div>
                `,
                iconSize: [32, 42],
                iconAnchor: [16, 42]
            });

            // Create markers
            try {
                partnerMarker = L.marker([partnerLat, partnerLng], { 
                    icon: partnerIcon,
                    title: trackingData.partnerName
                }).addTo(map);
                console.log('✓ Partner marker created');

                customerMarker = L.marker([customerLat, customerLng], { 
                    icon: customerIcon,
                    title: 'Lokasi Anda'
                }).addTo(map);
                console.log('✓ Customer marker created');

                // Add popups
                partnerMarker.bindPopup(`
                    <div class="p-2">
                        <strong>${trackingData.partnerName}</strong><br>
                        <small>Sedang menuju ke lokasi Anda</small>
                    </div>
                `);

                customerMarker.bindPopup(`
                    <div class="p-2">
                        <strong>Lokasi Anda</strong><br>
                        <small>${trackingData.location}</small>
                    </div>
                `);
            } catch (err) {
                console.error('❌ Error creating markers:', err);
                showError('Gagal membuat marker');
                return;
            }

            // Calculate and display route
            try {
                calculateRoute(partnerLat, partnerLng, customerLat, customerLng);
                console.log('✓ Route calculation started');
            } catch (err) {
                console.error('⚠️ Warning: Route calculation failed:', err);
                // Continue anyway, map will still work without route
            }

            // Fit bounds to show both markers
            try {
                if (Math.abs(partnerLat - customerLat) < 0.0001 && Math.abs(partnerLng - customerLng) < 0.0001) {
                    map.setView([customerLat, customerLng], 16);
                } else {
                    const bounds = L.latLngBounds([
                        [partnerLat, partnerLng],
                        [customerLat, customerLng]
                    ]);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
                console.log('✓ Map bounds set');
            } catch (err) {
                console.error('❌ Error setting bounds:', err);
            }

            // Hide loading overlay dan set flag
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                loadingEl.style.display = 'none';
                console.log('✓ Loading overlay hidden');
            }
            
            mapInitialized = true;

            // Force map to refresh tiles after short delay
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                    console.log('🔄 Map size recalculated');
                }
            }, 100);

            // Update akan otomatis dari Livewire polling + Alpine hook
            console.log('✅ Map initialization complete! Auto-update enabled via Livewire polling (5s).');
        }

            function calculateRoute(fromLat, fromLng, toLat, toLng) {
            // Remove old routing control if exists
            if (routingControl) {
                try { map.removeControl(routingControl); } catch(e){}
                routingControl = null;
            }
            
            // Remove old polyline if exists
            if (routePolyline) {
                try { map.removeLayer(routePolyline); } catch(e){}
                routePolyline = null;
            }

            // Check if already arrived or very close
            const directDistance = calculateDistance(fromLat, fromLng, toLat, toLng);
            if (directDistance < 0.05 || (fromLat === toLat && fromLng === toLng)) {
                const distanceEl = document.getElementById('distance-text');
                const etaEl = document.getElementById('eta-time');
                if (distanceEl) distanceEl.textContent = '0 m';
                if (etaEl) etaEl.textContent = 'Sudah di lokasi';
                return;
            }

            // If Leaflet Routing Machine is available, use it. Otherwise fallback to straight-line.
            if (window.L && L.Routing && typeof L.Routing.control === 'function') {
                try {
                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(fromLat, fromLng),
                            L.latLng(toLat, toLng)
                        ],
                        routeWhileDragging: false,
                        addWaypoints: false,
                        draggableWaypoints: false,
                        fitSelectedRoutes: false,
                        showAlternatives: false,
                        lineOptions: {
                            styles: [{
                                color: '#2563eb',
                                opacity: 0.8,
                                weight: 5
                            }]
                        },
                        createMarker: function() { return null; }, // Don't create default markers
                        router: L.Routing.osrmv1({
                            serviceUrl: 'https://router.project-osrm.org/route/v1'
                        })
                    }).addTo(map);

                    // Hide the routing instructions panel
                    const routingContainer = document.querySelector('.leaflet-routing-container');
                    if (routingContainer) routingContainer.style.display = 'none';

                    // Listen for route found event
                    routingControl.on('routesfound', function(e) {
                        const routes = e.routes;
                        const route = routes[0];
                        
                        // Get distance and time
                        const distanceKm = (route.summary.totalDistance / 1000).toFixed(1);
                        const timeMinutes = Math.ceil(route.summary.totalTime / 60);
                        
                        // Calculate ETA
                        const hours = Math.floor(timeMinutes / 60);
                        const minutes = timeMinutes % 60;
                        let etaText = '';
                        
                        if (hours > 0) {
                            etaText = `${hours} jam ${minutes} menit`;
                        } else {
                            etaText = `${minutes} menit`;
                        }

                        // Update UI
                        document.getElementById('distance-text').textContent = distanceKm + ' km';
                        document.getElementById('eta-time').textContent = etaText;
                    });

                    // Fallback: routing error
                    routingControl.on('routingerror', function(err) {
                        console.warn('Routing error, falling back to straight-line:', err);
                        fallbackStraightLine();
                    });
                } catch (err) {
                    console.error('Routing control failed, fallback:', err);
                    fallbackStraightLine();
                }
            } else {
                // Routing library not available — fallback
                console.warn('Leaflet Routing Machine not available, using straight-line fallback');
                fallbackStraightLine();
            }

            function fallbackStraightLine() {
                const distance = calculateDistance(fromLat, fromLng, toLat, toLng);
                const distanceKm = distance.toFixed(1);

                // Draw straight line as fallback
                routePolyline = L.polyline([
                    [fromLat, fromLng],
                    [toLat, toLng]
                ], {
                    color: '#2563eb',
                    weight: 5,
                    opacity: 0.8,
                    dashArray: '10, 10'
                }).addTo(map);

                // Estimate time (assuming 40 km/h average speed)
                const estimatedMinutes = Math.ceil((distance / 40) * 60);

                const distanceEl = document.getElementById('distance-text');
                const etaEl = document.getElementById('eta-time');
                if (distanceEl) distanceEl.textContent = distanceKm + ' km';
                    if (etaEl) etaEl.textContent = estimatedMinutes + ' menit (estimasi)';
                }
            }

            function calculateDistance(lat1, lng1, lat2, lng2) {
            // Haversine formula for distance calculation
            const R = 6371; // Earth radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                     Math.sin(dLng/2) * Math.sin(dLng/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            // Fungsi untuk update peta dari Livewire event
            window.updateMapFromTracking = function(data) {
                if (!map || !partnerMarker) {
                    console.log('⚠️ Map or marker not ready yet');
                    return;
                }
                
                const newLat = parseFloat(data.partnerLat);
                const newLng = parseFloat(data.partnerLng);
                const customerLat = parseFloat(data.customerLat);
                const customerLng = parseFloat(data.customerLng);

                console.log('📍 Updating map from Livewire:', { 
                    partner: { lat: newLat, lng: newLng },
                    customer: { lat: customerLat, lng: customerLng }
                });

                if (newLat && newLng && !isNaN(newLat) && !isNaN(newLng) && partnerMarker) {
                    const currentLatLng = partnerMarker.getLatLng();
                    
                    // Hanya update jika posisi berubah
                    if (Math.abs(currentLatLng.lat - newLat) > 0.0001 || Math.abs(currentLatLng.lng - newLng) > 0.0001) {
                        console.log('🚶 Partner bergerak dari', currentLatLng, 'ke', {lat: newLat, lng: newLng});
                        
                        // Animate marker movement
                        animateMarker(partnerMarker, [newLat, newLng]);

                        // Recalculate route setelah marker bergerak
                        setTimeout(() => {
                            if (map && partnerMarker) {
                                calculateRoute(newLat, newLng, customerLat, customerLng);
                            }
                        }, 1000);
                    } else {
                        console.log('📍 Partner masih di posisi yang sama');
                    }
                }
            };
            
            // Backward compatibility
            window.updateMapFromAlpine = function() {
                try {
                    const trackingData = Alpine.$data(document.querySelector('[x-data]')).trackingData;
                    window.updateMapFromTracking(trackingData);
                } catch (err) {
                    console.error('Error in updateMapFromAlpine:', err);
                }
            };

            function updatePartnerLocation() {
                // Livewire polling akan trigger x-init hook yang memanggil updateMapFromAlpine
                // Fungsi ini tetap ada untuk kompatibilitas
                window.updateMapFromAlpine();
            }

            function animateMarker(marker, newLatLng) {
            const startLatLng = marker.getLatLng();
            const endLatLng = L.latLng(newLatLng);
            
            let step = 0;
            const numSteps = 50;
            const deltaLat = (endLatLng.lat - startLatLng.lat) / numSteps;
            const deltaLng = (endLatLng.lng - startLatLng.lng) / numSteps;

            const moveMarker = setInterval(() => {
                step++;
                const lat = startLatLng.lat + (deltaLat * step);
                const lng = startLatLng.lng + (deltaLng * step);
                marker.setLatLng([lat, lng]);

                    if (step >= numSteps) {
                        clearInterval(moveMarker);
                    }
                }, 20);
            }

            // Cleanup ketika modal ditutup
            window.addEventListener('beforeunload', () => {
                if (map) {
                    try { map.remove(); } catch(e) {}
                }
            });

            // Initialize map when modal opens (listen to Livewire)
            document.addEventListener('livewire:init', () => {
                Livewire.on('mapModalOpened', () => {
                    console.log('📢 Map modal opened');
                    initAttempts = 0; // Reset counter
                    setTimeout(waitAndInit, 100);
                });
                
                // Listen untuk tracking data updates dari Livewire
                Livewire.on('tracking-data-updated', (event) => {
                    console.log('📡 Tracking data updated event received:', event);
                    if (map && mapInitialized) {
                        window.updateMapFromTracking(event);
                    }
                });
            });

            // Also check on Livewire update - only init if modal exists and map doesn't
            Livewire.hook('morph.updated', ({ el, component }) => {
                const modalElement = document.querySelector('[wire\\:click="closeMapModal"]');
                
                // Jika modal ada dan map belum di-init
                if (modalElement && !map) {
                    console.log('📢 Modal detected in DOM, initializing...');
                    initAttempts = 0; // Reset counter
                    setTimeout(waitAndInit, 100);
                }
                
                // Jika modal tidak ada tapi map masih ada, cleanup
                if (!modalElement && map) {
                    console.log('🧹 Modal closed, cleaning up map...');
                    try {
                        map.remove();
                        map = null;
                        partnerMarker = null;
                        customerMarker = null;
                        routingControl = null;
                        routePolyline = null;
                        mapInitialized = false;
                    } catch (e) {
                        console.error('Error cleaning up map:', e);
                    }
                }
            });
        })();
    </script>

    <script>
        // Expose current help id so polling can start immediately
        window.currentHelpId = '{{ $help->id }}';

        // Client-side polling runs continuously and updates Alpine + map
        (function() {
            let pollingInterval = null;
            const POLL_MS = 4000; // poll every 4 seconds

            function startPolling(helpId) {
                if (!helpId) return;
                if (pollingInterval) return; // already running
                console.log('🔁 Starting tracking polling for help', helpId);
                // immediate fetch
                fetchAndUpdate(helpId);
                pollingInterval = setInterval(() => fetchAndUpdate(helpId), POLL_MS);
            }

            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    console.log('⏹️ Stopped tracking polling');
                }
            }

            async function fetchAndUpdate(helpId) {
                try {
                    const resp = await fetch(`/customer/helps/${helpId}/tracking`, {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) {
                        console.warn('Tracking endpoint returned', resp.status);
                        return;
                    }
                    const data = await resp.json();

                    // Update Alpine's trackingData so UI and any bindings reflect latest coords
                    try {
                        if (window.Alpine) {
                            const alpineEl = document.querySelector('[x-data]');
                            if (alpineEl) {
                                const alpine = Alpine.$data(alpineEl);
                                if (alpine && alpine.trackingData) {
                                    alpine.trackingData.partnerLat = data.partnerLat ?? alpine.trackingData.partnerLat;
                                    alpine.trackingData.partnerLng = data.partnerLng ?? alpine.trackingData.partnerLng;
                                    alpine.trackingData.customerLat = data.customerLat ?? alpine.trackingData.customerLat;
                                    alpine.trackingData.customerLng = data.customerLng ?? alpine.trackingData.customerLng;
                                    alpine.trackingData.partnerName = data.partnerName ?? alpine.trackingData.partnerName;
                                }
                            }
                        }
                    } catch (err) {
                        console.warn('Failed updating Alpine data', err);
                    }

                    // Call existing global function used by map code to update markers & route
                    if (window.updateMapFromTracking && data) {
                        window.updateMapFromTracking({
                            partnerLat: data.partnerLat,
                            partnerLng: data.partnerLng,
                            customerLat: data.customerLat,
                            customerLng: data.customerLng
                        });
                    }

                    // Calculate simple straight-line distance + ETA fallback and update summary + modal placeholders
                    try {
                        const pLat = parseFloat(data.partnerLat);
                        const pLng = parseFloat(data.partnerLng);
                        const cLat = parseFloat(data.customerLat);
                        const cLng = parseFloat(data.customerLng);

                        if (!isNaN(pLat) && !isNaN(pLng) && !isNaN(cLat) && !isNaN(cLng)) {
                            // Haversine formula (km)
                            function haversine(lat1, lon1, lat2, lon2) {
                                const R = 6371; // km
                                const dLat = (lat2 - lat1) * Math.PI / 180;
                                const dLon = (lon2 - lon1) * Math.PI / 180;
                                const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
                                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                return R * c;
                            }

                            const distKm = haversine(pLat, pLng, cLat, cLng);
                            const distText = distKm >= 1 ? distKm.toFixed(1) + ' km' : Math.round(distKm * 1000) + ' m';

                            // Estimate time assuming avg speed 30 km/h in city
                            const estMinutes = Math.max(1, Math.ceil((distKm / 30) * 60));
                            const hours = Math.floor(estMinutes / 60);
                            const minutes = estMinutes % 60;
                            const etaText = hours > 0 ? `${hours} jam ${minutes} menit` : `${minutes} menit`;

                            // Update always-visible summary
                            const summaryD = document.getElementById('summary-distance');
                            const summaryE = document.getElementById('summary-eta');
                            if (summaryD) summaryD.textContent = distText;
                            if (summaryE) summaryE.textContent = etaText;

                            // Also update modal placeholders if present
                            const modalD = document.getElementById('distance-text');
                            const modalE = document.getElementById('eta-time');
                            if (modalD) modalD.textContent = distText;
                            if (modalE) modalE.textContent = etaText;
                        }
                    } catch (err) {
                        console.warn('Failed calculating distance/ETA fallback', err);
                    }

                } catch (err) {
                    console.error('Error fetching tracking data:', err);
                }
            }

            // Start polling immediately for the current help id (so modal doesn't need open/close)
            try {
                const initialId = window.currentHelpId || null;
                if (initialId) startPolling(initialId);
            } catch (e) {
                console.error('Error starting initial polling:', e);
            }

            // Hook into Livewire modal events to ensure polling persists or can be stopped if desired
            document.addEventListener('livewire:init', () => {
                Livewire.on('mapModalOpened', (helpId) => {
                    const idToUse = helpId || window.currentHelpId;
                    startPolling(idToUse);
                });

                // stop polling when modal closed (optional) - we will keep polling in background, so don't stop here
                Livewire.on('mapModalClosed', () => {
                    // intentionally left blank to allow continuous background polling
                });
            });

            // Stop polling on page unload
            window.addEventListener('beforeunload', stopPolling);
        })();
    </script>

    {{-- Toast notification for copy --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copied', (event) => {
                // Show toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                toast.textContent = 'ID Pesanan disalin: ' + event.orderId;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 2000);
            });
        });
    </script>
@endpush

{{-- Flash Messages --}}
@if(session()->has('success'))
    <div class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down">
        {{ session('success') }}
    </div>
@endif

@if(session()->has('error'))
    <div class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down">
        {{ session('error') }}
    </div>
@endif