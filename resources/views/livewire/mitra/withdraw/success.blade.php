@php
    $isCustomer = ($user && $user->role === 'customer');
    $prefix = $prefix ?? ($isCustomer ? 'customer' : 'mitra');
@endphp
@extends($isCustomer ? 'layouts.app' : 'layouts.mitra')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="px-5 pt-6 pb-8 relative bg-gradient-to-br from-emerald-500 to-green-600 text-white text-center">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h1 class="text-xl font-bold">Penarikan Berhasil!</h1>
                    <p class="text-xs text-emerald-100 mt-1">Permintaan penarikan telah diproses</p>
                </div>

                <svg class="absolute -bottom-1 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 28px; bottom: -2px;">
                    <path d="M0,28 C360,76 1080,-4 1440,36 L1440,84 L0,84 Z" fill="#ffffff"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="bg-white px-5 pt-4 pb-6 space-y-4">
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Jumlah Penarikan:</span>
                        <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($withdraw->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Bank / E-Wallet Tujuan:</span>
                        <span class="font-bold text-gray-900">{{ strtoupper($withdraw->bank_code) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Nomor Rekening:</span>
                        <span class="font-mono text-gray-900 font-bold">{{ $withdraw->account_number }}</span>
                    </div>
                    @if($withdraw->account_name)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Nama Pemilik:</span>
                        <span class="font-bold text-gray-900">{{ $withdraw->account_name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-xs border-t border-gray-200 pt-2.5">
                        <span class="text-gray-500">Tanggal Diproses:</span>
                        <span class="text-gray-900">{{ optional($withdraw->processed_at)->format('d M Y • H:i') }} WIB</span>
                    </div>
                    @if($withdraw->transfer_proof)
                    <div class="pt-2 border-t border-gray-200">
                        <span class="text-gray-500 text-xs block mb-1.5">Bukti Transfer dari Admin:</span>
                        <a href="{{ asset('storage/' . $withdraw->transfer_proof) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Bukti Transfer
                        </a>
                    </div>
                    @endif
                </div>

                <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-xs text-blue-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span>Dana telah berhasil dikirimkan ke rekening tujuan Anda.</span>
                </div>

                <div class="flex gap-2.5 pt-2">
                    <a href="{{ route($prefix . '.withdraw.history') }}" wire:navigate class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-center text-xs font-bold transition shadow-sm">
                        Lihat Riwayat
                    </a>
                    <a href="{{ route($prefix . '.dashboard') }}" wire:navigate class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold transition">
                        Ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
