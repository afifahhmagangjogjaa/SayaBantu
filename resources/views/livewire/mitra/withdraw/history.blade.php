@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-gray-100 pb-24">
        <!-- Top Blue Header (Matching Dashboard Design) -->
        <div class="px-5 pt-8 pb-20 bg-[#0066FF] text-white flex items-center justify-between shadow-xs relative">
            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.dashboard') }}" wire:navigate aria-label="Kembali" class="p-2 -ml-2 hover:bg-white/20 rounded-xl transition inline-flex items-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-base font-extrabold text-white leading-tight">Penghasilan & Saldo</h1>
                    <p class="text-[11px] text-blue-100 mt-0.5">Mutasi pendapatan & penarikan dana</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @include('components.notification-icon', ['route' => route('mitra.notifications.index'), 'class' => 'p-2 rounded-xl text-white hover:bg-white/20 transition relative'])
            </div>
        </div>

        <!-- Content with Overlapping Saldo Card -->
        <div class="px-5 -mt-12 relative z-10" x-data="{ currentTab: '{{ request()->query('tab', $activeTab ?? 'income') }}' }">
            
            <!-- Balance Card (Overlapping Header) -->
            <div class="bg-gradient-to-r from-[#0052CC] to-[#0044AA] rounded-2xl p-5 text-white shadow-xl relative overflow-hidden border border-white/10 flex items-center justify-between mb-5">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 text-blue-100 text-xs font-semibold mb-1.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Saldo Tersedia</span>
                    </div>
                    <h2 class="text-2xl font-black tracking-tight text-white">
                        Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="flex-shrink-0 ml-4">
                    <a href="{{ route('mitra.withdraw.form', ['force' => 1]) }}" wire:navigate
                       class="bg-white hover:bg-slate-50 text-[#0066FF] active:scale-95 font-bold text-xs px-5 py-3 rounded-xl transition shadow-md block">
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
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-white rounded-2xl mb-5 border border-gray-200/90 shadow-2xs">
                <button type="button" 
                    @click="currentTab = 'income'"
                    :class="currentTab === 'income' ? 'bg-[#0066FF] text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 font-medium'"
                    class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Saldo Masuk</span>
                </button>
                <button type="button" 
                    @click="currentTab = 'withdraw'"
                    :class="currentTab === 'withdraw' ? 'bg-[#0066FF] text-white shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 font-medium'"
                    class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
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
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
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
                                                <a href="{{ route('mitra.helps.detail', $row->reference_id) }}" wire:navigate class="text-blue-600 font-semibold hover:underline flex-shrink-0 inline-flex items-center gap-0.5">
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
                        <x-empty-state 
                            title="Belum Ada Saldo Masuk" 
                            description="Selesaikan pekerjaan bantuan untuk mendapatkan penghasilan yang otomatis masuk ke saldo Anda." 
                            icon="wallet" 
                            actionText="Cari Bantuan Tersedia" 
                            :actionUrl="route('mitra.helps.all')" 
                        />
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
                                    <div class="flex items-start justify                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
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
                                                        ✓ Berhasil
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                                        ✕ Ditolak
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2.5 mt-2 border-t border-gray-100">
                                        <div>
                                            <p class="text-[11px] text-gray-400">{{ $row->created_at->format('d M Y • H:i') }} WIB</p>
                                        </div>                                        </div>
                                        
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
                        <x-empty-state 
                            title="Belum Ada Riwayat Tarik Saldo" 
                            description="Pengajuan penarikan dana ke rekening bank Anda akan dicatat di sini." 
                            icon="money" 
                            actionText="Ajukan Penarikan Dana" 
                            :actionUrl="route('mitra.withdraw.form', ['force' => 1])" 
                        />
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
