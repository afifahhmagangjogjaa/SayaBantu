@php
    $title = 'Tarif & Radius Bantuan';
    $breadcrumb = 'Super Admin / Pengaturan / Bantuan / Tarif & Radius';
@endphp

<div>
    <!-- Sub-Nav Tabs Pengaturan Bantuan -->
    <div class="mb-6 bg-white border border-slate-200/90 rounded-2xl p-1.5 shadow-2xs">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <a href="{{ route('superadmin.pengaturan.bantuan.fee-pendapatan') }}" wire:navigate
                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.pengaturan.bantuan.fee-pendapatan*') ? 'bg-[#0066FF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <span>Fee & Pendapatan</span>
            </a>
            <a href="{{ route('superadmin.pengaturan.bantuan.biaya-topup') }}" wire:navigate
                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.pengaturan.bantuan.biaya-topup*') ? 'bg-[#0066FF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Biaya Top-Up & Bank</span>
            </a>
            <a href="{{ route('superadmin.pengaturan.bantuan.tarif-radius') }}" wire:navigate
                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.pengaturan.bantuan.tarif-radius*') || request()->routeIs('superadmin.settings.radius*') || request()->routeIs('superadmin.settings.tarif-radius*') ? 'bg-[#0066FF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Tarif & Radius Jangkauan Mitra</span>
            </a>
        </div>
    </div>

    <!-- Quick Overview Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- Stat 1: Tarif Reguler -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs hover:shadow-xs transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Min. Bantuan Reguler</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">Rp {{ number_format((int) ($min_help_nominal ?? 10000), 0, ',', '.') }}</p>
                <span class="text-[11px] text-blue-600 font-semibold">Tarif dasar pesanan biasa</span>
            </div>
        </div>

        <!-- Stat 2: Tarif Prioritas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs hover:shadow-xs transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Min. Bantuan Prioritas</p>
                <p class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">Rp {{ number_format((int) ($default_urgent_nominal ?? 50000), 0, ',', '.') }}</p>
                <span class="text-[11px] text-amber-600 font-semibold">Tarif dasar pesanan urgent</span>
            </div>
        </div>

        <!-- Stat 3: Radius Jarak Mitra -->
        <div class="bg-gradient-to-br from-indigo-50/80 via-white to-indigo-50/30 rounded-2xl border border-indigo-200/80 p-4 sm:p-5 shadow-2xs hover:shadow-xs transition flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-indigo-900/60 uppercase tracking-wider">Radius Jangkauan Mitra</p>
                <p class="text-lg sm:text-xl font-black text-indigo-700 truncate">{{ $mitra_max_distance_km ?? 10 }} KM</p>
                <span class="text-[11px] text-indigo-600 font-semibold">Batas ambil order bantuan</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-emerald-900 font-bold text-sm">Pengaturan Berhasil Disimpan</p>
                        <p class="text-emerald-700 text-xs">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div id="settingsFlash" data-message="{{ session('message') ?? '' }}" style="display:none"></div>

        <!-- 1 & 2: Grid 2 Kolom untuk Tarif Bantuan Reguler & Prioritas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- 1. Konfigurasi Tarif Bantuan Reguler -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden flex flex-col justify-between hover:border-slate-300 transition duration-200">
                <div class="border-b border-slate-100 px-5 sm:px-6 py-4 bg-slate-50/60 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 truncate">Tarif Bantuan Reguler</h2>
                            <p class="text-xs text-slate-500 truncate">Batas minimal komisi order reguler</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200/60 text-xs font-semibold rounded-lg shrink-0">Reguler</span>
                </div>

                <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Nominal Minimal Bantuan Standar</label>
                        <div class="flex items-center rounded-xl border border-slate-300 bg-white hover:border-blue-400 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100 transition px-3.5 py-1 shadow-2xs">
                            <span class="text-slate-400 font-bold text-sm mr-2 select-none">Rp</span>
                            <input type="text"
                                inputmode="numeric"
                                wire:ignore
                                x-data
                                x-init="$el.value = ($wire.min_help_nominal !== null && $wire.min_help_nominal !== '') ? Number($wire.min_help_nominal).toLocaleString('id-ID') : ''"
                                x-on:input="
                                    let raw = $el.value.replace(/\D/g, '');
                                    $el.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                    $wire.set('min_help_nominal', raw ? parseInt(raw, 10) : 0);
                                "
                                value="{{ number_format((int) ($min_help_nominal ?? 10000), 0, ',', '.') }}"
                                placeholder="10.000"
                                class="w-full py-2 text-sm sm:text-base font-bold text-slate-900 bg-transparent outline-none border-none focus:ring-0" />
                        </div>
                        @error('min_help_nominal')
                            <div class="flex items-center gap-2 mt-2 text-rose-600 text-xs font-medium">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <p class="text-xs text-slate-500 mt-4 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                        Nominal terendah yang dapat dimasukkan customer saat membuat permintaan order bantuan biasa.
                    </p>
                </div>
            </div>

            <!-- 2. Konfigurasi Bantuan Prioritas / Urgent -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden flex flex-col justify-between hover:border-slate-300 transition duration-200">
                <div class="border-b border-slate-100 px-5 sm:px-6 py-4 bg-amber-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-slate-900 truncate">Tarif Bantuan Prioritas</h2>
                            <p class="text-xs text-slate-500 truncate">Batas minimal order berlabel urgent</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 text-xs font-semibold rounded-lg shrink-0">Prioritas</span>
                </div>

                <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Nominal Minimal Bantuan Prioritas</label>
                        <div class="flex items-center rounded-xl border border-slate-300 bg-white hover:border-amber-400 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-100 transition px-3.5 py-1 shadow-2xs">
                            <span class="text-slate-400 font-bold text-sm mr-2 select-none">Rp</span>
                            <input type="text"
                                inputmode="numeric"
                                wire:ignore
                                x-data
                                x-init="$el.value = ($wire.default_urgent_nominal !== null && $wire.default_urgent_nominal !== '') ? Number($wire.default_urgent_nominal).toLocaleString('id-ID') : ''"
                                x-on:input="
                                    let raw = $el.value.replace(/\D/g, '');
                                    $el.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                    $wire.set('default_urgent_nominal', raw ? parseInt(raw, 10) : 0);
                                "
                                value="{{ number_format((int) ($default_urgent_nominal ?? 50000), 0, ',', '.') }}"
                                placeholder="50.000"
                                class="w-full py-2 text-sm sm:text-base font-bold text-slate-900 bg-transparent outline-none border-none focus:ring-0" />
                        </div>
                        @error('default_urgent_nominal')
                            <div class="flex items-center gap-2 mt-2 text-rose-600 text-xs font-medium">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <p class="text-xs text-slate-500 mt-4 leading-relaxed bg-amber-50/50 p-3 rounded-xl border border-amber-100">
                        Batas tarif terendah saat opsi "Prioritas / Urgent" dipilih. Order darurat tidak dapat dibuat di bawah nilai ini.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. FEATURED SECTION: Konfigurasi Jarak & Radius Mitra -->
        <div class="bg-white rounded-2xl shadow-xs border border-indigo-200/90 overflow-hidden mb-8" id="radius-section">
            <div class="border-b border-indigo-100 px-5 sm:px-8 py-5 bg-gradient-to-r from-indigo-50/90 via-white to-blue-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-500/25">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Jangkauan & Radius Jarak Mitra</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Batas jarak maksimal antara mitra dengan customer untuk mendeteksi & mengambil order</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-100/90 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        Radius Aktif: {{ $mitra_max_distance_km ?? 10 }} KM
                    </span>
                    <span class="px-2.5 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl border border-slate-200 hidden md:inline-block">GPS & Geofencing</span>
                </div>
            </div>

            <div class="p-5 sm:p-8" x-data="{
                radius: @entangle('mitra_max_distance_km'),
                setRadius(val) {
                    this.radius = val;
                    $wire.set('mitra_max_distance_km', val);
                }
            }">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Controls (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Input Box & Suffix -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Batas Radius Maksimal</label>
                            <div class="flex items-center rounded-2xl border-2 border-indigo-200/90 bg-indigo-50/20 px-4 py-2.5 focus-within:border-indigo-600 focus-within:ring-4 focus-within:ring-indigo-100 transition shadow-2xs">
                                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <input type="number" step="0.5" min="1" max="100" 
                                    x-model.number="radius"
                                    x-on:input.debounce.250ms="$wire.set('mitra_max_distance_km', radius)"
                                    value="{{ $mitra_max_distance_km ?? 10 }}"
                                    class="w-full text-xl sm:text-2xl font-black text-slate-900 bg-transparent outline-none border-none focus:ring-0 p-0" />
                                <span class="bg-indigo-600 text-white font-extrabold text-xs px-3 py-1.5 rounded-xl uppercase tracking-wider shadow-xs select-none">KM</span>
                            </div>
                            @error('mitra_max_distance_km')
                                <div class="flex items-center gap-2 mt-2 text-rose-600 text-xs font-semibold">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Range Slider with Markers -->
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/70">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                                <span>1 KM</span>
                                <span class="px-2.5 py-0.5 bg-indigo-600 text-white rounded-md text-[11px] font-extrabold shadow-2xs" x-text="(radius || 10) + ' KM'"></span>
                                <span>100 KM</span>
                            </div>
                            <input type="range" min="1" max="100" step="0.5" 
                                x-model.number="radius"
                                x-on:input.debounce.150ms="$wire.set('mitra_max_distance_km', radius)"
                                class="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600" />
                            <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 px-0.5">
                                <span>Dekat</span>
                                <span>Standar (10 KM)</span>
                                <span>Antar Kota (50 KM)</span>
                                <span>Maksimal</span>
                            </div>
                        </div>

                        <!-- Quick Preset Pill Buttons -->
                        <div>
                            <span class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2.5">Pilihan Cepat Radius:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach([5, 10, 15, 20, 25, 30, 50] as $preset)
                                    <button type="button" 
                                        @click="setRadius({{ $preset }})"
                                        :class="radius == {{ $preset }} ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-200 shadow-sm' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs'"
                                        class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer flex items-center gap-1.5">
                                        <span>{{ $preset }} KM</span>
                                        @if($preset === 10)
                                            <span :class="radius == {{ $preset }} ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-500'" class="text-[10px] px-1.5 py-0.5 rounded-md font-bold">Default</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Visual Geofencing & Simulation Preview (5 cols) -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-indigo-50/90 via-slate-50 to-blue-50/70 border border-indigo-100 rounded-2xl p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4 text-indigo-900">
                                <div class="w-8 h-8 rounded-xl bg-indigo-600/10 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-bold uppercase tracking-wider">Simulasi Jangkauan Mitra</h3>
                            </div>

                            <!-- Live Radar Pulse Graphic -->
                            <div class="py-4 flex flex-col items-center justify-center text-center">
                                <div class="relative w-28 h-28 flex items-center justify-center mb-3">
                                    <div class="absolute inset-0 rounded-full bg-indigo-200/60 animate-ping opacity-40"></div>
                                    <div class="absolute w-24 h-24 rounded-full bg-indigo-100/80 border border-indigo-300"></div>
                                    <div class="absolute w-16 h-16 rounded-full bg-indigo-200 border border-indigo-400"></div>
                                    <div class="relative w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.516" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white rounded-full border border-indigo-200 shadow-2xs mb-2">
                                    <span class="text-xs font-black text-indigo-700" x-text="'Radius: ' + (radius || 10) + ' KM'"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 max-w-xs leading-relaxed">
                                    Mitra dapat mendeteksi order customer yang berjarak hingga <strong class="text-slate-800" x-text="(radius || 10) + ' km'"></strong> dari lokasinya.
                                </p>
                            </div>

                            <!-- Rules Checklist -->
                            <div class="space-y-2 pt-2 border-t border-indigo-100/80 text-xs text-slate-600">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Tampil di dashboard mitra terdekat</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Mitra diperbolehkan mengambil order</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Di luar radius otomatis disembunyikan</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-indigo-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span class="font-medium">Respons Cepat (SLA)</span>
                            <span class="text-indigo-600 font-bold">Terjamin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Fitur Komunikasi: Visibilitas Tombol WhatsApp -->
        <div class="bg-white rounded-2xl shadow-xs border border-emerald-200/90 overflow-hidden mb-8">
            <div class="border-b border-emerald-100 px-5 sm:px-8 py-5 bg-gradient-to-r from-emerald-50/80 via-white to-gray-50/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/25">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Visibilitas Tombol WhatsApp</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Atur apakah tombol kontak WhatsApp ditampilkan atau disembunyikan pada detail bantuan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $show_whatsapp_button ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }} text-xs font-bold rounded-xl border shadow-2xs">
                        <span class="w-2 h-2 rounded-full {{ $show_whatsapp_button ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ $show_whatsapp_button ? 'Status: Ditampilkan' : 'Status: Disembunyikan (Hide)' }}
                    </span>
                </div>
            </div>

            <div class="p-5 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border {{ $show_whatsapp_button ? 'bg-emerald-50/30 border-emerald-200' : 'bg-gray-50/50 border-gray-200' }} transition">
                    <div class="max-w-xl">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-gray-900">Tombol WhatsApp di Halaman Detail Pesanan</h4>
                            @if(!$show_whatsapp_button)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Disembunyikan</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Aktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Jika <strong>disembunyikan</strong>, tombol WhatsApp pada rincian bantuan (sisi mitra & customer) tidak akan muncul, sehingga seluruh komunikasi transaksi terpusat aman melalui fitur <strong>Chat Aplikasi</strong> bawaan. Jika <strong>ditampilkan</strong>, tombol WhatsApp akan muncul kembali seperti biasa.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="show_whatsapp_button" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#0066FF] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky / Elegant Bottom Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-xs text-slate-500 text-center sm:text-left">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 hidden sm:flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span>Perubahan yang disimpan akan langsung aktif di seluruh aplikasi Mitra dan Customer secara real-time.</span>
            </div>

            <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/25 hover:shadow-lg transition-all duration-200 cursor-pointer">
                <svg wire:loading.remove wire:target="save" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg wire:loading wire:target="save" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Simpan Perubahan Tarif & Radius</span>
                <span wire:loading wire:target="save">Menyimpan Perubahan...</span>
            </button>
        </div>
    </form>

    <!-- Saved Confirmation Modal -->
    <div id="settingsSavedModal" class="fixed inset-0 z-50 flex items-center justify-center hidden transition-opacity duration-300">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0 border border-slate-100" id="settingsSavedContent">
            <button id="settingsSavedClose" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm animate-bounce-once">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Berhasil Disimpan!</h3>
                <p class="text-xs text-slate-600 leading-relaxed" id="settingsSavedMessage">
                    Tarif dan radius jangkauan mitra berhasil diperbarui.
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-once {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        .animate-bounce-once {
            animation: bounce-once 0.4s ease-in-out;
        }
        #settingsSavedModal:not(.hidden) #settingsSavedContent {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</div>

<script>
    let modalTimeout = null;

    function showSettingsSaved(message) {
        const modal = document.getElementById('settingsSavedModal');
        const msgEl = document.getElementById('settingsSavedMessage');
        if (!modal) return;
        if (!message) return;

        if (msgEl) msgEl.textContent = message;

        if (modalTimeout) {
            clearTimeout(modalTimeout);
            modalTimeout = null;
        }

        modal.classList.remove('hidden');

        modalTimeout = setTimeout(() => {
            modal.classList.add('hidden');
            modalTimeout = null;
        }, 3000);
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('settingsSaved', (event) => {
            const message = event[0]?.message || event.message || 'Pengaturan berhasil disimpan';
            showSettingsSaved(message);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('settingsSavedClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                if (modalTimeout) {
                    clearTimeout(modalTimeout);
                    modalTimeout = null;
                }
                const modal = document.getElementById('settingsSavedModal');
                if (modal) modal.classList.add('hidden');
            });
        }
    });
</script>
