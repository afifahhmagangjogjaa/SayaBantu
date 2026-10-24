<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header Step 4 -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Review & Konfirmasi</h2>

                <!-- Step Progress Bar -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                </div>

                <!-- Sub Row: Left hint & Right Step indicator -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Periksa kembali kelengkapan data Anda</span>
                    <span class="font-bold text-blue-600">Langkah 4 dari 4</span>
                </div>
            </div>

            <form action="{{ route('onboarding.step4.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Role Info Banner -->
                <div class="p-3.5 rounded-2xl flex items-center justify-between {{ $user->role === 'mitra' ? 'bg-green-50 border border-green-200 text-green-900' : 'bg-blue-50 border border-blue-200 text-blue-900' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $user->role === 'mitra' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                            @if($user->role === 'mitra')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-[11px] font-medium text-gray-500">Mendaftar Sebagai:</p>
                            <p class="text-sm font-bold uppercase">{{ $user->role === 'mitra' ? 'Mitra' : 'Customer' }}</p>
                        </div>
                    </div>
                    @if($user->hasVerifiedEmail())
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold inline-flex items-center gap-1 {{ $user->role === 'mitra' ? 'bg-green-200/80 text-green-800' : 'bg-blue-200/80 text-blue-800' }}">
                        <span>Email Terverifikasi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    @else
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        Email Belum Diverifikasi
                    </span>
                    @endif
                </div>

                <!-- 1. Ringkasan Data Diri -->
                <div class="bg-gray-50/80 border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200/60">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Data Pribadi
                        </h3>
                        <a href="{{ route('onboarding.step1') }}" class="text-xs font-bold text-blue-600 hover:underline">Ubah</a>
                    </div>

                    <div class="grid grid-cols-2 gap-y-2.5 gap-x-2 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">NIK</span>
                            <span class="font-bold text-gray-900">{{ $user->nik }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Nama Lengkap</span>
                            <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Jenis Kelamin</span>
                            <span class="font-medium text-gray-900">{{ $user->gender }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">No. WhatsApp</span>
                            <span class="font-medium text-gray-900">{{ $user->phone }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Alamat Domisili</span>
                            <span class="font-medium text-gray-900">{{ $user->address }}{{ $user->kelurahan ? ', ' . $user->kelurahan : '' }}{{ $user->kecamatan ? ', ' . $user->kecamatan : '' }}{{ ($user->city ?? optional($user->cityRelation)->name) ? ', ' . ($user->city ?? optional($user->cityRelation)->name) : '' }}{{ $user->province ? ', ' . $user->province : '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Ringkasan Dokumen -->
                <div class="bg-gray-50/80 border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200/60">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Dokumen Verifikasi
                        </h3>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Foto KTP -->
                        <div class="bg-white rounded-xl p-2.5 border border-gray-200 shadow-2xs flex flex-col justify-between">
                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center mb-1.5">
                                @php $ktpSrc = !empty($user->ktp_photo) ? asset('storage/' . $user->ktp_photo) : (!empty($user->ktp_path) ? asset('storage/' . $user->ktp_path) : ''); @endphp
                                @if($ktpSrc)
                                    <img src="{{ $ktpSrc }}" alt="KTP" class="w-full h-full object-contain">
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-700">Foto KTP</span>
                                <a href="{{ route('onboarding.step2') }}" class="text-[11px] font-bold text-blue-600 hover:underline">Ganti</a>
                            </div>
                        </div>

                        <!-- Foto Selfie -->
                        <div class="bg-white rounded-xl p-2.5 border border-gray-200 shadow-2xs flex flex-col justify-between">
                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center mb-1.5">
                                @php $selfieSrc = !empty($user->selfie_photo) ? asset('storage/' . $user->selfie_photo) : ''; @endphp
                                @if($selfieSrc)
                                    <img src="{{ $selfieSrc }}" alt="Selfie" class="w-full h-full object-contain">
                                @else
                                    <span class="text-xs text-gray-400">Belum ada</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-700">Foto Selfie</span>
                                <a href="{{ route('onboarding.step3') }}" class="text-[11px] font-bold text-blue-600 hover:underline">Ganti</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-5 pb-2 flex gap-3">
                    <a href="{{ route('onboarding.step3') }}" class="w-1/3 py-3.5 border border-gray-200 rounded-full text-center text-sm font-bold text-gray-700 hover:bg-gray-100 transition inline-flex items-center justify-center cursor-pointer">
                        ← Kembali
                    </a>
                    <button type="submit"
                        class="flex-1 text-white font-bold py-3.5 sm:py-4 rounded-full shadow-md shadow-blue-500/25 hover:bg-[#0055D4] active:scale-[0.98] transition text-base tracking-wide bg-[#0066FF] cursor-pointer">
                        Selesai & Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

