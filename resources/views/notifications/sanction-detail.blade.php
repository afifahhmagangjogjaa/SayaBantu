@php
    $layout = ($user->role === 'mitra') ? 'layouts.mitra' : 'layouts.app';
    $backUrl = ($user->role === 'mitra') ? route('mitra.notifications.index') : route('customer.notifications.index');
    $profileUrl = ($user->role === 'mitra') ? route('mitra.profile') : route('profile');
@endphp

@extends($layout)

@section('content')
<div class="min-h-screen bg-gray-50 pb-28 pt-4">
    <div class="max-w-md mx-auto px-4">
        <!-- Top Back Bar -->
        <div class="flex items-center justify-between mb-4">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-700 hover:text-gray-900 bg-white px-3 py-2 rounded-xl shadow-xs border border-gray-200 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Notifikasi
            </a>

            <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ $warningLevel === 0 ? 'text-emerald-700 bg-emerald-100 border-emerald-200' : 'text-red-700 bg-red-100 border-red-200' }} px-2.5 py-1 rounded-lg border">
                @if($warningLevel === 0)
                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Pencabutan Sanksi
                @else
                    <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Sanksi Resmi
                @endif
            </span>
        </div>

        <!-- Official Letter Document Card -->
        <div class="bg-white rounded-2xl shadow-sm border {{ $warningLevel === 0 ? 'border-emerald-200' : 'border-red-200' }} overflow-hidden relative">
            <!-- Header Stripe -->
            <div class="h-2 bg-gradient-to-r {{ $warningLevel === 0 ? 'from-emerald-600 via-teal-600 to-emerald-700' : 'from-red-600 via-rose-600 to-red-700' }}"></div>

            <div class="p-5">
                <!-- Letterhead -->
                <div class="text-center pb-4 border-b border-gray-100">
                    <div class="w-12 h-12 mx-auto rounded-full {{ $warningLevel === 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center mb-2">
                        @if($warningLevel === 0)
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @endif
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $warningLevel === 0 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-red-600 bg-red-50 border-red-200' }} px-2.5 py-0.5 rounded-full border">
                        {{ $warningLevel === 0 ? 'Pemberitahuan Pemulihan Akun' : 'Pemberitahuan Resmi Platform' }}
                    </span>
                    <h1 class="text-lg font-bold text-gray-900 mt-2">
                        {{ $warningLevel === 0 ? 'Surat Pencabutan Peringatan' : 'Surat Peringatan ' . $warningLevel }}
                    </h1>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">
                        No: {{ $warningLevel === 0 ? 'SP-CABUT' : 'SP' }}/SB/{{ $notification->created_at->format('Y') }}/{{ strtoupper(substr($notification->id, 0, 8)) }}
                    </p>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        Diterbitkan: {{ $notification->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <!-- Recipient Info -->
                <div class="py-3.5 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Diberikan Kepada:</h2>
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nama Akun:</span>
                            <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Peran Akun:</span>
                            <span class="font-semibold text-gray-800 capitalize">{{ $user->role }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Email:</span>
                            <span class="font-mono text-gray-700">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tingkat Peringatan:</span>
                            @if($warningLevel === 0)
                                <span class="font-bold text-emerald-600">Sanksi Dicabut (Akun Normal)</span>
                            @else
                                <span class="font-bold text-red-600">Tingkat {{ $warningLevel }} dari 3</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Violation Reason / Revocation Note -->
                <div class="py-3.5 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $warningLevel === 0 ? 'Keterangan Pemulihan:' : 'Alasan & Uraian Sanksi:' }}</h2>
                    <div class="p-3.5 rounded-xl {{ $warningLevel === 0 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-red-50/70 border-red-200' }} border">
                        <p class="text-xs font-medium {{ $warningLevel === 0 ? 'text-emerald-950' : 'text-red-950' }} leading-relaxed">
                            @if($warningLevel === 0)
                                {{ $reason ?? 'Surat Peringatan pada akun Anda telah resmi dicabut oleh Admin. Status akun Anda kini telah dipulihkan dan dapat beroperasi secara normal kembali.' }}
                            @else
                                {{ $reason ?? 'Pelanggaran terhadap syarat dan ketentuan operasional platform SayaBantu.' }}
                            @endif
                        </p>
                        @if($reportId)
                            <div class="mt-2 pt-2 border-t {{ $warningLevel === 0 ? 'border-emerald-200 text-emerald-800' : 'border-red-200 text-red-800' }} text-[11px] flex items-center justify-between">
                                <span>Berdasarkan Laporan Aduan:</span>
                                <span class="font-mono font-bold">#{{ $reportId }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($warningLevel === 0)
                    <!-- Pemulihan Akun Info -->
                    <div class="py-3.5 border-b border-gray-100">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status Akun Terkini:</h2>
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                            <div class="flex items-start gap-2.5">
                                <span class="p-1 bg-emerald-100 text-emerald-700 rounded-lg shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div>
                                    <p class="font-bold text-emerald-900">Akun Aktif & Bebas Peringatan</p>
                                    <p class="text-[11px] text-emerald-800 mt-0.5 leading-normal">
                                        Seluruh sanksi Surat Peringatan pada akun Anda telah dicabut. Anda dapat kembali menggunakan layanan dan menjalankan order bantuan seperti biasa dengan tetap mematuhi pedoman komunitas SayaBantu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Consequence Summary -->
                    <div class="py-3.5 border-b border-gray-100">
                        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konsekuensi & Aturan:</h2>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-start gap-2 p-2.5 rounded-xl {{ $warningLevel === 1 ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50' }}">
                                <div class="w-5 h-5 rounded-full {{ $warningLevel === 1 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">1</div>
                                <div>
                                    <span class="font-bold {{ $warningLevel === 1 ? 'text-amber-900' : 'text-gray-700' }}">Surat Peringatan 1:</span>
                                    <p class="text-[11px] text-gray-600 mt-0.5 leading-normal">Teguran resmi pertama. Akun tetap beroperasi normal dan peringatan ini tidak ditampilkan pada profil publik.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 p-2.5 rounded-xl {{ $warningLevel === 2 ? 'bg-orange-50 border border-orange-200' : 'bg-gray-50' }}">
                                <div class="w-5 h-5 rounded-full {{ $warningLevel === 2 ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">2</div>
                                <div>
                                    <span class="font-bold {{ $warningLevel === 2 ? 'text-orange-900' : 'text-gray-700' }}">Surat Peringatan 2:</span>
                                    <p class="text-[11px] text-gray-600 mt-0.5 leading-normal">Peringatan keras kedua. Tanda peringatan ditampilkan di halaman profil akun Anda.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 p-2.5 rounded-xl {{ $warningLevel === 3 ? 'bg-red-50 border border-red-200' : 'bg-gray-50' }}">
                                <div class="w-5 h-5 rounded-full {{ $warningLevel === 3 ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">3</div>
                                <div>
                                    <span class="font-bold {{ $warningLevel === 3 ? 'text-red-900' : 'text-gray-700' }}">Surat Peringatan 3:</span>
                                    <p class="text-[11px] text-gray-600 mt-0.5 leading-normal">Akun dinonaktifkan / diblokir permanen dari seluruh ekosistem layanan SayaBantu.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Footer Notice -->
                <div class="pt-4 text-center">
                    <p class="text-[11px] text-gray-400 leading-relaxed">
                        {{ $warningLevel === 0 ? 'Surat Pencabutan ini adalah dokumen resmi yang diterbitkan oleh Tim Pengawas SayaBantu.' : 'Surat Peringatan ini adalah catatan sanksi resmi dan permanen yang diterbitkan oleh Tim Pengawas SayaBantu.' }}
                    </p>
                    <div class="mt-4 flex flex-col gap-2">
                        @if($warningLevel >= 3)
                            {{-- SP 3: Tombol Saya Mengerti → ban + logout --}}
                            <form method="POST" action="{{ route('notifications.sanction.acknowledge', $notification->id) }}">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Dengan mengklik ini, akun Anda akan segera dinonaktifkan dan Anda akan keluar dari sistem. Lanjutkan?')"
                                    class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl text-center transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Saya Mengerti — Keluar dari Akun
                                </button>
                            </form>
                        @else
                            <a href="{{ $profileUrl }}" class="w-full py-2.5 px-4 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl text-center transition">
                                Periksa Halaman Profil
                            </a>
                            <a href="{{ $backUrl }}" class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl text-center transition">
                                Kembali ke Notifikasi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
