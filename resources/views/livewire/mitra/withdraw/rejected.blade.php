@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="px-5 pt-6 pb-12 relative overflow-hidden bg-gradient-to-br from-red-500 to-rose-600 text-white text-center">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <h1 class="text-xl font-bold">Penarikan Ditolak</h1>
                    <p class="text-xs text-red-100 mt-1">Permintaan penarikan tidak dapat diproses</p>
                </div>

                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24 space-y-4">
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Jumlah Penarikan:</span>
                        <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($withdraw->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Bank / E-Wallet:</span>
                        <span class="font-bold text-gray-900">{{ strtoupper($withdraw->bank_code) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Nomor Rekening:</span>
                        <span class="font-mono text-gray-900 font-bold">{{ $withdraw->account_number }}</span>
                    </div>
                    <div class="flex justify-between text-xs border-t border-gray-200 pt-2.5">
                        <span class="text-gray-500">Tanggal Diproses:</span>
                        <span class="text-gray-900">{{ optional($withdraw->processed_at)->format('d M Y • H:i') }} WIB</span>
                    </div>
                </div>

                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl">
                    <h3 class="text-xs font-bold text-red-900 mb-1">Alasan Penolakan:</h3>
                    <p class="text-xs text-red-700 leading-relaxed">{{ $withdraw->description ?? 'Nomor rekening tidak valid atau data bank tidak sesuai.' }}</p>
                </div>

                <div class="flex gap-2.5 pt-2">
                    <a href="{{ route('mitra.withdraw.form', ['force' => 1]) }}" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-center text-xs font-bold transition shadow-sm">
                        Ajukan Ulang
                    </a>
                    <a href="{{ route('mitra.withdraw.history') }}" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold transition">
                        Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection