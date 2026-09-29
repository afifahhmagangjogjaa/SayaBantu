<div>
    <div wire:poll.3s="poll"></div>

    {{-- MODAL POP-UP MERAH SURAT PERINGATAN (COMPACT & PROPORTIONAL) --}}
    @if($sanctionModalOpen && $currentSanction)
        <div class="fixed inset-0 z-[99999999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs"
             x-data
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             style="position: fixed; inset: 0; z-index: 99999999; display: flex; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.75);">
            
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden relative"
                 style="max-width: 340px; width: 90%; background-color: #ffffff; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); border: 2px solid rgba(239, 68, 68, 0.3);">
                
                <!-- Red Gradient Header (Compact) -->
                <div class="text-center text-white relative"
                     style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #991b1b 100%); padding: 18px 16px 14px 16px;">
                    
                    <!-- Close button on top-right -->
                    <button wire:click="dismissSanctionModal('{{ $currentSanction['id'] }}')"
                            type="button"
                            class="transition cursor-pointer"
                            style="position: absolute; top: 12px; right: 12px; width: 28px; height: 28px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.25); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; border: none;"
                            title="Tutup">
                        ✕
                    </button>

                    <!-- Warning Icon -->
                    <div style="width: 44px; height: 44px; margin: 0 auto 8px auto; border-radius: 14px; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); display: flex; align-items: center; justify-content: center; color: #ffffff;">
                        <svg style="width: 24px; height: 24px;" class="animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <span style="display: inline-block; padding: 3px 12px; border-radius: 9999px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; background-color: #ffffff; color: #b91c1c; margin-bottom: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        SURAT PERINGATAN {{ $currentSanction['warning_level'] ?? 1 }}
                    </span>

                    <h3 style="font-size: 15px; font-weight: 800; line-height: 1.25; margin: 2px 0 0 0; color: #ffffff;">
                        {{ ($currentSanction['warning_level'] ?? 1) >= 3 ? 'Akun Dinonaktifkan / Diblokir' : 'Peringatan Akun Resmi' }}
                    </h3>
                    <p style="font-size: 10px; color: rgba(254, 226, 226, 0.9); margin-top: 2px;">
                        Diterbitkan oleh Admin SayaBantu
                    </p>
                </div>

                <!-- Modal Body (Compact) -->
                <div style="padding: 14px 16px; display: flex; flex-direction: column; gap: 10px;">
                    @if(!empty($currentSanction['reason']))
                        <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 10px 12px; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 5px; color: #991b1b; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 2px;">
                                <svg style="width: 13px; height: 13px; color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                                </svg>
                                Alasan Sanksi Admin:
                            </div>
                            <p style="color: #450a0a; font-weight: 600; font-size: 11px; line-height: 1.4; margin: 0;">
                                {{ $currentSanction['reason'] }}
                            </p>
                        </div>
                    @endif

                    <div style="font-size: 11px; color: #4b5563; background-color: #f9fafb; padding: 10px 12px; border-radius: 12px; border: 1px solid #f3f4f6; text-align: center; line-height: 1.45;">
                        @if(($currentSanction['warning_level'] ?? 0) >= 3)
                            <span style="color: #dc2626; font-weight: bold; display: block; margin-bottom: 2px;">⛔ Akun Diblokir Permanen</span>
                            Akun Anda telah dinonaktifkan dari sistem karena menerima Surat Peringatan 3.
                        @elseif(($currentSanction['warning_level'] ?? 0) == 2)
                            <span style="color: #dc2626; font-weight: bold; display: block; margin-bottom: 2px;">⚠️ PERINGATAN KERAS</span>
                            Jika mencapai Surat Peringatan 3, akun Anda akan otomatis diblokir secara permanen.
                        @else
                            Harap selalu mematuhi syarat & tata tertib layanan SayaBantu agar akun tetap aman.
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; flex-direction: column; gap: 6px; padding-top: 4px;">
                        <button wire:click="dismissSanctionModal('{{ $currentSanction['id'] }}')"
                                type="button"
                                class="transition active:scale-95 cursor-pointer"
                                style="width: 100%; padding: 10px 14px; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #ffffff; font-weight: 700; font-size: 12px; border-radius: 12px; border: none; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.35); display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            @if(($currentSanction['warning_level'] ?? 0) >= 3)
                                <span>Saya Mengerti &mdash; Keluar dari Akun</span>
                            @else
                                <span>Saya Mengerti &amp; Akan Mematuhi</span>
                            @endif
                        </button>

                        <button wire:click="viewSanctionDetail('{{ $currentSanction['id'] }}')"
                                type="button"
                                class="transition active:scale-95 cursor-pointer"
                                style="width: 100%; padding: 8.5px 14px; background-color: #fef2f2; color: #b91c1c; font-weight: 700; font-size: 11.5px; border-radius: 12px; border: 1px solid #fecaca; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Lihat Detail Surat Peringatan &rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('help-taken', (event) => {
            console.log('Livewire help-taken received:', event);
            window.dispatchEvent(new CustomEvent('help-taken', { detail: event }));
        });

        Livewire.on('help-new-message', (event) => {
            console.log('Livewire help-new-message received:', event);
            window.dispatchEvent(new CustomEvent('help-new-message', { detail: event }));
        });
    });
</script>
