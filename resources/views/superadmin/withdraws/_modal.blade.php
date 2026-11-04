<div id="superadmin-withdraw-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <div id="superadmin-withdraw-modal-overlay" class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-md mx-auto bg-white rounded-xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto no-scrollbar">
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Withdraw Request</h3>
                        <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $withdraw->withdraw_code }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Detail permintaan penarikan saldo</p>
                </div>
                <button id="close-superadmin-withdraw-modal"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Status Badge -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($withdraw->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-base font-semibold text-gray-900">{{ $withdraw->user?->name ?? '-' }}</h4>
                            @if($withdraw->user?->role === 'customer')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Customer</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Mitra</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">ID {{ $withdraw->user?->role === 'customer' ? 'Customer' : 'Mitra' }}: {{ $withdraw->user_id }}</p>
                    </div>
                </div>
                <div>
                    @if($withdraw->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Pending
                        </span>
                    @elseif($withdraw->status === 'processing')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing
                        </span>
                    @elseif($withdraw->status === 'success')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Success
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Failed
                        </span>
                    @endif
                </div>
            </div>

            <!-- Amount Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900 mb-1">Jumlah Penarikan</p>
                        <p class="text-3xl font-bold text-blue-900">
                            Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-blue-700 mb-1">Bank / E-Wallet</p>
                        <p class="text-sm font-semibold text-blue-900">{{ $withdraw->bank_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 mb-1">No. Rekening / No. HP</p>
                        <p class="text-sm font-semibold text-blue-900 font-mono">{{ $withdraw->account_number }}</p>
                    </div>
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs text-blue-700">Nama Pemilik Rekening</p>
                            @php
                                $accName = trim(strtolower($withdraw->account_name ?: ''));
                                $mitraClean = trim(strtolower($withdraw->user?->clean_name ?? $withdraw->user?->name ?? ''));
                                $isMatch = ($accName !== '' && $mitraClean !== '') && ($accName === $mitraClean || str_contains($accName, $mitraClean) || str_contains($mitraClean, $accName));
                            @endphp
                            @if($isMatch)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-100/70 border border-emerald-300 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Sesuai Identitas {{ $withdraw->user?->role === 'customer' ? 'Customer' : 'Mitra' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-800 bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-full">
                                    ⚠️ Perbedaan Nama Rekening
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-blue-950">{{ $withdraw->account_name ?: ($withdraw->user?->clean_name ?? $withdraw->user?->name ?? '-') }}</p>
                    </div>
                </div>
                @if($withdraw->description)
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <p class="text-xs text-blue-700 mb-1">Keterangan</p>
                    <p class="text-sm text-blue-900">{{ $withdraw->description }}</p>
                </div>
                @endif
            </div>

            @if($withdraw->status === 'pending')
                <!-- Approval Form -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h5 class="text-sm font-semibold text-gray-900 mb-4">Form Approval & Bukti Transfer</h5>
                    <form action="{{ route('superadmin.withdraws.approve', $withdraw) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unggah Bukti Transfer <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="file" name="transfer_proof" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#0066FF] hover:file:bg-blue-100 border border-gray-300 rounded-lg p-1.5 transition cursor-pointer" />
                                <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maks 5MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Transfer <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="text" name="transfer_reference"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow text-sm"
                                    placeholder="Contoh: No. referensi bank / ID transaksi" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="text" name="note" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow text-sm"
                                    placeholder="Tambahkan catatan jika diperlukan" />
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <button type="button" id="open-reject-local"
                                style="display: inline-flex; align-items: center; justify-content: center; height: 44px; width: 100%;"
                                class="w-full h-11 px-4 inline-flex items-center justify-center gap-2 rounded-xl border border-red-500 bg-white text-red-600 hover:bg-red-50 font-bold text-xs sm:text-sm leading-none transition-all duration-150 active:scale-95 cursor-pointer shadow-xs">
                                <svg class="w-4 h-4 shrink-0 text-red-500" style="display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="leading-none inline-flex items-center">Tolak</span>
                            </button>
                            <button type="submit" 
                                style="display: inline-flex; align-items: center; justify-content: center; height: 44px; width: 100%;"
                                class="w-full h-11 px-5 inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-600 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm leading-none shadow-sm shadow-emerald-600/25 transition-all duration-150 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0 text-white" style="display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="leading-none inline-flex items-center">Setujui Transfer</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Processed Info -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h5 class="text-sm font-semibold text-gray-900 mb-4">Informasi Pemrosesan</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($withdraw->status) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Diproses pada</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $withdraw->processed_at ? $withdraw->processed_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                        @if($withdraw->external_id)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Referensi</span>
                            <span class="text-sm font-mono font-semibold text-gray-900">{{ $withdraw->external_id }}</span>
                        </div>
                        @endif
                        @if($withdraw->transfer_proof)
                        <div class="pt-3 border-t border-gray-200">
                            <span class="text-sm text-gray-600 block mb-2 font-medium">Bukti Transfer:</span>
                            <a href="{{ asset('storage/' . $withdraw->transfer_proof) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-[#0066FF] rounded-lg text-xs font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Buka Bukti Transfer
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Fallback Reject Modal (used when modal is injected dynamically) -->
    <div id="withdraw-reject-modal-fallback" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div id="withdraw-reject-fallback-overlay" class="absolute inset-0 bg-gray-900/60 backdrop-blur-xs"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto z-10 overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900">Tolak Permintaan Withdraw</h3>
                <button id="close-reject-fallback" class="text-gray-400 hover:text-gray-600 p-1 text-lg rounded-lg hover:bg-gray-100 transition cursor-pointer">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">Masukkan alasan penolakan. Saldo penarikan akan otomatis dikembalikan ke saldo dompet {{ $withdraw->user?->role === 'customer' ? 'customer' : 'mitra' }}.</p>
                <form action="{{ route('superadmin.withdraws.reject', $withdraw) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penolakan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="note" class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition outline-none"
                            placeholder="Contoh: Nomor rekening tidak valid atau nama tidak sesuai" />
                    </div>
                    <div class="grid grid-cols-2 gap-2.5 pt-2">
                        <button type="button" id="cancel-reject-fallback"
                            class="w-full h-10 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold transition active:scale-95 cursor-pointer">Batal</button>
                        <button type="submit" class="w-full h-10 inline-flex items-center justify-center rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition active:scale-95 cursor-pointer shadow-sm shadow-red-600/20">Konfirmasi Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
