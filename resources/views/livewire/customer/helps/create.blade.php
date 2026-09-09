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
                                <input type="number" wire:model.live="amount" min="{{ $minNominal ?? 10000 }}" max="{{ $maxNominal ?? 10000000 }}" step="1000" maxlength="8"
                                    oninput="if(this.value.length > 8) this.value = this.value.slice(0, 8); if(Number(this.value) > {{ $maxNominal ?? 10000000 }}) this.value = {{ $maxNominal ?? 10000000 }};"
                                    class="w-full pl-12 pr-4 py-3 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-white">
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

                            <!-- Hidden inputs for Livewire -->
                            <input type="hidden" wire:model="latitude" id="latitude-input">
                            <input type="hidden" wire:model="longitude" id="longitude-input">

                            <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Klik pada peta untuk menandai titik lokasi bantuan
                            </p>
                            @error('latitude')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                            @error('longitude')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
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
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        @if ($photo)
                                            <p class="text-xs text-green-600 font-medium mb-1">Foto terpilih: {{ $photo->getClientOriginalName() }}</p>
                                        @else
                                            <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mb-1 text-xs text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                                            <p class="text-xs text-gray-400">PNG, JPG, JPEG (Maks. 2MB)</p>
                                        @endif
                                    </div>
                                    <input type="file" wire:model="photo" class="hidden" accept="image/png,image/jpeg,image/jpg" />
                                </label>
                            </div>
                            @error('photo')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror

                            @if ($photo)
                                <div class="mt-2">
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-24 w-auto rounded-lg object-cover border border-gray-200">
                                </div>
                            @endif
                        </div>

                        <!-- Tombol Submit Form Bantuan -->
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Lanjutkan & Tinjau Permintaan
                            </button>
                        </div>
                    </form>
                @endif
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

    <!-- Insufficient Balance Modal -->
    @if($showInsufficientModal)
        <div class="modal-overlay fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div class="relative bg-white rounded-3xl w-full max-w-sm p-5 shadow-2xl animate-in zoom-in-95 duration-200 max-h-[85vh] overflow-y-auto hide-scrollbar flex flex-col">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Saldo Tidak Cukup</h3>
                            <span class="inline-flex items-center text-[11px] font-semibold text-emerald-600">⚡ Top Up Tanpa Reset Form</span>
                        </div>
                    </div>
                    <button wire:click="closeInsufficientModal" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Balance Breakdown Card -->
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-3 mb-3 text-xs space-y-1.5">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Saldo Anda Saat Ini:</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($currentBalance ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Total Biaya Bantuan:</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($confirmTotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-1.5 border-t border-gray-200 flex justify-between items-center text-amber-700 font-bold">
                        <span>Kekurangan Saldo:</span>
                        <span class="text-sm text-red-600">Rp {{ number_format($topupDeficit ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Topup Amount Input & Quick Chips -->
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nominal Top Up</label>
                    <div class="flex rounded-xl shadow-xs border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 overflow-hidden bg-white">
                        <span class="inline-flex items-center px-3 bg-gray-100 border-r border-gray-200 text-gray-700 font-bold text-xs select-none">Rp</span>
                        <input type="number" wire:model="topupAmount" min="10000" step="1000"
                            class="w-full px-3 py-2 text-sm font-semibold border-0 focus:ring-0 focus:outline-hidden text-gray-900 bg-transparent">
                    </div>
                    @error('topupAmount')
                        <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror

                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if($topupDeficit > 0)
                            <button type="button" wire:click="$set('topupAmount', {{ (int)(ceil($topupDeficit / 1000) * 1000) }})"
                                class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                Pas Kurangnya (Rp {{ number_format(ceil($topupDeficit / 1000) * 1000, 0, ',', '.') }})
                            </button>
                        @endif
                        <button type="button" wire:click="$set('topupAmount', 25000)"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            Rp 25.000
                        </button>
                        <button type="button" wire:click="$set('topupAmount', 50000)"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            Rp 50.000
                        </button>
                        <button type="button" wire:click="$set('topupAmount', 100000)"
                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            Rp 100.000
                        </button>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Metode Pembayaran</label>
                    <div class="space-y-1.5">
                        <label class="flex items-center justify-between p-2.5 border rounded-xl cursor-pointer transition text-left {{ $topupMethod === 'all' ? 'border-blue-500 bg-blue-50/50 text-blue-900 font-semibold ring-1 ring-blue-500' : 'border-gray-200 hover:bg-gray-50' }}">
                            <div class="flex items-center gap-2">
                                <input type="radio" wire:model.live="topupMethod" value="all" class="text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-gray-900">Semua Metode (Lengkap)</div>
                                    <div class="text-[10px] text-gray-500">Bisa pilih Bank, QRIS, GoPay, ShopeePay di Midtrans</div>
                                </div>
                            </div>
                            <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-md font-bold">Rekomendasi</span>
                        </label>

                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex flex-col p-2 border rounded-xl cursor-pointer transition text-left {{ $topupMethod === 'bank' ? 'border-blue-500 bg-blue-50/50 text-blue-900 font-semibold ring-1 ring-blue-500' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <input type="radio" wire:model.live="topupMethod" value="bank" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold">Transfer Bank</span>
                                </div>
                                <span class="text-[10px] text-gray-500 pl-4">BCA, BRI, Mandiri, BNI</span>
                            </label>

                            <label class="flex flex-col p-2 border rounded-xl cursor-pointer transition text-left {{ $topupMethod === 'ewallet' ? 'border-blue-500 bg-blue-50/50 text-blue-900 font-semibold ring-1 ring-blue-500' : 'border-gray-200 hover:bg-gray-50' }}">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <input type="radio" wire:model.live="topupMethod" value="ewallet" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold">QRIS & E-Wallet</span>
                                </div>
                                <span class="text-[10px] text-gray-500 pl-4">GoPay, ShopeePay, QRIS</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-2.5 mb-4">
                    <p class="text-[11px] text-blue-800 leading-tight">
                        ✨ <strong>Data form tidak akan hilang.</strong> Setelah pembayaran Midtrans berhasil, saldo langsung terisi dan konfirmasi bantuan otomatis terbuka.
                    </p>
                </div>

                <div class="flex gap-2.5 mt-auto pt-1">
                    <button wire:click="closeInsufficientModal" type="button"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="processDirectTopup" type="button" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 rounded-xl text-white text-xs font-bold text-center shadow transition flex items-center justify-center gap-2 disabled:opacity-50"
                        style="background: linear-gradient(to right, #0098e7, #0077cc);">
                        <span wire:loading.remove wire:target="processDirectTopup">Bayar via Midtrans</span>
                        <span wire:loading wire:target="processDirectTopup" class="flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
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
                .blur-target { filter: blur(8px); transition: filter 0.3s ease; }
                body.modal-open { overflow: hidden; }
                body.modal-open #bottom-nav { display: none !important; }
                .modal-overlay { filter: none !important; }
            `;
            document.head.appendChild(style);

            function updateModalState() {
                const hasOverlay = document.querySelector('.modal-overlay') !== null;
                const mainContent = document.getElementById('main-content');

                if (hasOverlay) {
                    document.body.classList.add('modal-open');
                    if (mainContent) mainContent.classList.add('blur-target');
                } else {
                    document.body.classList.remove('modal-open');
                    if (mainContent) mainContent.classList.remove('blur-target');
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

@push('scripts')
    {{-- Midtrans Snap JS --}}
    <script
        src="https://{{ config('services.midtrans.is_production') ? 'app.midtrans.com' : 'app.sandbox.midtrans.com' }}/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <script>
        (function() {
            function handleDirectMidtransSnap(snapToken) {
                if (!snapToken) {
                    console.error('Snap token kosong');
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    console.error('Snap JS belum termuat');
                    alert('Sistem pembayaran Midtrans sedang memuat. Silakan coba sesaat lagi.');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        try {
                            fetch('{{ route('topup.client-callback') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: result.order_id, payment_status: 'success' })
                            }).finally(function () {
                                if (window.Livewire) {
                                    Livewire.dispatch('topupCompleted');
                                }
                            });
                        } catch (e) {
                            if (window.Livewire) {
                                Livewire.dispatch('topupCompleted');
                            }
                        }
                    },
                    onPending: function (result) {
                        try {
                            fetch('{{ route('topup.client-callback') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    order_id: result.order_id,
                                    payment_status: 'pending_va',
                                    va_number: result.va_numbers ? result.va_numbers[0]?.va_number : null
                                })
                            }).finally(function() {
                                if (window.Livewire) {
                                    Livewire.dispatch('topupCompleted');
                                }
                            });
                        } catch(e) {
                            if (window.Livewire) {
                                Livewire.dispatch('topupCompleted');
                            }
                        }
                    },
                    onError: function (result) {
                        alert('Pembayaran gagal atau dibatalkan.');
                    },
                    onClose: function () {
                        console.log('Modal Midtrans ditutup');
                    }
                });
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('openDirectMidtransSnap', function (event) {
                    let token = (Array.isArray(event) && event.length > 0) 
                        ? (event[0].snapToken || event[0]) 
                        : (event?.snapToken || event);
                    handleDirectMidtransSnap(token);
                });
            });

            window.addEventListener('openDirectMidtransSnap', function (e) {
                let token = e.detail?.snapToken || e.detail;
                if (token) {
                    handleDirectMidtransSnap(token);
                }
            });
        })();
    </script>
@endpush