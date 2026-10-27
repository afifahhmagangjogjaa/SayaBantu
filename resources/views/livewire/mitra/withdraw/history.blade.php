@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-24">
        <!-- Header - SayaBantu Brand Blue matching Beranda with Wave Separator -->
        <div class="px-5 pt-6 pb-20 relative overflow-hidden text-white bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Top Row: Title & Notification -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-black text-white tracking-tight leading-tight">Penghasilan</h1>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola saldo &amp; riwayat transaksi Anda</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('mitra.notifications.index'), 'class' => 'p-2 rounded-xl text-white hover:bg-white/20 transition relative'])
                    </div>
                </div>
            </div>

            <!-- Wave Separator (SVG) bergelombang di bawah nominal saldo -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Saldo Card matching Beranda (bg-gradient-to-r from-[#0052CC] to-[#0044AA]) -->
        <div class="px-5 -mt-14 relative z-10 mb-5">
            <div class="bg-gradient-to-r from-[#0052CC] to-[#0044AA] rounded-2xl p-5 text-white shadow-xl relative overflow-hidden border border-white/20">
                <!-- Decorative circle overlay matching Beranda -->
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-100 mb-1">Saldo Tersedia</p>
                        <h2 class="text-2xl font-black text-white tracking-tight">
                            Rp {{ number_format($user->balance ?? 250000, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="text-white/95 mt-1">
                        <!-- Classical Greek Bank Temple Icon -->
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v2h20V7L12 2zm-8 8v8h3v-8H4zm6 0v8h4v-8h-4zm7 0v8h3v-8h-3zM2 20h20v2H2v-2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Centered Tarik Saldo Pill Button (White with Blue Text) -->
                <div class="relative z-10 mt-5 flex justify-center">
                    <a href="{{ route('mitra.withdraw.form', ['force' => 1]) }}" wire:navigate
                       class="bg-white hover:bg-slate-50 text-[#0066FF] active:scale-95 font-bold text-xs px-8 py-2.5 rounded-full shadow-md hover:shadow-lg transition-all text-center inline-block">
                        Penarikan Saldo
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area: Riwayat Transaksi -->
        <div class="bg-white rounded-t-3xl pt-2 pb-6 px-5 border-t border-slate-100 shadow-xs">
            <!-- Status Flash Message (If any) -->
            @if(session('status'))
                <div class="mb-4 mt-2 p-3.5 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs flex items-start gap-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Section: Riwayat Transaksi -->
            <div class="flex items-center justify-between mb-2 pt-2">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Riwayat Transaksi</h2>
                <a href="{{ route('mitra.withdraw.history') }}" wire:navigate
                   class="text-xs font-semibold text-[#0066FF] hover:underline flex items-center gap-0.5">
                    Lihat Semua <span class="text-[11px]">&gt;</span>
                </a>
            </div>

            <!-- Transaction Feed List matching Mockup -->
            <div class="divide-y divide-gray-100">
                @forelse($combinedTransactions as $tx)
                    @php
                        $months = [
                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                        ];
                        $dt = $tx->created_at instanceof \Carbon\Carbon ? $tx->created_at : \Carbon\Carbon::parse($tx->created_at);
                        $d = $dt->format('j');
                        $m = $months[(int)$dt->format('n')];
                        $y = $dt->format('Y');
                        $time = $dt->format('H:i');
                        $dateFormatted = "{$d} {$m} {$y}, {$time}";
                    @endphp
                    <div class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition px-1 rounded-xl">
                        <!-- Left: Icon + Info -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#0066FF] border border-blue-100/70 flex items-center justify-center flex-shrink-0">
                                @if($tx->type === 'income')
                                    <!-- House / Order building icon matching Mockup -->
                                    <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                @else
                                    <!-- Bank / Building / Card icon matching Mockup -->
                                    <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold text-gray-900 truncate">{{ $tx->title }}</h3>
                                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $tx->subtitle }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $dateFormatted }}</p>
                            </div>
                        </div>

                        <!-- Right: Amount -->
                        <div class="text-right flex-shrink-0">
                            @if($tx->type === 'income')
                                <span class="text-xs font-bold text-emerald-500">
                                    + Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-red-500">
                                    - Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="w-14 h-14 bg-blue-50 text-[#0066FF] rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Belum Ada Transaksi</p>
                        <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">Riwayat pendapatan dari order dan penarikan saldo Anda akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
