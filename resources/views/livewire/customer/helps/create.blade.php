<div>
    <style>
        :root {
            --brand-500: #0ea5a4;
            --brand-600: #08979a;
            --muted-600: #6b7280;
        }

        .card-shadow {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.2);
        }

        .header-pattern {
            position: relative;
            overflow: hidden;
        }

        .header-pattern::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-pattern::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        /* Leaflet Map Styles */
        #map {
            height: 280px !important;
            min-height: 280px;
            z-index: 1;
        }
        
        .leaflet-container {
            height: 100%;
            width: 100%;
            border-radius: 0.5rem;
        }
    </style>

    <div id="main-content" class="min-h-screen bg-white">
        <div class="max-w-md mx-auto">
            <!-- Header - BRImo Style -->
            <div class="px-5 pt-5 pb-8 relative overflow-hidden header-pattern"
                style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between text-white mb-3">
                        <a href="{{ route('customer.dashboard') }}" aria-label="Kembali ke Dashboard"
                            class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="text-center flex-1">
                            <h1 class="text-lg font-bold">Buat Permintaan Baru</h1>
                            <p class="text-xs text-white/90 mt-0.5">Isi form di bawah untuk membuat permintaan</p>
                        </div>

                        <div class="w-9"></div>
                    </div>
                </div>

                <!-- Curved separator -->
                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none"
                    aria-hidden="true">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-8">
                @if(!$isProfileComplete)
                    <div class="py-6 text-center">
                        <div class="w-16 h-16 bg-amber-50 border border-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-amber-500 shadow-2xs">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 mb-1.5">Profil Anda Belum Lengkap</h2>
                        <p class="text-xs text-gray-600 mb-5 leading-relaxed max-w-xs mx-auto">
                            Untuk dapat membuat permintaan bantuan baru, Anda wajib melengkapi data profil Anda terlebih dahulu.
                        </p>

                        @if(!empty($missingProfileFields) && count($missingProfileFields))
                            <div class="bg-amber-50/90 border border-amber-200 rounded-2xl p-4 mb-6 text-left shadow-2xs">
                                <p class="text-xs font-bold text-amber-900 mb-2.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Data yang wajib dilengkapi:
                                </p>
                                <ul class="space-y-1.5 pl-5 list-disc text-xs text-amber-800 font-medium">
                                    @foreach($missingProfileFields as $field)
                                        <li>{{ $field }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex flex-col gap-2.5">
                            <a href="{{ route('profile.edit') }}"
                               class="w-full py-3 px-4 rounded-xl text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2"
                                style="background: linear-gradient(to right, #0098e7, #0077cc);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Lengkapi Profil Sekarang
                            </a>
                            <a href="{{ route('customer.dashboard') }}"
                               class="w-full py-3 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center justify-center">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                @elseif(!$isKtpVerified)
                    <div class="py-6 text-center">
                        <div class="w-16 h-16 bg-amber-50 border border-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-amber-500 shadow-2xs">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 mb-1.5">Verifikasi KTP Sedang Diproses</h2>
                        <p class="text-xs text-gray-600 mb-5 leading-relaxed max-w-xs mx-auto">
                            Dokumen dan foto KTP Anda sedang dalam peninjauan oleh Admin. Anda belum dapat membuat permintaan bantuan baru hingga akun diverifikasi.
                        </p>

                        <div class="flex flex-col gap-2.5">
                            <a href="{{ route('profile.settings.verification') }}"
                               class="w-full py-3 px-4 rounded-xl text-amber-800 bg-amber-50 border border-amber-200 font-bold text-xs shadow-xs hover:bg-amber-100 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Cek Status Dokumen KTP
                            </a>
                            <a href="{{ route('customer.dashboard') }}"
                               class="w-full py-3 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center justify-center">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                @elseif($hasReachedHelpLimit)
                    <div class="py-6 text-center">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-amber-600 shadow-sm"
                             style="background: #fffbeb; border: 1.5px solid #f59e0b;">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900 mb-1.5">Batas Pembuatan Bantuan Tercapai</h2>
                        
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mb-3"
                             style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                            <span>{{ $activeHelpsCount }} dari {{ $maxHelpsLimit }} Kuota Bantuan Terpakai</span>
                        </div>

                        <p class="text-xs text-gray-600 mb-6 leading-relaxed max-w-xs mx-auto">
                            Akun Anda belum verifikasi email dan telah mencapai batas maksimal <strong>{{ $maxHelpsLimit }} permintaan bantuan</strong>. Silakan verifikasi email Anda terlebih dahulu untuk membuat bantuan baru tanpa batas.
                        </p>

                        <div class="flex flex-col gap-2.5 max-w-xs mx-auto">
                            <form method="POST" action="{{ route('verification.send') }}" onsubmit="event.preventDefault(); window.sendEmailVerification(this.querySelector('button'), '{{ route('verification.send') }}');">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 px-4 rounded-xl text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 hover:opacity-95 cursor-pointer"
                                        style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Kirim Link Verifikasi Email
                                </button>
                            </form>

                            <a href="{{ route('customer.helps.index') }}"
                               class="w-full py-3 px-4 rounded-xl font-bold text-xs transition flex items-center justify-center gap-2 text-white shadow-md hover:opacity-95"
                               style="background: linear-gradient(to right, #0098e7, #0077cc);">
                                Lihat Bantuan Saya
                            </a>

                            <a href="{{ route('customer.dashboard') }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition flex items-center justify-center">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                @else
                    <form wire:submit.prevent="prepareConfirm" enctype="multipart/form-data" class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                    </svg>
                                    Judul Bantuan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input type="text" wire:model="title"
                                placeholder="Contoh: Butuh Bantuan Makanan untuk Keluarga"
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white">
                            @error('title')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    Kategori Bantuan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                    </svg>
                                    Nominal Uang untuk Mitra
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold text-sm">Rp</span>
                                <input type="text"
                                    wire:ignore
                                    x-data
                                    x-init="
                                        $el.value = $wire.amount ? Number($wire.amount).toLocaleString('id-ID') : '';
                                    "
                                    x-on:input="
                                        let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                                        let max = {{ $maxNominal ?? 10000000 }};
                                        if (parseInt(raw) > max) raw = String(max);
                                        $el.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                                        $wire.set('amount', parseInt(raw) || 0);
                                    "
                                    inputmode="numeric"
                                    class="w-full pl-12 pr-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white"
                                    placeholder="0">
                            </div>
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Minimal Rp {{ number_format($minNominal ?? 10000, 0, ',', '.') }} - Maksimal Rp {{ number_format($maxNominal ?? 10000000, 0, ',', '.') }}
                            </p>
                            @error('amount')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- City (Updated with explicit data-lat and data-lng) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Kota / Wilayah Layanan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select wire:model.live="city_id" id="city-select"
                                class="w-full px-4 py-3 text-xs font-semibold rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition bg-white text-gray-800">
                                <option value="">-- Pilih Kota --</option>
                                @foreach ($cities as $city)
                                    @php
                                        // Koordinat fallback akurat kota-kota umum jika database belum terisi
                                        $fallbackCoords = [
                                            'Jakarta' => [-6.2088, 106.8456],
                                            'DKI Jakarta' => [-6.2088, 106.8456],
                                            'Yogyakarta' => [-7.7956, 110.3695],
                                            'DI Yogyakarta' => [-7.7956, 110.3695],
                                            'Ponorogo' => [-7.8664, 111.4620],
                                            'Surabaya' => [-7.2575, 112.7521],
                                            'Bandung' => [-6.9175, 107.6191],
                                            'Semarang' => [-6.9667, 110.4167],
                                            'Solo' => [-7.5755, 110.8243],
                                            'Surakarta' => [-7.5755, 110.8243],
                                            'Malang' => [-7.9666, 112.6326],
                                        ];
                                        
                                        $cityNameClean = trim(str_replace(['Kota', 'Kabupaten'], '', $city->name));
                                        $defaultCoord = $fallbackCoords[$cityNameClean] ?? $fallbackCoords[$city->name] ?? [-6.2088, 106.8456];
                                        
                                        $lat = !empty($city->latitude) ? $city->latitude : $defaultCoord[0];
                                        $lng = !empty($city->longitude) ? $city->longitude : $defaultCoord[1];
                                    @endphp
                                    <option value="{{ $city->id }}" 
                                            data-province="{{ $city->province }}"
                                            data-lat="{{ $lat }}" 
                                            data-lng="{{ $lng }}">
                                        {{ $city->name }} ({{ $city->province }})
                                    </option>
                                @endforeach
                            </select>
                            
                            @error('city_id')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                            
                            <p class="text-[11px] text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Memilih kota akan otomatis memusatkan peta ke lokasi kota tersebut
                            </p>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Alamat Lengkap
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <textarea wire:model="full_address" rows="3"
                                placeholder="Contoh: Dukuh Sabet, Desa Sumberejo, Kecamatan Balong, Kabupaten Ponorogo, Jawa Timur"
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition resize-none bg-white"></textarea>
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Tulis alamat lengkap termasuk desa, kecamatan, kabupaten, provinsi
                            </p>
                            @error('full_address')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Location Detail -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Detail Lokasi Bantuan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input type="text" wire:model="location"
                                placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 05"
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white">
                            @error('location')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Jadwal Pelaksanaan Bantuan -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 000 2h8a1 1 0 100-2H6zM4 6a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                    </svg>
                                    Jadwal Pelaksanaan Bantuan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                    <input type="date" wire:model.live="scheduled_date" min="{{ date('Y-m-d') }}" max="{{ date('Y-12-31') }}" onkeydown="return false" onclick="this.showPicker()"
                                        class="w-full px-3 py-2 text-sm rounded-lg border @error('scheduled_date') border-red-400 bg-red-50/30 @else border-gray-300 bg-white @enderror focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                                    @error('scheduled_date')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-600 mb-1">Jam <span class="text-red-500">*</span></label>
                                    <input type="time" wire:model.live="scheduled_time" onclick="this.showPicker()"
                                        class="w-full px-3 py-2 text-sm rounded-lg border @error('scheduled_time') border-red-400 bg-red-50/30 @else border-gray-300 bg-white @enderror focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                                    @error('scheduled_time')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1.5">Tentukan tanggal dan jam pelaksanaan bantuan. Jika memilih hari ini, jam tidak boleh sebelum waktu sekarang. Untuk tanggal besok dan seterusnya, bebas memilih jam berapa saja.</p>
                        </div>

                        <!-- Tandai Lokasi di Peta -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Tandai Lokasi di Peta
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>

                            <!-- Map Container -->
                            <div wire:ignore id="map" style="height: 280px; min-height: 280px;"
                                class="w-full rounded-lg border border-gray-300 mb-2 bg-gray-100"></div>

                            <!-- Koordinat Display -->
                            <div id="coordinates-display"
                                class="bg-green-50 border border-green-200 rounded-lg p-3 mb-2 hidden">
                                <p class="text-xs font-semibold text-green-800 mb-1">✓ Lokasi Ditandai:</p>
                                <p class="text-xs text-green-900 font-mono">
                                    Lat: <span id="lat-display" class="font-semibold">-</span>, 
                                    Lng: <span id="lng-display" class="font-semibold">-</span>
                                </p>
                            </div>

                            <!-- Hidden inputs for Livewire -->
                            <input type="hidden" wire:model="latitude" id="latitude-input">
                            <input type="hidden" wire:model="longitude" id="longitude-input">

                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Klik pada peta atau geser pin merah/biru untuk menyesuaikan titik tepat bantuan
                            </p>

                            @error('latitude')
                                <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Deskripsi Bantuan
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <textarea wire:model="description" rows="4"
                                placeholder="Jelaskan detail kebutuhan bantuan Anda secara lengkap..."
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition resize-none bg-white"></textarea>
                            @error('description')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Peralatan yang Sudah Disediakan -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                    </svg>
                                    Peralatan yang Sudah Disediakan
                                    <span class="text-gray-400 text-xs ml-1">(Opsional)</span>
                                </span>
                            </label>
                            <textarea wire:model="equipment_provided" rows="3"
                                placeholder="Contoh: Sudah ada gerobak dorong, ember besar 2 buah, timbangan digital"
                                class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition resize-none bg-white"></textarea>
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Tuliskan alat atau peralatan yang sudah Anda sediakan untuk membantu mitra
                            </p>
                            @error('equipment_provided')
                                <span class="text-red-500 text-xs mt-1.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Foto Kondisi / Objek Bantuan (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    Foto Kondisi / Objek (Opsional)
                                </span>
                            </label>

                            <div class="flex items-center justify-center w-full">
                                @if ($photo)
                                    <div class="relative w-full h-44 rounded-xl overflow-hidden border-2 border-blue-400 bg-gray-900 group shadow-sm">
                                        <!-- Gambar langsung muncul di kotakan -->
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview Foto" class="w-full h-full object-cover">
                                        
                                        <!-- Overlay info & tombol ganti / hapus foto saat hover -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                            <label for="photo-input" class="cursor-pointer px-3 py-1.5 bg-white/90 hover:bg-white text-gray-800 text-xs font-semibold rounded-lg shadow transition flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Ganti Foto
                                            </label>
                                            <button type="button" wire:click="$set('photo', null)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow transition flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>

                                        <!-- Tombol Hapus Cepat (Pojok Kanan Atas) -->
                                        <button type="button" wire:click="$set('photo', null)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-full shadow transition" title="Hapus Foto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <!-- Nama File (Pojok Kiri Bawah) -->
                                        <div class="absolute bottom-2 left-2 max-w-[80%] bg-black/60 backdrop-blur-sm text-white text-[11px] px-2.5 py-1 rounded-md truncate">
                                            📷 {{ $photo->getClientOriginalName() }}
                                        </div>
                                    </div>
                                    <input type="file" id="photo-input" wire:model="photo" class="hidden" accept="image/png,image/jpeg,image/jpg" />
                                @else
                                    <label for="photo-input" class="flex flex-col items-center justify-center w-full h-36 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50/50 hover:border-blue-400 transition group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <div class="w-10 h-10 mb-2 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <p class="mb-1 text-xs text-gray-700"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau ambil foto</p>
                                            <p class="text-[11px] text-gray-400">PNG, JPG, JPEG (Maks. 2MB)</p>
                                        </div>
                                        <input type="file" id="photo-input" wire:model="photo" class="hidden" accept="image/png,image/jpeg,image/jpg" />
                                    </label>
                                @endif
                            </div>

                            <!-- Keterangan Penjelas Bantuan -->
                            <p class="text-[11px] text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Tidak wajib diisi, tetapi mohon disertakan jika memungkinkan agar memudahkan mitra.
                            </p>

                            @error('photo')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-6">
                            <a href="{{ route('dashboard') }}"
                                class="flex-1 inline-flex items-center justify-center bg-white border border-gray-300 text-gray-700 px-5 py-3 text-sm rounded-lg font-semibold hover:bg-gray-50 transition">
                                Batal
                            </a>
                            <button type="submit" wire:loading.attr="disabled"
                                class="flex-1 inline-flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-3 text-sm rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="save">Kirim Permintaan</span>
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Timezone Script -->
                <script>
                    (function() {
                        const western = [
                            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 'Bengkulu',
                            'Lampung', 'Bangka Belitung',
                            'Banten', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
                            'Kalimantan Barat'
                        ];
                        const central = [
                            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Tengah', 'Kalimantan Selatan',
                            'Kalimantan Timur', 'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Gorontalo',
                            'Sulawesi Barat', 'Sulawesi Utara'
                        ];
                        const eastern = [
                            'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat'
                        ];

                        const zoneIana = {
                            'WIB': 'Asia/Jakarta',
                            'WITA': 'Asia/Makassar',
                            'WIT': 'Asia/Jayapura'
                        };

                        function provinceToZone(prov) {
                            if (!prov) return 'WIB';
                            prov = prov.trim();
                            if (western.indexOf(prov) !== -1) return 'WIB';
                            if (central.indexOf(prov) !== -1) return 'WITA';
                            if (eastern.indexOf(prov) !== -1) return 'WIT';
                            return 'WIB';
                        }

                        function formatTimeForZone(date, iana) {
                            try {
                                const fmt = new Intl.DateTimeFormat('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false,
                                    timeZone: iana
                                });
                                return fmt.format(date);
                            } catch (e) {
                                const hh = String(date.getHours()).padStart(2, '0');
                                const mm = String(date.getMinutes()).padStart(2, '0');
                                return `${hh}:${mm}`;
                            }
                        }

                        function updateTimezoneDisplay() {
                            const citySelect = document.getElementById('city-select');
                            const tzDisplay = document.getElementById('timezone-display');
                            if (!citySelect || !tzDisplay) return;
                            const opt = citySelect.options[citySelect.selectedIndex];
                            const province = opt ? (opt.dataset.province || '') : '';
                            const zone = provinceToZone(province);
                            const iana = zoneIana[zone];
                            const now = new Date();
                            const timeText = formatTimeForZone(now, iana);
                            tzDisplay.textContent = timeText ? `Waktu lokal: ${zone} — ${timeText}` : `Waktu lokal: ${zone}`;
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            const citySelect = document.getElementById('city-select');
                            const hidden = document.getElementById('scheduled-time-hidden');
                            const manual = document.getElementById('scheduled-time-manual');
                            const tzBadge = document.getElementById('timezone-badge');

                            if (citySelect) {
                                citySelect.addEventListener('change', function() {
                                    const opt = citySelect.options[citySelect.selectedIndex];
                                    const province = opt ? (opt.dataset.province || '') : '';
                                    const zone = provinceToZone(province);
                                    if (tzBadge) tzBadge.textContent = zone;
                                    updateTimezoneDisplay();
                                });
                            }

                            window.addEventListener('help:timezone-changed', function(e) {
                                try {
                                    const detail = e.detail || {};
                                    const zone = detail.zone || 'WIB';
                                    const iana = detail.iana || zoneIana[zone] || 'Asia/Jakarta';
                                    if (tzBadge) tzBadge.textContent = zone;
                                    const tzDisplayEl = document.getElementById('timezone-display');
                                    const now = new Date();
                                    try {
                                        const fmt = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: iana });
                                        const timeText = fmt.format(now);
                                        if (tzDisplayEl) tzDisplayEl.textContent = `Waktu lokal: ${zone} — ${timeText}`;
                                    } catch (err) {
                                        if (tzDisplayEl) tzDisplayEl.textContent = `Waktu lokal: ${zone}`;
                                    }
                                } catch (err) {
                                    console.error('help:timezone-changed handler error', err);
                                }
                            });

                            function normalizeManualAndSync() {
                                if (!manual || !hidden) return;
                                let v = manual.value || '';
                                const ampmMatch = v.match(/(\d{1,2}):(\d{2})\s*([AP]M)?/i);
                                if (ampmMatch) {
                                    let hh = parseInt(ampmMatch[1], 10);
                                    const mm = parseInt(ampmMatch[2], 10);
                                    const ampm = (ampmMatch[3] || '').toUpperCase();
                                    if (ampm === 'PM' && hh < 12) hh += 12;
                                    if (ampm === 'AM' && hh === 12) hh = 0;
                                    v = String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0');
                                }
                                const parts = v.split(':');
                                if (parts.length === 2) {
                                    const hh = String(parseInt(parts[0], 10) || 0).padStart(2, '0');
                                    const mm = String(parseInt(parts[1], 10) || 0).padStart(2, '0');
                                    const normalized = `${hh}:${mm}`;
                                    if (normalized !== v) manual.value = normalized;
                                    if (hidden.value !== normalized) {
                                        hidden.value = normalized;
                                        hidden.dispatchEvent(new Event('input', { bubbles: true }));
                                    }
                                }
                            }

                            if (manual) {
                                manual.addEventListener('blur', normalizeManualAndSync);
                                manual.addEventListener('change', normalizeManualAndSync);

                                manual.addEventListener('input', function(e) {
                                    const raw = manual.value || '';
                                    const selStart = manual.selectionStart || 0;
                                    const before = raw.slice(0, selStart);
                                    const digitsBefore = (before.match(/\d/g) || []).length;
                                    let digits = raw.replace(/[^0-9]/g, '').slice(0, 4);
                                    let candidate = digits.length <= 2 ? digits : digits.slice(0, 2) + ':' + digits.slice(2);

                                    if (/^\d{1,2}:\d{1,2}$/.test(candidate)) {
                                        const p = candidate.split(':');
                                        let hh = parseInt(p[0], 10) || 0;
                                        let mm = parseInt(p[1], 10) || 0;
                                        hh = Math.max(0, Math.min(23, hh));
                                        mm = Math.max(0, Math.min(59, mm));
                                        candidate = `${String(hh).padStart(2, '0')}:${String(mm).padStart(2, '0')}`;
                                    }

                                    const inputType = (e && e.inputType) ? e.inputType : '';

                                    if (inputType && inputType.startsWith('delete')) {
                                        if (digits.length <= 2) {
                                            if (manual.value !== digits) manual.value = digits;
                                            try { manual.setSelectionRange(digits.length, digits.length); } catch (err) {}
                                        } else {
                                            const newVal = digits.slice(0,2) + ':' + digits.slice(2);
                                            if (manual.value !== newVal) manual.value = newVal;
                                            let newPos = digitsBefore <= 2 ? digitsBefore : digitsBefore + 1;
                                            if (newPos > manual.value.length) newPos = manual.value.length;
                                            try { manual.setSelectionRange(newPos, newPos); } catch (err) {}
                                        }
                                    } else {
                                        if (manual.value !== candidate) {
                                            manual.value = candidate;
                                            let newPos = digitsBefore <= 2 ? digitsBefore : digitsBefore + 1;
                                            if (newPos > manual.value.length) newPos = manual.value.length;
                                            try { manual.setSelectionRange(newPos, newPos); } catch (err) {}
                                        }
                                    }

                                    if (/^\d{2}:\d{2}$/.test(candidate)) {
                                        if (hidden.value !== candidate) {
                                            hidden.value = candidate;
                                            hidden.dispatchEvent(new Event('input', { bubbles: true }));
                                        }
                                    }
                                });

                                normalizeManualAndSync();
                            }

                            setInterval(updateTimezoneDisplay, 60 * 1000);
                            updateTimezoneDisplay();
                            if (tzBadge) {
                                const opt = citySelect ? citySelect.options[citySelect.selectedIndex] : null;
                                const province = opt ? (opt.dataset.province || '') : '';
                                const zone = provinceToZone(province);
                                tzBadge.textContent = zone;
                            }
                        });
                    })();
                </script>
            </div>
        </div>
    </div>

    <!-- Global submit overlay shown only while Livewire 'save' is processing -->
    <div wire:loading.class.remove="hidden" wire:target="save"
        class="hidden fixed inset-0 z-50 flex items-end md:items-center justify-center pointer-events-none">
        <div class="pointer-events-auto mb-6 md:mb-0 bg-white bg-opacity-95 rounded-lg px-4 py-3 flex items-center gap-3 shadow-lg">
            <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <div class="text-sm font-medium text-gray-800">Mengirim...</div>
        </div>
    </div>

    <!-- Insufficient Balance Modal (Top Up Standard - Sleek & Compact Modal) -->
    @if($showInsufficientModal)
        <div class="modal-overlay" 
            style="position: fixed; inset: 0; z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(15, 23, 42, 0.7);"
            wire:click="closeInsufficientModal">
            <div style="max-width: 400px; width: 100%; margin: auto; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); display: flex; flex-direction: column; overflow: hidden; max-height: 90vh; border: 1px solid #f1f5f9;"
                wire:click.stop>
                
                <!-- Header Modal -->
                <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; border: 1px solid #dbeafe; display: flex; align-items: center; justify-content: center; color: #0284c7; flex-shrink: 0;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">Top-Up Saldo</h3>
                            <p style="font-size: 11px; color: #64748b; margin: 2px 0 0 0;">Selesaikan top-up untuk memesan bantuan</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeInsufficientModal" 
                        style="padding: 6px; border-radius: 9999px; border: none; background: transparent; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                        onmouseover="this.style.background='#f1f5f9'; this.style.color='#334155';"
                        onmouseout="this.style.background='transparent'; this.style.color='#94a3b8';">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable Body -->
                <div class="hide-scrollbar" style="padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; flex: 1;">
                    
                    <!-- Ringkasan Saldo Card -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px; display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #475569;">
                            <span>Saldo Tersedia:</span>
                            <span style="font-weight: 700; color: #0f172a;">Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #475569;">
                            <span>Total Biaya Bantuan:</span>
                            <span style="font-weight: 700; color: #0f172a;">Rp {{ number_format($confirmTotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div style="border-top: 1px solid #e2e8f0; padding-top: 6px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; color: #dc2626; font-size: 12px;">Kekurangan Saldo:</span>
                            <span style="font-weight: 900; color: #dc2626; font-size: 14px;">Rp {{ number_format($topupDeficit ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Input Nominal Top Up -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <label style="font-weight: 700; color: #334155; font-size: 12px;">Nominal Transfer <span style="color: #ef4444;">*</span></label>
                            @if($topupDeficit > 0)
                                <button type="button" wire:click="setTopupQuickAmount({{ (int)(ceil($topupDeficit / 1000) * 1000) }})"
                                    style="font-size: 10px; font-weight: 700; color: #0284c7; background: #f0f9ff; border: 1px solid #bae6fd; padding: 2px 7px; border-radius: 6px; cursor: pointer;">
                                    ⚡ Pas Kurangnya (Rp {{ number_format(ceil($topupDeficit / 1000) * 1000, 0, ',', '.') }})
                                </button>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; border-radius: 10px; border: 1.5px solid #cbd5e1; overflow: hidden; background: #ffffff;">
                            <span style="padding: 8px 12px; background: #f8fafc; border-right: 1px solid #e2e8f0; font-size: 13px; font-weight: 800; color: #64748b; user-select: none;">Rp</span>
                            <input type="text"
                                wire:ignore
                                x-data
                                x-init="
                                    $el.value = $wire.topupAmount ? Number($wire.topupAmount).toLocaleString('id-ID') : '';
                                    $wire.on('topup-amount-updated', (val) => {
                                        $el.value = val ? Number(val).toLocaleString('id-ID') : '';
                                    });
                                "
                                x-on:input="
                                    let raw = $el.value.replace(/\./g, '').replace(/\D/g, '');
                                    $el.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                                    $wire.set('topupAmount', parseInt(raw) || 0);
                                "
                                inputmode="numeric"
                                style="width: 100%; padding: 8px 10px; font-size: 14px; font-weight: 800; color: #0f172a; border: none; outline: none; background: transparent;"
                                placeholder="10.000">
                        </div>
                        @error('topupAmount') <span style="font-size: 11px; color: #dc2626; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        
                        <!-- Quick Buttons -->
                        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; margin-top: 6px;">
                            <button type="button" wire:click="setTopupQuickAmount(20000)"
                                style="padding: 6px 0; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid {{ $topupAmount == 20000 ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupAmount == 20000 ? '#f0f9ff' : '#ffffff' }}; color: {{ $topupAmount == 20000 ? '#0369a1' : '#475569' }}; cursor: pointer;">20K</button>
                            <button type="button" wire:click="setTopupQuickAmount(50000)"
                                style="padding: 6px 0; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid {{ $topupAmount == 50000 ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupAmount == 50000 ? '#f0f9ff' : '#ffffff' }}; color: {{ $topupAmount == 50000 ? '#0369a1' : '#475569' }}; cursor: pointer;">50K</button>
                            <button type="button" wire:click="setTopupQuickAmount(100000)"
                                style="padding: 6px 0; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid {{ $topupAmount == 100000 ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupAmount == 100000 ? '#f0f9ff' : '#ffffff' }}; color: {{ $topupAmount == 100000 ? '#0369a1' : '#475569' }}; cursor: pointer;">100K</button>
                            <button type="button" wire:click="setTopupQuickAmount(200000)"
                                style="padding: 6px 0; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid {{ $topupAmount == 200000 ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupAmount == 200000 ? '#f0f9ff' : '#ffffff' }}; color: {{ $topupAmount == 200000 ? '#0369a1' : '#475569' }}; cursor: pointer;">200K</button>
                            <button type="button" wire:click="setTopupQuickAmount(500000)"
                                style="padding: 6px 0; font-size: 11px; font-weight: 700; border-radius: 6px; border: 1px solid {{ $topupAmount == 500000 ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupAmount == 500000 ? '#f0f9ff' : '#ffffff' }}; color: {{ $topupAmount == 500000 ? '#0369a1' : '#475569' }}; cursor: pointer;">500K</button>
                        </div>

                        {{-- Ringkasan Biaya Admin --}}
                        @if($topupAmount > 0)
                        <div style="margin-top: 8px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 10px 12px; display: flex; flex-direction: column; gap: 5px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #475569;">
                                <span>Nominal Top-Up</span>
                                <span style="font-weight: 600;">Rp {{ number_format($topupAmount, 0, ',', '.') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #475569;">
                                <span>Biaya Admin</span>
                                <span style="font-weight: 600; color: #dc2626;">+ Rp {{ number_format($topupAdminFee, 0, ',', '.') }}</span>
                            </div>
                            <div style="border-top: 1px dashed #93c5fd; padding-top: 5px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 12px; font-weight: 800; color: #0369a1;">Total Transfer</span>
                                <span style="font-size: 14px; font-weight: 900; color: #0369a1;">Rp {{ number_format($topupTotalTransfer, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Pilihan Tab Metode Pembayaran -->
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; font-size: 12px; margin-bottom: 6px;">Metode Pembayaran <span style="color: #ef4444;">*</span></label>
                        
                        <!-- Segmented Switcher -->
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; padding: 3px; background: #f1f5f9; border-radius: 10px; margin-bottom: 10px;">
                            @if($qrisEnabled)
                                <button type="button" wire:click="selectTopupMethod('qris')"
                                    style="padding: 7px 0; text-align: center; font-size: 12px; font-weight: 700; border-radius: 7px; border: none; cursor: pointer; transition: all 0.2s; background: {{ $topupMethod === 'qris' ? '#ffffff' : 'transparent' }}; color: {{ $topupMethod === 'qris' ? '#0284c7' : '#64748b' }}; box-shadow: {{ $topupMethod === 'qris' ? '0 1px 2px rgba(0,0,0,0.08)' : 'none' }};">
                                    📱 QRIS E-Wallet
                                </button>
                            @endif
                            <button type="button" wire:click="selectTopupMethod('{{ $availableBanks[0]['value'] ?? 'bank_bca' }}')"
                                style="padding: 7px 0; text-align: center; font-size: 12px; font-weight: 700; border-radius: 7px; border: none; cursor: pointer; transition: all 0.2s; background: {{ str_starts_with($topupMethod, 'bank_') ? '#ffffff' : 'transparent' }}; color: {{ str_starts_with($topupMethod, 'bank_') ? '#0284c7' : '#64748b' }}; box-shadow: {{ str_starts_with($topupMethod, 'bank_') ? '0 1px 2px rgba(0,0,0,0.08)' : 'none' }};">
                                🏦 Transfer Bank
                            </button>
                        </div>

                        <!-- Konten QRIS -->
                        @if($topupMethod === 'qris')
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px; text-align: center;">
                                <p style="font-size: 11px; font-weight: 700; color: #1e293b; margin: 0 0 6px 0;">Scan QRIS (Semua E-Wallet / Bank)</p>
                                @if(file_exists(public_path('images/payment/qris.png')))
                                    <div style="background: #ffffff; padding: 6px; border-radius: 10px; border: 1px solid #e2e8f0; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 6px;">
                                        <img src="{{ asset('images/payment/qris.png') }}" 
                                            alt="QRIS QR Code" 
                                            style="width: 130px; height: 130px; margin: auto; object-fit: contain; display: block;">
                                    </div>
                                @else
                                    <div style="width: 130px; height: 130px; margin: auto; border: 2px dashed #cbd5e1; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #ffffff; margin-bottom: 6px;">
                                        <p style="font-size: 11px; color: #94a3b8; margin: 0;">QRIS Code</p>
                                    </div>
                                @endif
                                <p style="font-size: 10px; color: #64748b; margin: 0;">GoPay, OVO, DANA, LinkAja, ShopeePay, BCA, Mandiri, dll</p>
                            </div>
                        @endif

                        <!-- Konten Transfer Bank -->
                        @if(str_starts_with($topupMethod, 'bank_'))
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                @foreach($availableBanks as $bank)
                                    <div wire:click="selectTopupMethod('{{ $bank['value'] }}')"
                                        style="border: 2px solid {{ $topupMethod === $bank['value'] ? '#0284c7' : '#e2e8f0' }}; background: {{ $topupMethod === $bank['value'] ? '#f0f9ff' : '#ffffff' }}; border-radius: 12px; padding: 8px 10px; cursor: pointer; transition: all 0.2s;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 36px; height: 36px; background: #ffffff; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; font-size: 10px; font-weight: 900; color: #1e293b; flex-shrink: 0;">
                                                {{ strtoupper($bank['code']) }}
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                    <span style="font-weight: 800; color: #0f172a; font-size: 12px;">{{ $bank['name'] }}</span>
                                                    <button type="button" 
                                                        onclick="event.stopPropagation(); navigator.clipboard.writeText('{{ $bank['account_number'] }}'); alert('Nomor rekening {{ $bank['name'] }} ({{ $bank['account_number'] }}) berhasil disalin!');"
                                                        style="padding: 2px 6px; font-size: 10px; font-weight: 700; background: #e2e8f0; color: #0369a1; border-radius: 4px; border: none; cursor: pointer;">
                                                        📋 Salin
                                                    </button>
                                                </div>
                                                <p style="font-family: monospace; font-size: 12px; font-weight: 800; color: #1e293b; margin: 1px 0 0 0;">{{ $bank['account_number'] }}</p>
                                                <p style="font-size: 10px; color: #64748b; margin: 0;">a.n. {{ $bank['account_name'] }}</p>
                                            </div>
                                            @if($topupMethod === $bank['value'])
                                                <svg style="width: 18px; height: 18px; color: #0284c7; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('topupMethod') <span style="font-size: 11px; color: #dc2626; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Upload Bukti Transfer -->
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; font-size: 12px; margin-bottom: 6px;">Upload Bukti Transfer <span style="color: #ef4444;">*</span></label>
                        
                        @if ($topupReceipt)
                            <div style="position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc; padding: 8px; text-align: center;">
                                <img src="{{ $topupReceipt->temporaryUrl() }}" style="max-height: 100px; margin: auto; border-radius: 6px; object-fit: contain; display: block;">
                                <button type="button" wire:click="$set('topupReceipt', null)" 
                                    style="position: absolute; top: 8px; right: 8px; background: #dc2626; color: #ffffff; padding: 4px; border-radius: 9999px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <p style="font-size: 11px; color: #16a34a; font-weight: 700; margin: 4px 0 0 0;">✓ Bukti transfer siap dikirim</p>
                            </div>
                        @else
                            <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 64px; border: 2px dashed #cbd5e1; border-radius: 12px; cursor: pointer; background: #f8fafc; transition: all 0.2s;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <svg style="width: 18px; height: 18px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span style="font-size: 12px; font-weight: 700; color: #334155;">Pilih Bukti Transfer</span>
                                    <span style="font-size: 10px; color: #94a3b8;">(Maks 2MB)</span>
                                </div>
                                <input type="file" wire:model="topupReceipt" accept="image/*" style="display: none;">
                            </label>
                        @endif

                        @error('topupReceipt')
                            <span style="font-size: 11px; color: #dc2626; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 8px 10px; font-size: 11px; color: #166534; display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 12px;">💡</span>
                        <p style="margin: 0; line-height: 1.3;">Saldo otomatis diverifikasi & masuk ke akun Anda setelah admin mengecek bukti.</p>
                    </div>
                </div>

                <!-- Footer Action Buttons (Proporsional & Seimbang) -->
                <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                    <button wire:click="closeInsufficientModal" type="button"
                        style="flex: 1; height: 44px; border-radius: 12px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9';"
                        onmouseout="this.style.background='#ffffff';">
                        Batal
                    </button>
                    <button wire:click="processDirectTopup" type="button" wire:loading.attr="disabled" wire:target="processDirectTopup"
                        style="flex: 2; height: 44px; border-radius: 12px; border: none; background: linear-gradient(135deg, #0098e7, #0077cc); color: #ffffff; font-size: 13px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(0, 152, 231, 0.35); transition: opacity 0.2s;"
                        onmouseover="this.style.opacity='0.95';"
                        onmouseout="this.style.opacity='1';">
                        <span wire:loading.remove wire:target="processDirectTopup" style="display: inline-flex; align-items: center; gap: 6px;">
                            <span>Kirim Bukti</span>
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                        <span wire:loading.flex wire:target="processDirectTopup" style="display: none; align-items: center; gap: 6px;">
                            <svg class="animate-spin" style="width: 16px; height: 16px; color: #ffffff;" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>Mengirim...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Confirmation Modal -->
    @if ($showConfirmModal)
        <div class="modal-overlay fixed inset-0 z-[9999] flex items-end justify-center animate-fade-in"
            style="background: rgba(0,0,0,0.5);" wire:click="closeConfirmModal">
            <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[85vh] overflow-y-auto hide-scrollbar animate-slide-up relative"
                wire:click.stop style="padding-bottom: env(safe-area-inset-bottom,24px);">
                <div class="sticky top-0 bg-white border-b px-5 py-4 rounded-t-3xl z-10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Permintaan</h3>
                        <button type="button" wire:click="closeConfirmModal"
                            class="p-2 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="p-5 pb-6">
                    <p class="text-sm text-gray-600 mb-4">Periksa ringkasan pesanan sebelum mengonfirmasi.</p>

                    <!-- Saldo Info Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-4 mb-4 border border-blue-100">
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-blue-100">
                            <span class="text-xs font-semibold text-gray-600">Saldo Tersedia</span>
                            <span class="text-lg font-bold text-gray-900">Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Nominal Bantuan</span>
                                <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($confirmAmount ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Biaya Admin</span>
                                <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($confirmAdminFee ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-white border-2 border-blue-200 rounded-2xl p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-xs font-semibold text-gray-600">Total Pembayaran</span>
                                <div class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($confirmTotal ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if ($confirmScheduled)
                        <div class="mb-4">
                            <div class="text-xs text-gray-600">Jadwal Permintaan</div>
                            <div class="text-sm font-semibold">{{ $confirmScheduled }}</div>
                        </div>
                    @endif

                    <!-- Info Box -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5">
                        <div class="flex gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs text-amber-900 leading-relaxed">
                                Dengan menekan <strong>Konfirmasi</strong>, Anda menyetujui bahwa saldo akan dipotong sesuai total pembayaran di atas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sticky footer with action buttons -->
                <div class="sticky bottom-0 bg-white border-t px-5 py-4 z-20 flex gap-3">
                    <button wire:click="closeConfirmModal" type="button"
                        class="flex-1 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                        Kembali
                    </button>
                    <button wire:click="save" type="button" wire:loading.attr="disabled"
                        class="flex-1 px-5 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold hover:from-blue-600 hover:to-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Konfirmasi</span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Instant Success Modal Pop-Up -->
    @if($showSuccessModal)
        <div class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity animate-in fade-in duration-200">
            <div class="bg-white rounded-3xl w-full max-w-sm p-6 shadow-2xl text-center transform animate-in zoom-in-95 duration-300">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1.5">Permintaan Terkirim!</h3>
                <p class="text-xs text-gray-600 leading-relaxed mb-6">
                    Permintaan bantuan Anda berhasil dibuat dan saat ini sedang mencari mitra di sekitar Anda.
                </p>

                <div class="flex flex-col gap-2.5">
                    <a href="{{ route('customer.helps.index') }}" 
                       class="w-full py-3 px-4 rounded-xl text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2"
                       style="background: linear-gradient(to right, #0098e7, #0077cc);">
                        <span>Lihat Permintaan Saya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ route('customer.dashboard') }}" 
                       class="w-full py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- OpenStreetMap Script with City-Sync -->
    <script>
        (function() {
            const style = document.createElement('style');
            style.innerHTML = `
                .hide-scrollbar::-webkit-scrollbar { display: none; }
                .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
                @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
                .animate-fade-in { animation: fadeIn 0.2s ease-out; }
                .animate-slide-up { animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
                body.modal-open { overflow: hidden; }
                body.modal-open #bottom-nav { display: none !important; }
            `;
            document.head.appendChild(style);

            function updateModalState() {
                const hasOverlay = document.querySelector('.modal-overlay') !== null;
                if (hasOverlay) {
                    document.body.classList.add('modal-open');
                } else {
                    document.body.classList.remove('modal-open');
                }
            }

            const observer = new MutationObserver(updateModalState);
            document.addEventListener('DOMContentLoaded', function() {
                updateModalState();
                observer.observe(document.body, { childList: true, subtree: true });
            });
            document.addEventListener('livewire:navigated', updateModalState);
        })();

        // Map initialization
        let leafletMapInstance = null;
        let leafletMarkerInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeMap, 150);
        });

        document.addEventListener('livewire:navigated', function() {
            setTimeout(initializeMap, 150);
        });

        function initializeMap() {
            const mapEl = document.getElementById('map');
            if (!mapEl) return;

            // Reset instance if it exists to avoid leaflet container collision
            if (mapEl._leaflet_id) {
                mapEl._leaflet_id = null;
            }

            const citySelect = document.getElementById('city-select');
            let initialLocation = [-6.2088, 106.8456]; // Default awal Jakarta
            
            // Periksa jika sudah ada pilihan kota aktif
            if (citySelect && citySelect.selectedIndex > 0) {
                const selectedOpt = citySelect.options[citySelect.selectedIndex];
                const optLat = parseFloat(selectedOpt.getAttribute('data-lat'));
                const optLng = parseFloat(selectedOpt.getAttribute('data-lng'));
                if (!isNaN(optLat) && !isNaN(optLng)) {
                    initialLocation = [optLat, optLng];
                }
            }

            // Koordinat dari Livewire (jika ada data edit / sebelumnya)
            let existingLat = null;
            let existingLng = null;
            try {
                existingLat = @this.get('latitude');
                existingLng = @this.get('longitude');
            } catch (err) {}

            if (existingLat && existingLng) {
                initialLocation = [parseFloat(existingLat), parseFloat(existingLng)];
            }

            leafletMapInstance = L.map('map', {
                center: initialLocation,
                zoom: 13,
                scrollWheelZoom: true,
                zoomControl: true
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(leafletMapInstance);

            setTimeout(function() {
                if (leafletMapInstance) leafletMapInstance.invalidateSize();
            }, 250);

            // Buat marker
            leafletMarkerInstance = L.marker(initialLocation, { draggable: true }).addTo(leafletMapInstance);

            function updateCoordinates(lat, lng) {
                const coordDisp = document.getElementById('coordinates-display');
                const latDisp = document.getElementById('lat-display');
                const lngDisp = document.getElementById('lng-display');
                
                if (coordDisp) coordDisp.classList.remove('hidden');
                if (latDisp) latDisp.textContent = parseFloat(lat).toFixed(6);
                if (lngDisp) lngDisp.textContent = parseFloat(lng).toFixed(6);

                try {
                    @this.set('latitude', lat);
                    @this.set('longitude', lng);
                } catch (e) {
                    const latInput = document.getElementById('latitude-input');
                    const lngInput = document.getElementById('longitude-input');
                    if (latInput) {
                        latInput.value = lat;
                        latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (lngInput) {
                        lngInput.value = lng;
                        lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            }

            // Pasang koordinat pertama
            updateCoordinates(initialLocation[0], initialLocation[1]);

            // Drag event marker
            leafletMarkerInstance.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
            });

            // Click event pada peta
            leafletMapInstance.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                leafletMarkerInstance.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
            });

            // DETEKSI PERUBAHAN PILIHAN KOTA -> Geser Peta Otomatis
            if (citySelect) {
                citySelect.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const targetLat = parseFloat(opt.getAttribute('data-lat'));
                    const targetLng = parseFloat(opt.getAttribute('data-lng'));

                    if (!isNaN(targetLat) && !isNaN(targetLng) && leafletMapInstance && leafletMarkerInstance) {
                        leafletMapInstance.flyTo([targetLat, targetLng], 14, {
                            animate: true,
                            duration: 1.2
                        });
                        leafletMarkerInstance.setLatLng([targetLat, targetLng]);
                        updateCoordinates(targetLat, targetLng);
                    }
                });
            }
        }
    </script>
</div>