@php
    $rejectedReg = null;
    if (auth()->check() && !request()->routeIs('profile.settings.verification')) {
        $user = auth()->user();
        if (in_array($user->role, ['customer', 'mitra', 'kustomer'])) {
            $rejectedReg = \App\Models\Registration::where('email', $user->email)
                ->where('status', 'rejected')
                ->first();
        }
    }
@endphp

@if($rejectedReg)
<div id="ktp-rejected-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[999999] flex items-center justify-center p-4 transition-all">
    <div class="bg-white rounded-2xl p-5 text-center shadow-2xl border border-gray-100 relative"
         style="max-width: 320px !important; width: 90% !important; margin: 0 auto !important;">
        <!-- Close button top-right -->
        <button type="button" onclick="closeKtpRejectedModal()" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Warning Icon Compact -->
        <div class="w-11 h-11 bg-red-50 text-red-600 rounded-xl flex items-center justify-center mx-auto mb-2.5 border border-red-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h3 class="text-sm font-bold text-gray-900 mb-1">Verifikasi Identitas Ditolak</h3>
        <p class="text-[11px] text-gray-500 mb-2.5 leading-relaxed">
            Pengajuan foto KTP & selfie Anda ditolak oleh Admin.
        </p>

        <!-- Compact Reason Box -->
        <div class="bg-red-50 border border-red-200/80 rounded-xl p-2.5 mb-3 text-left">
            <span class="text-[10px] font-bold uppercase tracking-wider text-red-700 block mb-0.5">Alasan:</span>
            <p class="text-xs font-semibold text-red-900 leading-snug">
                {{ $rejectedReg->rejection_reason ?: 'Dokumen tidak memenuhi persyaratan verifikasi.' }}
            </p>
        </div>

        <!-- Action Button -->
        <a href="{{ route('profile.settings.verification') }}" 
           style="background-color: #dc2626 !important; color: #ffffff !important;"
           class="w-full py-2.5 px-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs shadow-md shadow-red-200 transition flex items-center justify-center gap-1.5">
            <span>Perbaiki Dokumen</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>

        <button type="button" onclick="closeKtpRejectedModal()" class="mt-2 text-[11px] font-medium text-gray-400 hover:text-gray-600 transition">
            Nanti Saja
        </button>
    </div>
</div>

<script>
    (function checkKtpRejectedModalDismissal() {
        if (sessionStorage.getItem('dismissed_ktp_rejected_modal') === 'true') {
            const modal = document.getElementById('ktp-rejected-modal');
            if (modal) modal.style.display = 'none';
        }
    })();

    function closeKtpRejectedModal() {
        const modal = document.getElementById('ktp-rejected-modal');
        if (modal) {
            modal.style.display = 'none';
        }
        sessionStorage.setItem('dismissed_ktp_rejected_modal', 'true');
    }
</script>
@endif
