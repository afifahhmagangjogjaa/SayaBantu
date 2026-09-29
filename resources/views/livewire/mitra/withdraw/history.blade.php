@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="max-w-md mx-auto">
            <!-- Header - BRImo Style Standar Bawaan -->
            <div class="px-5 pt-5 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between text-white mb-3">
                        <a href="{{ route('mitra.dashboard') }}" aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="text-center flex-1 px-2">
                            <h1 class="text-lg font-bold">Riwayat Transaksi Saldo</h1>
                            <p class="text-xs text-white/90 mt-0.5">Mutasi pendapatan & penarikan dana</p>
                        </div>

                        <div class="w-9"></div>
                    </div>
                </div>

                <!-- Curved separator (Garis Lengkung Bawaan Asli) -->
                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24" x-data="{ currentTab: '{{ request()->query('tab', $activeTab ?? 'income') }}' }">
                
                <!-- Balance Card (Simpel & Bersih - Standar Gojek/Grab) -->
                <div class="mb-5 bg-gradient-to-br from-blue-50 via-white to-sky-50 border border-blue-100 rounded-2xl p-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] text-gray-500 font-medium">Saldo Tersedia</p>
                            <p class="text-xl font-black text-gray-900 mt-0.5 tracking-tight">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('mitra.withdraw.form', ['force' => 1]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tarik Saldo
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

                <!-- 2-TAB SEGMENTED CONTROLLER -->
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-gray-100/90 rounded-2xl mb-5 border border-gray-200/60">
                    <button type="button" 
                        @click="currentTab = 'income'; window.history.replaceState(null, null, '?tab=income')"
                        :class="currentTab === 'income' ? 'bg-white text-emerald-700 shadow-xs font-bold border border-emerald-100' : 'text-gray-500 hover:text-gray-800 font-medium'"
                        class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="text-sm">💰</span>
                        <span>Saldo Masuk</span>
                    </button>
                    <button type="button" 
                        @click="currentTab = 'withdraw'; window.history.replaceState(null, null, '?tab=withdraw')"
                        :class="currentTab === 'withdraw' ? 'bg-white text-blue-700 shadow-xs font-bold border border-blue-100' : 'text-gray-500 hover:text-gray-800 font-medium'"
                        class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="text-sm">🏧</span>
                        <span>Tarik Saldo</span>
                    </button>
                </div>

                <!-- ============================================== -->
                <!-- TAB 1: SALDO MASUK (PENDAPATAN BANTUAN)        -->
                <!-- ============================================== -->
                <div x-show="currentTab === 'income'" x-cloak>
                    @if(isset($incomeHistory) && $incomeHistory->count() > 0)
                        <div class="space-y-3">
                            @foreach($incomeHistory as $row)
                                <div class="bg-white rounded-2xl border border-gray-200 p-4 transition hover:border-emerald-300 shadow-2xs">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                📥
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900">
                                                    @if($row->reference_id)
                                                        Pendapatan Bantuan #{{ $row->reference_id }}
                                                    @else
                                                        {{ $row->description ?? 'Pendapatan Bantuan' }}
                                                    @endif
                                                </h3>
                                                @if(optional($row->help)->title)
                                                    <p class="text-xs text-gray-600 font-medium line-clamp-1">
                                                        {{ $row->help->title }}
                                                    </p>
                                                @endif
                                                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                                    {{ $row->created_at->format('d M Y • H:i') }} WIB
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <span class="text-sm font-extrabold text-emerald-600 block">
                                                + Rp {{ number_format($row->amount, 0, ',', '.') }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 mt-1">
                                                ✓ Masuk ke Saldo
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Keterangan Tambahan / Potongan Platform -->
                                    @if($row->description)
                                        <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                            <div class="truncate max-w-[240px]">
                                                {{ $row->description }}
                                            </div>
                                            @if($row->reference_id)
                                                <a href="{{ route('mitra.helps.detail', $row->reference_id) }}" class="text-blue-600 font-semibold hover:underline flex-shrink-0 inline-flex items-center gap-0.5">
                                                    Detail &rarr;
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($incomeHistory->hasPages())
                            <div class="mt-6">
                                {{ $incomeHistory->appends(['tab' => 'income'])->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl">
                                💰
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Belum Ada Saldo Masuk</h3>
                            <p class="text-xs text-gray-500 max-w-xs mx-auto mb-5 leading-relaxed">
                                Selesaikan pekerjaan bantuan untuk mendapatkan penghasilan yang otomatis masuk ke saldo Anda.
                            </p>
                            <a href="{{ route('mitra.helps.all') }}" class="inline-block px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Cari Bantuan Tersedia
                            </a>
                        </div>
                    @endif
                </div>

                <!-- ============================================== -->
                <!-- TAB 2: TARIK SALDO (WITHDRAW)                  -->
                <!-- ============================================== -->
                <div x-show="currentTab === 'withdraw'" x-cloak>
                    @if(isset($withdrawHistory) && $withdrawHistory->count() > 0)
                        <div class="space-y-3">
                            @foreach($withdrawHistory as $row)
                                <div class="bg-white rounded-2xl border border-gray-200 p-4 transition hover:border-blue-300 shadow-2xs">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                🏧
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900">{{ strtoupper($row->bank_code) }}</h3>
                                                <p class="text-xs text-gray-400 font-mono">{{ $row->account_number }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">a.n. {{ $row->account_name }}</p>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <span class="text-sm font-extrabold text-gray-900 block">
                                                Rp {{ number_format($row->amount, 0, ',', '.') }}
                                            </span>
                                            <div class="mt-1">
                                                @if($row->status === 'pending')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                                        ⏳ Menunggu
                                                    </span>
                                                @elseif($row->status === 'processing')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                        🔄 Diproses
                                                    </span>
                                                @elseif($row->status === 'success' || $row->status === 'approved')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                                                        ✅ Berhasil
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                                        ❌ Ditolak
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2.5 mt-2 border-t border-gray-100">
                                        <div>
                                            <p class="text-[11px] text-gray-400">{{ $row->created_at->format('d M Y • H:i') }} WIB</p>
                                        </div>
                                        
                                        @if($row->status === 'success' || $row->status === 'approved')
                                            <div>
                                                <button type="button" onclick="document.getElementById('modal-success-{{ $row->id }}').classList.remove('hidden')" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                    Lihat Bukti Transfer &rarr;
                                                </button>
                                            </div>
                                        @elseif($row->status === 'failed' || $row->status === 'rejected')
                                            <div>
                                                <button type="button" onclick="document.getElementById('modal-rejected-{{ $row->id }}').classList.remove('hidden')" class="text-xs font-semibold text-red-500 hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                    Lihat Detail Penolakan &rarr;
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Modal Success (Client Side) -->
                                    @if($row->status === 'success' || $row->status === 'approved')
                                    <div id="modal-success-{{ $row->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                                        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl animate-fade-in-up">
                                            <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white text-center rounded-t-3xl" style="background: linear-gradient(to bottom right, #10b981, #16a34a);">
                                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                                                
                                                <div class="relative z-10">
                                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <h2 class="text-xl font-bold">Penarikan Berhasil!</h2>
                                                    <p class="text-xs text-emerald-100 mt-1">Permintaan penarikan telah diproses</p>
                                                </div>
                                            </div>

                                            <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-6 space-y-4">
                                                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-3">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Jumlah Penarikan:</span>
                                                        <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($row->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Bank / E-Wallet Tujuan:</span>
                                                        <span class="font-bold text-gray-900">{{ strtoupper($row->bank_code) }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Nomor Rekening:</span>
                                                        <span class="font-mono text-gray-900 font-bold">{{ $row->account_number }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs border-t border-gray-200 pt-2.5">
                                                        <span class="text-gray-500">Tanggal Diproses:</span>
                                                        <span class="text-gray-900">{{ optional($row->processed_at)->format('d M Y • H:i') }} WIB</span>
                                                    </div>
                                                </div>

                                                <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-xs text-blue-800 flex items-start gap-2.5">
                                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                                    </svg>
                                                    <span>Dana telah berhasil dikirimkan ke rekening tujuan Anda.</span>
                                                </div>

                                                <div class="pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-success-{{ $row->id }}').classList.add('hidden')" class="block w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-center text-xs font-bold transition shadow-sm cursor-pointer">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Modal Rejected (Client Side) -->
                                    @if($row->status === 'failed' || $row->status === 'rejected')
                                    <div id="modal-rejected-{{ $row->id }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                                        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl animate-fade-in-up">
                                            <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white text-center rounded-t-3xl" style="background: linear-gradient(to bottom right, #ef4444, #e11d48);">
                                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                                                
                                                <div class="relative z-10">
                                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                    <h2 class="text-xl font-bold">Penarikan Ditolak</h2>
                                                    <p class="text-xs text-red-100 mt-1">Permintaan penarikan tidak dapat diproses</p>
                                                </div>
                                            </div>

                                            <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-6 space-y-4">
                                                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-3">
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Jumlah Penarikan:</span>
                                                        <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($row->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Bank / E-Wallet:</span>
                                                        <span class="font-bold text-gray-900">{{ strtoupper($row->bank_code) }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-gray-500">Nomor Rekening:</span>
                                                        <span class="font-mono text-gray-900 font-bold">{{ $row->account_number }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-xs border-t border-gray-200 pt-2.5">
                                                        <span class="text-gray-500">Tanggal Diproses:</span>
                                                        <span class="text-gray-900">{{ optional($row->processed_at)->format('d M Y • H:i') }} WIB</span>
                                                    </div>
                                                </div>

                                                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl">
                                                    <h3 class="text-xs font-bold text-red-900 mb-1">Alasan Penolakan:</h3>
                                                    <p class="text-xs text-red-700 leading-relaxed">{{ $row->description ?? 'Nomor rekening tidak valid atau data bank tidak sesuai.' }}</p>
                                                </div>

                                                <div class="pt-2">
                                                    <button type="button" onclick="document.getElementById('modal-rejected-{{ $row->id }}').classList.add('hidden')" class="block w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold transition shadow-sm cursor-pointer">
                                                        Tutup
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($withdrawHistory->hasPages())
                            <div class="mt-6">
                                {{ $withdrawHistory->appends(['tab' => 'withdraw'])->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-800 mb-1">Belum Ada Riwayat Tarik Saldo</h3>
                            <p class="text-xs text-gray-500 mb-5">Pengajuan penarikan dana Anda akan dicatat di sini</p>
                            <a href="{{ route('mitra.withdraw.form', ['force' => 1]) }}" class="inline-block px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Ajukan Penarikan Dana
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection