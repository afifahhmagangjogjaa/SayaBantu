<div class="min-h-screen bg-white">
    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <a href="{{ route('customer.dashboard') }}" aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold">Riwayat Top-Up</h1>
                        <p class="text-xs text-white/90 mt-0.5">Semua request top-up Anda</p>
                    </div>

                    <div class="w-9"></div>
                </div>
            </div>

            <!-- Curved separator -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24">
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl text-sm text-green-700 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-sm text-red-700 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="mb-5 flex gap-2 overflow-x-auto pb-2">
                <button wire:click="filterByStatus('all')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $filterStatus === 'all' ? 'text-white' : 'text-gray-600 bg-gray-100' }}"
                    style="{{ $filterStatus === 'all' ? 'background: linear-gradient(to bottom right, #0098e7, #0060b0);' : '' }}">
                    Semua
                </button>
                <button wire:click="filterByStatus('waiting_approval')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $filterStatus === 'waiting_approval' ? 'bg-yellow-500 text-white' : 'text-gray-600 bg-gray-100' }}">
                    Menunggu
                </button>
                <button wire:click="filterByStatus('approved')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $filterStatus === 'approved' ? 'bg-green-500 text-white' : 'text-gray-600 bg-gray-100' }}">
                    Disetujui
                </button>
                <button wire:click="filterByStatus('rejected')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $filterStatus === 'rejected' ? 'bg-red-500 text-white' : 'text-gray-600 bg-gray-100' }}">
                    Ditolak
                </button>
            </div>

            <!-- Transaction List -->
            <div class="space-y-3">
                @forelse($transactions as $transaction)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-bold text-gray-900 text-sm">{{ $transaction->request_code ?? '#'.$transaction->id }}</h3>
                                    @if($transaction->status === 'waiting_approval')
                                        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                            Menunggu
                                        </span>
                                    @elseif($transaction->status === 'approved' || $transaction->status === 'completed')
                                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Disetujui
                                        </span>
                                    @elseif($transaction->status === 'rejected')
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                            Ditolak
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-base font-bold text-gray-900">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">+{{ number_format($transaction->admin_fee, 0, ',', '.') }} admin</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="text-sm">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-bold text-gray-900 ml-1">Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}</span>
                            </div>
                            <button wire:click="viewDetail({{ $transaction->id }})"
                                class="text-sm font-semibold hover:underline"
                                style="color: #0098e7;">
                                Detail →
                            </button>
                        </div>

                        @if($transaction->status === 'rejected' && $transaction->rejection_reason)
                            <div class="mt-3 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                <p class="text-xs font-semibold text-red-800 mb-1">Alasan Penolakan:</p>
                                <p class="text-xs text-red-700">{{ $transaction->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Riwayat</h3>
                        <p class="text-sm text-gray-500 mb-6">Mulai top-up saldo Anda sekarang</p>
                        <a href="{{ route('customer.topup.request') }}"
                            class="inline-block px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transition"
                            style="background: linear-gradient(to bottom right, #0098e7, #0060b0);">
                            Top-Up Saldo
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedTransaction)
        @php
            $isRejected = ($selectedTransaction->status === 'rejected');
            $isWaiting = ($selectedTransaction->status === 'waiting_approval');
            $isApproved = ($selectedTransaction->status === 'approved' || $selectedTransaction->status === 'completed');
        @endphp
        <!-- Sembunyikan menu navigasi bawah saat pop-up terbuka agar layar tertutup penuh -->
        <style>
            #bottom-nav { display: none !important; }
        </style>
        <div class="modal-backdrop-open fixed inset-0 bg-black/70 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" wire:click="closeModal">
            <div class="bg-white rounded-3xl w-full max-w-sm max-h-[85vh] flex flex-col shadow-2xl overflow-hidden transform transition-all my-auto" wire:click.stop>
                <!-- Dynamic Header Banner -->
                @if($isRejected)
                    <div class="px-5 pt-6 pb-9 relative overflow-hidden text-white text-center rounded-t-3xl shrink-0" style="background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);">
                @elseif($isWaiting)
                    <div class="px-5 pt-6 pb-9 relative overflow-hidden text-white text-center rounded-t-3xl shrink-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                @else
                    <div class="px-5 pt-6 pb-9 relative overflow-hidden text-white text-center rounded-t-3xl shrink-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                @endif
                    <!-- Decorative circles -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mx-auto mb-2.5 shadow-md shrink-0" style="width: 56px; height: 56px;">
                            @if($isRejected)
                                <span class="text-2xl leading-none">❌</span>
                            @elseif($isWaiting)
                                <span class="text-2xl leading-none">⏳</span>
                            @else
                                <span class="text-2xl leading-none">✅</span>
                            @endif
                        </div>
                        <h2 class="text-base font-bold tracking-tight">
                            @if($isRejected)
                                Permintaan Top-Up Ditolak
                            @elseif($isWaiting)
                                Menunggu Verifikasi
                            @else
                                Top-Up Berhasil
                            @endif
                        </h2>
                        <p class="text-xs text-white/90 mt-0.5">
                            @if($isRejected)
                                Dana tidak ditambahkan ke saldo
                            @elseif($isWaiting)
                                Sedang diverifikasi oleh admin
                            @else
                                Saldo telah ditambahkan ke akun
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="bg-white rounded-t-3xl -mt-4 px-5 pt-4 pb-5 space-y-3.5 overflow-y-auto flex-1">
                    <!-- Nominal Besar -->
                    <div class="text-center pb-2 border-b border-gray-100">
                        <p class="text-[11px] font-bold uppercase tracking-wider {{ $isRejected ? 'text-rose-600' : ($isWaiting ? 'text-amber-600' : 'text-gray-500') }}">
                            Nominal Top-Up
                        </p>
                        <p class="text-2xl font-black mt-0.5 {{ $isRejected ? 'text-gray-400 line-through' : ($isWaiting ? 'text-amber-600' : 'text-emerald-600') }}">
                            Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }}
                        </p>
                        @if($isRejected)
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                Ditolak • Tidak masuk ke saldo
                            </span>
                        @elseif($isWaiting)
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu persetujuan admin
                            </span>
                        @endif
                    </div>

                    <!-- Kotak Alasan Penolakan (Langsung di atas agar mudah dibaca) -->
                    @if($isRejected)
                        <div class="bg-rose-50/90 border border-rose-200 rounded-2xl p-3.5 space-y-2">
                            <div class="flex items-center gap-1.5 text-rose-800 font-bold text-xs uppercase tracking-wide">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Alasan Penolakan:</span>
                            </div>
                            <p class="text-xs text-rose-900 font-medium bg-white/90 rounded-xl p-2.5 border border-rose-100 leading-relaxed">
                                {{ $selectedTransaction->rejection_reason ?: 'Bukti transfer tidak valid atau dana tidak masuk.' }}
                            </p>
                            @if($selectedTransaction->approved_by && $selectedTransaction->approvedBy)
                                <div class="pt-2 border-t border-rose-200/60 flex justify-between items-center text-[11px] text-rose-600 font-medium">
                                    <span>Ditolak oleh: <strong>{{ $selectedTransaction->approvedBy->name }}</strong></span>
                                    <span>{{ $selectedTransaction->approved_at?->format('d M Y, H:i') }} WIB</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Detail Rincian List -->
                    <div class="bg-gray-50/80 border border-gray-200/80 rounded-2xl p-3.5 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Status</span>
                            @if($selectedTransaction->status === 'waiting_approval')
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800 text-[11px]">Menunggu Approval</span>
                            @elseif($selectedTransaction->status === 'approved' || $selectedTransaction->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[11px]">Berhasil</span>
                            @elseif($selectedTransaction->status === 'rejected')
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800 text-[11px]">Ditolak</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Kode Request</span>
                            <span class="font-mono font-bold text-gray-900">{{ $selectedTransaction->request_code ?? '#'.$selectedTransaction->id }}</span>
                        </div>
                        @if(!empty($selectedTransaction->order_id))
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Order ID</span>
                                <span class="font-mono font-bold text-gray-900">{{ $selectedTransaction->order_id }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Waktu Pengajuan</span>
                            <span class="font-medium text-gray-800">{{ $selectedTransaction->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Metode</span>
                            <span class="font-semibold text-gray-900">{{ ucwords(str_replace(['_', 'bank'], [' ', 'Transfer Bank '], $selectedTransaction->payment_method ?? '-')) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Nominal Pokok</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedTransaction->admin_fee > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Biaya Admin</span>
                                <span class="font-medium text-gray-900">Rp {{ number_format($selectedTransaction->admin_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200/80 pt-2 flex justify-between items-center font-bold">
                            <span class="text-gray-800">Total Ditransfer</span>
                            <span class="text-blue-600 text-sm">Rp {{ number_format($selectedTransaction->total_payment, 0, ',', '.') }}</span>
                        </div>

                        <!-- Disetujui Oleh (Jika Berhasil) -->
                        @if(($selectedTransaction->status === 'approved' || $selectedTransaction->status === 'completed') && $selectedTransaction->approved_by && $selectedTransaction->approvedBy)
                            <div class="border-t border-gray-200/80 pt-2 flex justify-between items-center text-[11px] text-emerald-700">
                                <span>Disetujui oleh: <strong>{{ $selectedTransaction->approvedBy->name }}</strong></span>
                                <span>{{ $selectedTransaction->approved_at?->format('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>

                    <!-- Bukti Transfer (Clean Compact Preview) -->
                    @if($selectedTransaction->proof_of_payment)
                        <div class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700">Bukti Transfer</span>
                                <a href="{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <span>Buka Gambar Penuh</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-900/5 h-28 flex items-center justify-center group cursor-pointer" onclick="window.open('{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}', '_blank')">
                                <img src="{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}" 
                                    class="w-full h-full object-contain group-hover:scale-105 transition duration-200"
                                    alt="Bukti Transfer">
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    <span>Perbesar</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sticky Modal Footer -->
                <div class="bg-white border-t border-gray-100 p-4 shrink-0">
                    <button type="button" wire:click="closeModal"
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs active:scale-98 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
