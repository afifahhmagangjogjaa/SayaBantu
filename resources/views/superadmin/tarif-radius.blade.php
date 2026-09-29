@php
    $title = 'Tarif & Radius Bantuan';
    $breadcrumb = 'Super Admin / Pengaturan / Bantuan / Tarif & Radius';
@endphp

<div>
    <form wire:submit.prevent="save">
        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        <div id="settingsFlash" data-message="{{ session('message') ?? '' }}" style="display:none"></div>

        <!-- 1. Konfigurasi Tarif Bantuan Standar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-blue-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-medium shadow-sm" style="background-color: #2563eb; color: #ffffff;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-medium text-gray-900">Konfigurasi Tarif Bantuan Terjadwal</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Batas nominal minimal komisi untuk pembuatan order bantuan reguler</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-lg border border-blue-200 hidden sm:inline-block">Tarif Standar</span>
            </div>

            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Nominal Minimal Bantuan Standar</label>
                    <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500; margin-right: 8px; user-select: none;">Rp</span>
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
                            style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                    </div>
                    @error('min_help_nominal')
                        <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">Nominal terendah yang dapat diajukan customer saat memesan bantuan biasa/standar.</p>
                </div>
            </div>
        </div>

        <!-- 2. Konfigurasi Bantuan Mendesak (Urgent) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-amber-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-medium shadow-sm" style="background-color: #f59e0b; color: #ffffff;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-medium text-gray-900">Konfigurasi Bantuan Mendesak (Urgent)</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Penetapan tarif awal minimal khusus untuk order bantuan berlabel darurat / urgent</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded-lg border border-amber-200 hidden sm:inline-block">Mode Urgent</span>
            </div>

            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Nominal Minimal Bantuan Mendesak</label>
                    <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                        <span style="color: #64748b; font-size: 14px; font-weight: 500; margin-right: 8px; user-select: none;">Rp</span>
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
                            style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                    </div>
                    @error('default_urgent_nominal')
                        <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">Batas tarif terendah saat customer mencentang opsi "Mendesak / Urgent". Nominal bantuan darurat tidak dapat dibuat di bawah nilai ini.</p>
                </div>
            </div>
        </div>

        <!-- 3. Konfigurasi Jarak & Radius Mitra -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-indigo-50/60 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-medium shadow-sm" style="background-color: #4f46e5; color: #ffffff;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-medium text-gray-900">Jangkauan & Radius Mitra</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Batas jarak maksimal antara posisi mitra dengan lokasi customer untuk deteksi order bantuan</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-lg border border-indigo-200 hidden sm:inline-block">GPS & Geofencing</span>
            </div>

            <div class="p-4 sm:p-8">
                <div class="max-w-xl">
                    <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Radius Maksimal Penjangkauan Mitra</label>
                    <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                        <input type="number" step="0.5" min="1" max="100" wire:model="mitra_max_distance_km"
                            style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                        <span style="color: #64748b; font-size: 14px; font-weight: 500; margin-left: 8px; user-select: none;">KM</span>
                    </div>
                    @error('mitra_max_distance_km')
                        <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2">Mitra di luar radius ini tidak akan melihat atau menerima tawaran order bantuan untuk menjamin kecepatan penanganan.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2 flex justify-end">
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium shadow-md shadow-primary-500/20 hover:shadow-lg transition-all duration-200 w-full sm:w-auto cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>
                Simpan Perubahan Tarif & Radius
            </button>
        </div>
    </form>

    <!-- Saved confirmation modal -->
    <div id="settingsSavedModal" class="fixed inset-0 z-50 flex items-center justify-center hidden transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="settingsSavedContent">
            <button id="settingsSavedClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 animate-bounce-once">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Berhasil Disimpan!</h3>
                <p class="text-sm text-gray-600 leading-relaxed" id="settingsSavedMessage">
                    Perubahan telah diterapkan dan tersimpan.
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-once {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .animate-bounce-once {
            animation: bounce-once 0.5s ease-in-out;
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

        modal.classList.add('hidden');

        setTimeout(() => {
            modal.classList.remove('hidden');
            modalTimeout = setTimeout(() => {
                modal.classList.add('hidden');
                modalTimeout = null;
            }, 3000);
        }, 50);
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