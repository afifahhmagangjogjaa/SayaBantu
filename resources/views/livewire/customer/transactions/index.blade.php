<div class="min-h-screen bg-white">
    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style Standar Bawaan -->
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

                    <div class="text-center flex-1 px-2">
                        <h1 class="text-lg font-bold">Riwayat Transaksi</h1>
                        <p class="text-xs text-white/90 mt-0.5">Mutasi saldo & pembayaran Anda</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('customer.notifications.index')])
                    </div>
                </div>
            </div>

            <!-- Curved separator (Garis Lengkung Bawaan Asli) -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24">
            
            <!-- Balance Card (Simpel & Elegan - Tanpa Total Akumulasi Uang) -->
            <div class="mb-5 bg-gradient-to-br from-blue-50 via-white to-sky-50 border border-blue-100 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-gray-500 font-medium">Saldo Tersedia</p>
                        <p class="text-xl font-black text-gray-900 mt-0.5 tracking-tight">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('customer.topup') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Top Up Saldo
                    </a>
                </div>
            </div>

            <!-- Status Flash Message -->
            @if(session('status'))
                <div class="mb-4 p-3.5 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs flex items-start gap-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-start gap-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- 2-TAB SEGMENTED CONTROLLER (Tanpa Badge Angka Sesuai Request) -->
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-gray-100/90 rounded-2xl mb-5 border border-gray-200/60">
                <button type="button" 
                    wire:click="switchTab('masuk')"
                    class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'masuk' ? 'bg-white text-emerald-700 shadow-xs font-bold border border-emerald-100' : 'text-gray-500 hover:text-gray-800 font-medium' }}">
                    <span class="text-sm">💰</span>
                    <span>Saldo Masuk</span>
                </button>
                <button type="button" 
                    wire:click="switchTab('keluar')"
                    class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer {{ $activeTab === 'keluar' ? 'bg-white text-blue-700 shadow-xs font-bold border border-blue-100' : 'text-gray-500 hover:text-gray-800 font-medium' }}">
                    <span class="text-sm">📤</span>
                    <span>Saldo Keluar</span>
                </button>
            </div>

            <!-- Loading Indicator -->
            <div wire:loading.flex class="items-center justify-center py-8 text-gray-500 gap-2 text-xs">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data mutasi...</span>
            </div>

            <!-- ============================================== -->
            <!-- TAB 1: SALDO MASUK (TOP UP & REFUND)           -->
            <!-- ============================================== -->
            @if($activeTab === 'masuk')
                <div wire:loading.remove>
                    @if($transactions->count() > 0)
                        <div class="space-y-3">
                            @foreach($transactions as $t)
                                <div wire:click="showTransaction({{ $t->id }})" class="bg-white rounded-2xl border border-gray-200 p-4 transition hover:border-emerald-300 shadow-2xs cursor-pointer">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                📥
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900">
                                                    @if(str_contains(strtolower($t->description ?? ''), 'refund') || str_contains(strtolower($t->description ?? ''), 'pengembalian'))
                                                        Pengembalian Dana (Refund)
                                                    @elseif(!empty($t->request_code))
                                                        Top Up Saldo ({{ $t->request_code }})
                                                    @else
                                                        Top Up Saldo
                                                    @endif
                                                </h3>
                                                <p class="text-xs text-gray-500 font-medium">
                                                    @if(!empty($t->payment_method))
                                                        {{ strtoupper(str_replace('_', ' ', $t->payment_method)) }}
                                                    @elseif(!empty($t->payment_type))
                                                        {{ ucfirst($t->payment_type) }}
                                                    @elseif(str_contains(strtolower($t->description ?? ''), 'refund'))
                                                        Bantuan #{{ $t->reference_id }}
                                                    @else
                                                        Top-up Saldo
                                                    @endif
                                                </p>
                                                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                                    {{ $t->created_at->format('d M Y • H:i') }} WIB
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <span class="text-sm font-extrabold text-emerald-600 block">
                                                + Rp {{ number_format($t->amount, 0, ',', '.') }}
                                            </span>
                                            @if($t->status === 'waiting_approval')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 mt-1">
                                                    ⏳ Menunggu
                                                </span>
                                            @elseif($t->status === 'approved' || $t->status === 'completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1">
                                                    ✓ Masuk ke Saldo
                                                </span>
                                            @elseif($t->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 mt-1">
                                                    ✕ Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 mt-1">
                                                    {{ ucfirst($t->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Deskripsi / Keterangan Ringkas -->
                                    @if($t->description)
                                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                            <div class="truncate max-w-[240px]">
                                                {{ $t->description }}
                                            </div>
                                            <span class="text-emerald-600 font-semibold inline-flex items-center gap-0.5">
                                                Detail &rarr;
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="mt-6">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State Tab Masuk -->
                        <div class="text-center py-16">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl">
                                💰
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Belum Ada Saldo Masuk</h3>
                            <p class="text-xs text-gray-500 max-w-xs mx-auto mb-5 leading-relaxed">
                                Riwayat top-up dan pengembalian saldo Anda akan muncul di sini.
                            </p>
                            <a href="{{ route('customer.topup') }}" class="inline-block px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Top Up Saldo Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- ============================================== -->
            <!-- TAB 2: SALDO KELUAR (BUAT MITRA / BANTUAN)     -->
            <!-- ============================================== -->
            @if($activeTab === 'keluar')
                <div wire:loading.remove>
                    @if($transactions->count() > 0)
                        <div class="space-y-3">
                            @foreach($transactions as $t)
                                <div wire:click="showTransaction({{ $t->id }})" class="bg-white rounded-2xl border border-gray-200 p-4 transition hover:border-blue-300 shadow-2xs cursor-pointer">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                🤝
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900">
                                                    @if(!empty($t->reference_id))
                                                        Pembayaran Bantuan #{{ $t->reference_id }}
                                                    @else
                                                        {{ $t->description ?? 'Pembayaran Bantuan' }}
                                                    @endif
                                                </h3>
                                                @if(optional($t->help)->title)
                                                    <p class="text-xs text-gray-600 font-medium line-clamp-1">
                                                        {{ $t->help->title }}
                                                    </p>
                                                @endif
                                                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                                    {{ $t->created_at->format('d M Y • H:i') }} WIB
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <span class="text-sm font-extrabold text-red-600 block">
                                                - Rp {{ number_format(abs($t->amount), 0, ',', '.') }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 mt-1">
                                                ✓ Terbayar ke Mitra
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Keterangan Tambahan / Link Bantuan -->
                                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                        <div class="truncate max-w-[240px]">
                                            {{ $t->description ?? 'Pembayaran jasa ke mitra' }}
                                        </div>
                                        @if($t->reference_id)
                                            <a href="{{ route('customer.helps.detail', $t->reference_id) }}" wire:click.stop class="text-blue-600 font-semibold hover:underline flex-shrink-0 inline-flex items-center gap-0.5">
                                                Lihat Bantuan &rarr;
                                            </a>
                                        @else
                                            <span class="text-blue-600 font-semibold inline-flex items-center gap-0.5">
                                                Detail &rarr;
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="mt-6">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State Tab Keluar -->
                        <div class="text-center py-16">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-3xl">
                                📤
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Belum Ada Saldo Keluar</h3>
                            <p class="text-xs text-gray-500 max-w-xs mx-auto mb-5 leading-relaxed">
                                Riwayat pembayaran bantuan untuk mitra akan tercatat secara rapi di sini.
                            </p>
                            <a href="{{ route('customer.helps.create') }}" class="inline-block px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Buat Permintaan Bantuan
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- ============================================== -->
    <!-- DETAIL MODAL TRANSAKSI (POP-UP LENGKAP)        -->
    <!-- ============================================== -->
    @if($selectedTransaction)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" wire:click="closeTransaction">
            <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all" wire:click.stop>
                
                <!-- Dynamic Header Modal -->
                @if($selectedTransaction['type'] === 'topup' || str_contains(strtolower($selectedTransaction['description'] ?? ''), 'refund'))
                    <div class="px-5 pt-6 pb-10 relative overflow-hidden text-white text-center rounded-t-3xl" style="background: linear-gradient(to bottom right, #10b981, #059669);">
                @else
                    <div class="px-5 pt-6 pb-10 relative overflow-hidden text-white text-center rounded-t-3xl" style="background: linear-gradient(to bottom right, #0098e7, #0077cc);">
                @endif
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                    
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mx-auto mb-2.5 shadow-md">
                            @if($selectedTransaction['type'] === 'topup' || str_contains(strtolower($selectedTransaction['description'] ?? ''), 'refund'))
                                <span class="text-2xl">💰</span>
                            @else
                                <span class="text-2xl">🤝</span>
                            @endif
                        </div>
                        <h2 class="text-lg font-bold">
                            @if($selectedTransaction['type'] === 'topup' || str_contains(strtolower($selectedTransaction['description'] ?? ''), 'refund'))
                                Detail Saldo Masuk
                            @else
                                Detail Saldo Keluar
                            @endif
                        </h2>
                        <p class="text-xs text-white/90 mt-0.5">{{ $selectedTransaction['created_at'] }}</p>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="bg-white rounded-t-3xl -mt-5 px-5 pt-5 pb-6 space-y-4">
                    <!-- Nominal Besar -->
                    <div class="text-center pb-2 border-b border-gray-100">
                        <p class="text-xs text-gray-500 font-medium">Nominal Mutasi</p>
                        <p class="text-2xl font-black mt-0.5 {{ ($selectedTransaction['type'] === 'topup' || str_contains(strtolower($selectedTransaction['description'] ?? ''), 'refund')) ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ ($selectedTransaction['type'] === 'topup' || str_contains(strtolower($selectedTransaction['description'] ?? ''), 'refund')) ? '+' : '-' }} Rp {{ number_format(abs($selectedTransaction['amount']), 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Detail Rincian List -->
                    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-gray-500">Status</span>
                            @if($selectedTransaction['status'] === 'waiting_approval')
                                <span class="px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800">Menunggu Approval</span>
                            @elseif($selectedTransaction['status'] === 'approved' || $selectedTransaction['status'] === 'completed')
                                <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800">Berhasil / Selesai</span>
                            @elseif($selectedTransaction['status'] === 'rejected')
                                <span class="px-2 py-0.5 rounded-full font-bold bg-rose-100 text-rose-800">Ditolak</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full font-bold bg-gray-100 text-gray-700">{{ ucfirst($selectedTransaction['status'] ?? 'Selesai') }}</span>
                            @endif
                        </div>

                        @if(!empty($selectedTransaction['request_code']))
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500">Kode Request</span>
                                <span class="font-mono font-bold text-gray-900">{{ $selectedTransaction['request_code'] }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['order_id']))
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500">Order ID</span>
                                <span class="font-mono font-bold text-gray-900">{{ $selectedTransaction['order_id'] }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['reference_id']))
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500">Nomor Bantuan</span>
                                <span class="font-bold text-blue-600">#{{ $selectedTransaction['reference_id'] }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['help_title']))
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-gray-500 whitespace-nowrap">Judul Bantuan</span>
                                <span class="font-bold text-gray-900 text-right">{{ $selectedTransaction['help_title'] }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['payment_method']))
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500">Metode</span>
                                <span class="font-bold text-gray-900">{{ $selectedTransaction['payment_method'] }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['admin_fee']) && $selectedTransaction['admin_fee'] > 0)
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500">Biaya Admin</span>
                                <span class="font-bold text-gray-900">Rp {{ number_format($selectedTransaction['admin_fee'], 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['total_payment']) && $selectedTransaction['total_payment'] > 0)
                            <div class="flex justify-between items-center gap-2 border-t border-gray-200/70 pt-2 font-bold">
                                <span class="text-gray-700">Total Ditransfer</span>
                                <span class="text-blue-700">Rp {{ number_format($selectedTransaction['total_payment'], 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['description']))
                            <div class="pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500 block mb-1">Keterangan:</span>
                                <p class="text-gray-800 leading-relaxed">{{ $selectedTransaction['description'] }}</p>
                            </div>
                        @endif

                        @if(!empty($selectedTransaction['rejection_reason']))
                            <div class="pt-2 border-t border-rose-200 text-rose-700 bg-rose-50 -mx-4 -mb-4 p-3 rounded-b-2xl">
                                <span class="font-bold block mb-0.5">Alasan Penolakan:</span>
                                <p class="leading-relaxed">{{ $selectedTransaction['rejection_reason'] }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Bukti Transfer (Jika Ada) -->
                    @if(!empty($selectedTransaction['proof_of_payment']))
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-between text-xs">
                            <span class="text-gray-600 font-medium">Bukti Transfer</span>
                            <a href="{{ asset('storage/' . $selectedTransaction['proof_of_payment']) }}" target="_blank" class="px-3 py-1 bg-blue-50 text-blue-600 font-bold rounded-lg hover:bg-blue-100 transition">
                                Lihat Bukti &rarr;
                            </a>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-2">
                        @if(!empty($selectedTransaction['reference_id']))
                            <a href="{{ route('customer.helps.detail', $selectedTransaction['reference_id']) }}" class="block w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-center text-xs font-bold transition shadow-xs">
                                Buka Halaman Bantuan #{{ $selectedTransaction['reference_id'] }}
                            </a>
                        @endif

                        <button type="button" wire:click="closeTransaction" class="block w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>