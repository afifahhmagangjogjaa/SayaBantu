<div wire:poll.30s="checkForIdlePartner">
    @if($showBanner && $activeHelp)
        {{-- Floating Toast Notification di Atas Layar --}}
        <div x-data="{
                visible: true,
                storageKey: 'idle_alert_ack_{{ $activeHelp->id }}_{{ $activeHelp->mitra_id }}',
                init() {
                    if (sessionStorage.getItem(this.storageKey)) {
                        this.visible = false;
                        $wire.dismissBanner();
                    }
                },
                handleOpen() {
                    this.visible = false;
                    sessionStorage.setItem(this.storageKey, '1');
                    $wire.dismissBanner();
                    const isCurrentHelpDetail = window.location.pathname.includes('/customer/helps/{{ $activeHelp->id }}');
                    if (isCurrentHelpDetail) {
                        const target = document.getElementById('idle-partner-warning-banner') || document.getElementById('customer-action-section');
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        window.location.href = '{{ route('customer.helps.detail', $activeHelp->id) }}#customer-action-section';
                    }
                },
                handleDismiss() {
                    this.visible = false;
                    sessionStorage.setItem(this.storageKey, '1');
                    $wire.dismissBanner();
                }
             }"
             x-show="visible"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[99999] w-[94%] max-w-md animate-slide-down">
            <div @click="handleOpen()"
                 class="bg-white rounded-2xl shadow-xl border border-amber-300 p-3.5 flex items-center justify-between gap-3 cursor-pointer hover:shadow-2xl hover:border-amber-400 transition group active:scale-98">
                
                {{-- Icon Badge --}}
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-600 flex-shrink-0 group-hover:scale-105 transition">
                    <svg class="w-6 h-6 animate-pulse text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                {{-- Content Text --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 mb-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        <h4 class="text-xs font-bold text-gray-900 tracking-tight truncate">Rekan Jasa Belum Berangkat</h4>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">
                        Belum menuju lokasi selama lebih dari 30 menit
                    </p>
                </div>

                {{-- Action Arrow / Close --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 text-white text-[11px] font-bold rounded-xl shadow-xs group-hover:bg-amber-600 transition">
                        <span>Buka</span>
                        <span>&rarr;</span>
                    </span>
                    <button type="button" 
                            @click.stop="handleDismiss()"
                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition cursor-pointer"
                            title="Tutup notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
