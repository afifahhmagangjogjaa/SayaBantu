@php
    $title = 'Manajemen Kota';
    $breadcrumb = 'Super Admin / Manajemen Kota';
@endphp

<div>
    <!-- Header Summary Card & Action -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-3 sm:px-5 sm:py-3.5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50/80 border border-blue-100/80 flex items-center justify-center text-blue-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-base sm:text-lg font-bold text-gray-900">
                Jumlah kota saat ini: <span class="text-blue-600 font-extrabold ml-1">{{ $cities->total() }}</span>
            </span>
        </div>
        <button wire:click="openCreateModal"
            class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition shadow-sm hover:shadow flex items-center justify-center space-x-2 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kota</span>
        </button>
    </div>

    <div>

        <!-- Filter Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5 mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
                <!-- Search Input with inline Icon -->
                <div class="relative w-full md:flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" style="padding-left: 14px;">
                        <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Cari nama kota atau provinsi..."
                        style="padding-left: 44px; padding-right: 16px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                        class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <!-- Per Page Filter -->
                    <div class="relative min-w-[120px] w-full md:w-auto">
                        <select wire:model.live="perPage"
                            style="padding-left: 14px; padding-right: 36px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                            class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-800 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <option value="10">10 Data</option>
                            <option value="25">25 Data</option>
                            <option value="50">50 Data</option>
                            <option value="100">100 Data</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kota</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Provinsi</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jumlah User</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Dibuat</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($cities as $city)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500">
                                    {{ $cities->firstItem() + $loop->index }}
                                </td>
                                <td class="px-2.5 py-3">
                                    <div class="flex items-center gap-3" style="gap: 12px;">
                                        <div class="w-8 h-8 flex-shrink-0 bg-primary-50 border border-primary-200 rounded-full flex items-center justify-center" style="width: 32px; height: 32px; min-width: 32px;">
                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0" style="margin-left: 4px;">
                                            <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[180px]" title="{{ $city->name }}">{{ $city->name }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                                <span>@if(!empty($loadDistricts) && $city->relationLoaded('districts')) {{ $city->districts->count() }} kecamatan @else - kecamatan @endif</span>
                                                @if($city->latitude && $city->longitude)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-emerald-600 font-mono text-[10px]" title="Koordinat: {{ $city->latitude }}, {{ $city->longitude }}">📍 {{ round((float)$city->latitude, 4) }}, {{ round((float)$city->longitude, 4) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-700">
                                    {{ $city->province }}
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ number_format($city->users_count) }} User
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full {{ $city->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $city->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $city->created_at->format('d M Y') }}
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5 leading-none">
                                        {{-- Detail --}}
                                        <button wire:click="openDetailModal({{ $city->id }})"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Detail Kota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        {{-- Edit --}}
                                        <button wire:click="editCity({{ $city->id }})"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                            title="Edit Kota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- Toggle Status ON/OFF --}}
                                        <button type="button"
                                            x-data="{ on: {{ $city->is_active ? 'true' : 'false' }} }"
                                            @click="on = !on; $wire.toggleStatus({{ $city->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleStatus({{ $city->id }})"
                                            class="inline-flex items-center gap-1.5 cursor-pointer focus:outline-none"
                                            title="{{ $city->is_active ? 'Aktif — Klik untuk nonaktifkan' : 'Nonaktif — Klik untuk aktifkan' }}">
                                            <span class="inline-flex items-center h-5 w-9 p-0.5 rounded-full transition-colors duration-200 ease-in-out"
                                                :class="on ? 'bg-emerald-500 justify-end' : 'bg-gray-300 justify-start'">
                                                <span class="h-4 w-4 rounded-full bg-white shadow ring-0"></span>
                                            </span>
                                        </button>
                                        {{-- Hapus --}}
                                        <button wire:click="confirmDelete({{ $city->id }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Kota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- District rows grouped under the city --}}
                            @if(!empty($loadDistricts) && $city->relationLoaded('districts') && $city->districts->isNotEmpty())
                                @foreach($city->districts as $district)
                                    <tr class="bg-gray-50/60 text-xs">
                                        <td></td>
                                        <td class="px-2.5 py-2 text-gray-700">&nbsp;&nbsp;&mdash; {{ $district->name }}</td>
                                        <td class="px-2.5 py-2 text-gray-500">Kecamatan</td>
                                        <td class="px-2.5 py-2 text-gray-500">&nbsp;</td>
                                        <td class="px-2.5 py-2">@if($district->is_active)<span class="text-green-600 font-semibold">Aktif</span>@else<span class="text-red-600 font-semibold">Nonaktif</span>@endif</td>
                                        <td class="px-2.5 py-2 text-gray-400">{{ optional($district->created_at)->format('d M Y') }}</td>
                                        <td class="px-2.5 py-2 text-center text-gray-500">&nbsp;</td>
                                    </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada data kota</p>
                                        <p class="text-gray-400 text-xs mt-0.5">Tambah kota baru untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($cities->hasPages())
                <div class="bg-gray-50 px-6 py-5 border-t border-gray-200">
                    {{ $cities->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create / Edit Modal (polished like Users modal) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $editMode ? 'Edit Kota' : 'Tambah Kota Baru' }}</h3>
                        <p class="text-sm text-white/90">
                            {{ $editMode ? 'Perbarui data kota dengan hati-hati' : 'Lengkapi formulir untuk menambah kota layanan' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="text-white/90 hover:text-white p-2 rounded-md" aria-label="Tutup modal">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal body (form) -->
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Provinsi <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="selectedProvinceCode"
                                    class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($apiProvinces as $p)
                                        <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('province') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700">Nama Kota / Kabupaten <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="selectedCityCode"
                                    class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    {{ empty($apiCities) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kota --</option>
                                    @foreach($apiCities as $c)
                                        <option value="{{ $c['code'] }}">{{ $c['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700">Admin Kota <span class="text-red-500">*</span></label>
                                <select wire:model="admin_id"
                                    class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">-- Tidak ada --</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
                                    @endforeach
                                </select>
                                @error('admin_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>

                            <!-- Koordinat Latitude & Longitude (Auto-fill) -->
                            <div class="bg-gray-50/80 p-3.5 rounded-xl border border-gray-200/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Koordinat Wilayah (Peta)
                                    </label>
                                    <span class="text-[11px] text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        Otomatis Terisi
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[11px] font-medium text-gray-500">Latitude</label>
                                        <input type="text" wire:model="latitude" placeholder="-7.5755000"
                                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                                        @error('latitude') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-medium text-gray-500">Longitude</label>
                                        <input type="text" wire:model="longitude" placeholder="110.8243000"
                                            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                                        @error('longitude') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Terisi otomatis saat memilih kota, atau dapat Anda sesuaikan titiknya secara manual.
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-2">Status Layanan <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-3">
                                    <button type="button" 
                                        wire:click="$set('is_active', {{ $is_active ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-sm font-semibold {{ $is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                                        {{ $is_active ? 'ON (Aktif)' : 'OFF (Nonaktif)' }}
                                    </span>
                                </div>
                                @error('is_active') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Footer Actions inside form so submit works with enter -->
                        <div class="pt-2 border-t flex items-center justify-end gap-3">
                            <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg flex items-center gap-2 text-sm"
                                wire:loading.attr="disabled">
                                <svg wire:loading class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                        fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>
                                </svg>
                                <span>{{ $editMode ? 'Perbarui Kota' : 'Simpan Kota' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Province Modals --}}
    @if($showProvinceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $provinceEditId ? 'Edit Provinsi' : 'Tambah Provinsi' }}</h3>
                        <p class="text-sm text-white/90">{{ $provinceEditId ? 'Perbarui nama provinsi' : 'Masukkan nama provinsi baru' }}</p>
                    </div>
                    <div>
                        <button type="button" wire:click="$set('showProvinceModal', false)" class="text-white/90 p-2">&times;</button>
                    </div>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="saveProvince">
                        <div>
                            <label class="text-xs font-medium text-gray-700">Nama Provinsi</label>
                            <input type="text" wire:model="provinceName" class="w-full mt-2 px-4 py-3 border rounded-lg" />
                            @error('provinceName') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" wire:click="$set('showProvinceModal', false)" class="px-4 py-2 bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showProvinceDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Konfirmasi Hapus Provinsi</h3>
                        <p class="text-sm text-gray-600 mt-1">Anda yakin ingin menghapus provinsi ini? Semua relasi provinsi akan dilepas dari kota.</p>
                    </div>
                    <div>
                        <button type="button" wire:click="$set('showProvinceDeleteModal', false)" class="text-gray-400 hover:text-gray-700 text-xl">&times;</button>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="text-sm text-gray-700">Provinsi yang akan dihapus:</div>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg text-gray-900 font-medium">{{ $deletingProvinceName ?? '-' }}</div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showProvinceDeleteModal', false)" class="px-4 py-2 bg-gray-100 rounded-lg">Batal</button>
                    <button type="button" wire:click.prevent="deleteProvince" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal (polished) -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-600 mt-1">Anda yakin ingin menghapus kota ini? Aksi ini tidak dapat
                            dibatalkan.</p>
                    </div>
                    <div>
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="text-gray-400 hover:text-gray-700 text-xl">&times;</button>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="text-sm text-gray-700">Kota yang akan dihapus:</div>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg text-gray-900 font-medium">{{ $deletingCityName ?? '-' }}
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 bg-gray-100 rounded-lg">Batal</button>
                    <button type="button" wire:click.prevent="deleteCity"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal (per-city stats) --}}
    @if($showDetailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data="{
                 initChart() {
                     if (typeof Chart === 'undefined') {
                         console.error('Chart.js not loaded');
                         return;
                     }
                     
                     const labels = JSON.parse($refs.chartData.getAttribute('data-labels') || '[]');
                     const customers = JSON.parse($refs.chartData.getAttribute('data-customers') || '[]');
                     const mitras = JSON.parse($refs.chartData.getAttribute('data-mitras') || '[]');
                     
                     const ctx = $refs.canvas.getContext('2d');
                     new Chart(ctx, {
                         type: 'line',
                         data: {
                             labels: labels,
                             datasets: [
                                 { label: 'Customer (Aktif)', data: customers, borderColor: '#0ea5e9', backgroundColor: 'rgba(14,165,233,0.08)', tension: 0.3, fill: true },
                                 { label: 'Mitra (Aktif)', data: mitras, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.08)', tension: 0.3, fill: true }
                             ]
                         },
                         options: { responsive: true, maintainAspectRatio: false }
                     });
                 }
             }"
             x-init="setTimeout(() => initChart(), 100)">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <div>
                        <h3 class="text-xl font-bold text-white">Detail Kota: {{ $detailCityName }}</h3>
                        <p class="text-sm text-white/90">Grafik jumlah pengguna aktif per hari (Customer & Mitra) — 30 hari
                            terakhir</p>
                    </div>
                    <div>
                        <button type="button" wire:click.prevent="closeDetailModal"
                            class="text-white/90 hover:text-white p-2 rounded-md">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div wire:ignore>
                        <canvas x-ref="canvas" height="160"></canvas>
                    </div>
                    <div x-ref="chartData" class="hidden" data-labels='@json($chartLabels)'
                        data-customers='@json($chartCustomerData)' data-mitras='@json($chartMitraData)'></div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" wire:click.prevent="closeDetailModal"
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</div>