@props([
    'id' => 'smart-location-' . uniqid(),
    'name' => null,
    'placeholder' => 'Ketik patokan / nama tempat / jalan...',
    'cityRef' => null,
    'required' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<div 
    x-data="smartLocationComponent({
        wireModel: '{{ $wireModel }}',
        cityRef: '{{ $cityRef }}',
        initialQuery: @js(old($name, $attributes->get('value', '')))
    })"
    class="relative w-full"
    @click.outside="isOpen = false"
>
    <!-- Input Wrapper -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
        </div>

        <input
            type="text"
            x-ref="input"
            id="{{ $id }}"
            @if($name) name="{{ $name }}" @endif
            placeholder="{{ $placeholder }}"
            x-model="query"
            @focus="onFocus()"
            @input.debounce.250ms="search()"
            @keydown.arrow-down.prevent="navigateSuggestions(1)"
            @keydown.arrow-up.prevent="navigateSuggestions(-1)"
            @keydown.enter="handleEnter($event)"
            @keydown.escape="isOpen = false"
            autocomplete="off"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full pl-10 pr-12 py-3 text-sm font-medium rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs'
            ]) }}
        />

        <!-- Right Side Icons: Loading & Clear Button -->
        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center space-x-1.5 z-10 pointer-events-auto">
            <!-- Loading Spinner -->
            <div x-show="isLoading" x-cloak class="text-blue-500 animate-spin p-1">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Clear button -->
            <button 
                type="button" 
                x-show="query.length > 0 && !isLoading" 
                @click="clear()"
                title="Hapus"
                class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100 focus:outline-none"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Quick Chips / Rekomendasi Format Patokan Cepat -->
    <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-[11px]">
        <span class="text-gray-400 text-[10px] font-medium flex items-center gap-0.5">
            <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14H8a4 4 0 01-.82-7.915A4.002 4.002 0 0115 10a3.992 3.992 0 01-3 4z" />
            </svg>
            Contoh:
        </span>
        <button type="button" @click="insertPrefix('Depan ')" 
            class="px-2 py-0.5 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition cursor-pointer font-medium">
            🏢 Depan...
        </button>
        <button type="button" @click="insertPrefix('Dekat ')" 
            class="px-2 py-0.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition cursor-pointer font-medium">
            🏪 Dekat...
        </button>
        <button type="button" @click="insertPrefix('Seberang ')" 
            class="px-2 py-0.5 rounded-full bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 transition cursor-pointer font-medium">
            📍 Seberang...
        </button>
        <button type="button" @click="insertPrefix('Masuk Gang ')" 
            class="px-2 py-0.5 rounded-full bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition cursor-pointer font-medium">
            🚪 Masuk Gang...
        </button>
        <template x-if="lastSmartAddressTitle">
            <button type="button" @click="useSmartAddressTitle()" 
                class="px-2 py-0.5 rounded-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition cursor-pointer font-medium flex items-center gap-1 animate-pulse">
                <span>📋 Pakai:</span>
                <span class="font-bold truncate max-w-[120px]" x-text="lastSmartAddressTitle"></span>
            </button>
        </template>
    </div>

    <!-- Autocomplete Dropdown List -->
    <div 
        x-show="isOpen && suggestions.length > 0" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        x-cloak
        class="absolute left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-2xl border border-blue-100/90 overflow-hidden z-50 divide-y divide-gray-100 max-h-72 overflow-y-auto"
        style="backdrop-filter: blur(10px);"
    >
        <!-- Dropdown Header -->
        <div class="px-3.5 py-2.5 bg-gradient-to-r from-blue-50/80 to-cyan-50/60 flex items-center justify-between text-xs text-blue-900 font-semibold border-b border-blue-100/80">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                Rekomendasi Patokan & Tempat Terdekat
            </span>
            <span class="text-[10px] text-blue-600/90 bg-blue-100/70 px-2 py-0.5 rounded-full font-medium">Pilih patokan</span>
        </div>

        <!-- Suggestion Items -->
        <template x-for="(item, idx) in suggestions" :key="idx">
            <div 
                @mousedown.prevent="select(item)"
                @click.prevent="select(item)"
                @mouseenter="highlightedIndex = idx"
                class="px-4 py-2.5 flex items-start gap-3 cursor-pointer transition-colors duration-150 text-left"
                :class="highlightedIndex === idx ? 'bg-blue-50/90 text-blue-950 border-l-4 border-blue-500 pl-3' : 'hover:bg-gray-50/80 text-gray-800'"
            >
                <!-- Location Pin Icon -->
                <div 
                    class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors"
                    :class="highlightedIndex === idx ? 'bg-blue-500 text-white shadow-xs' : 'bg-blue-100/60 text-blue-600'"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate" x-text="item.title"></p>
                    <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5 leading-snug" x-text="item.subtitle"></p>
                </div>

                <!-- Action Badge -->
                <div class="self-center flex-shrink-0">
                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                        Pilih
                    </span>
                </div>
            </div>
        </template>
    </div>
</div>

@once
<script>
    function smartLocationComponent(config) {
        return {
            query: '',
            suggestions: [],
            isLoading: false,
            isOpen: false,
            isSelecting: false,
            highlightedIndex: -1,
            abortController: null,
            lastSmartAddressTitle: '',

            init() {
                // Initialize query from Livewire property or input value
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.query = this.$wire.get(config.wireModel) || '';
                    this.$watch('$wire.' + config.wireModel, (newVal) => {
                        if (newVal !== this.query && !this.isSelecting) {
                            this.query = newVal || '';
                        }
                    });
                } else if (config.initialQuery) {
                    this.query = config.initialQuery;
                } else if (this.$refs.input) {
                    this.query = this.$refs.input.value || '';
                }

                // Dengarkan jika user memilih Alamat Lengkap dari smart-address-input
                window.addEventListener('smart-address-selected', (e) => {
                    const item = e.detail;
                    if (item && item.title) {
                        this.lastSmartAddressTitle = item.title;
                        // Jika location masih kosong, otomatis isi dengan patokan alamat tersebut
                        if (!this.query || this.query.trim() === '') {
                            this.applyLocation(item.title);
                        }
                    }
                });
            },

            onFocus() {
                if (!this.isSelecting && this.suggestions.length > 0) {
                    this.isOpen = true;
                }
            },

            insertPrefix(prefix) {
                if (!this.query.toLowerCase().startsWith(prefix.toLowerCase())) {
                    this.query = prefix + this.query.replace(/^(Depan|Dekat|Seberang|Masuk Gang)\s*/i, '');
                }
                this.syncValue(this.query);
                if (this.$refs.input) {
                    this.$refs.input.focus();
                }
            },

            useSmartAddressTitle() {
                if (this.lastSmartAddressTitle) {
                    this.applyLocation('Dekat ' + this.lastSmartAddressTitle);
                }
            },

            async search() {
                if (this.isSelecting) {
                    this.isOpen = false;
                    this.suggestions = [];
                    return;
                }

                const trimmed = (this.query || '').trim();
                
                // Keep Livewire model synchronized while typing
                this.syncValue(this.query);

                if (trimmed.length < 3) {
                    this.suggestions = [];
                    this.isOpen = false;
                    return;
                }

                if (this.abortController) {
                    this.abortController.abort();
                }
                this.abortController = new AbortController();

                this.isLoading = true;
                this.highlightedIndex = -1;

                try {
                    let cityParam = '';
                    if (config.cityRef) {
                        const cityEl = document.querySelector(config.cityRef);
                        if (cityEl) {
                            const val = cityEl.value ? cityEl.value.trim() : '';
                            const txt = cityEl.options ? (cityEl.options[cityEl.selectedIndex]?.text || '') : (cityEl.value || '');
                            if (val && !txt.includes('--') && !txt.toLowerCase().includes('pilih') && !txt.toLowerCase().includes('memuat')) {
                                cityParam = txt.replace(/\(.*\)/, '').replace(/Kota|Kabupaten/i, '').trim();
                            }
                        }
                    }

                    // Bersihkan prefix seperti 'Depan ', 'Dekat ' saat search agar query akurat
                    const cleanQuery = trimmed.replace(/^(Depan|Dekat|Seberang|Masuk Gang)\s*/i, '').trim();
                    const searchQuery = cleanQuery.length >= 2 ? cleanQuery : trimmed;

                    const url = new URL('/api/address/autocomplete', window.location.origin);
                    url.searchParams.set('q', searchQuery);
                    if (cityParam) {
                        url.searchParams.set('city', cityParam);
                    }

                    const response = await fetch(url.toString(), {
                        signal: this.abortController.signal,
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (!this.isSelecting) {
                            this.suggestions = Array.isArray(data) ? data : [];
                            this.isOpen = this.suggestions.length > 0;
                        }
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.warn('Smart location error:', err);
                    }
                } finally {
                    this.isLoading = false;
                }
            },

            navigateSuggestions(direction) {
                if (!this.isOpen || this.suggestions.length === 0) return;
                this.highlightedIndex += direction;
                if (this.highlightedIndex < 0) {
                    this.highlightedIndex = this.suggestions.length - 1;
                } else if (this.highlightedIndex >= this.suggestions.length) {
                    this.highlightedIndex = 0;
                }
            },

            handleEnter(event) {
                if (this.isOpen && this.highlightedIndex >= 0 && this.suggestions[this.highlightedIndex]) {
                    event.preventDefault();
                    this.select(this.suggestions[this.highlightedIndex]);
                }
            },

            select(item) {
                if (this.abortController) {
                    this.abortController.abort();
                }

                // Buat deskripsi lokasi yang ramah patokan
                let locText = item.title;
                if (item.subtitle && !item.title.toLowerCase().includes(item.subtitle.toLowerCase())) {
                    locText += ', ' + item.subtitle.split(',')[0].trim();
                }

                this.applyLocation(locText);

                // Broadcast custom event agar peta ikut terpusat ke lokasi patokan
                window.dispatchEvent(new CustomEvent('smart-location-selected', {
                    detail: item
                }));
            },

            applyLocation(text) {
                if (this.abortController) {
                    this.abortController.abort();
                }
                this.isSelecting = true;
                this.isOpen = false;
                this.suggestions = [];
                this.query = text;
                this.syncValue(text);

                if (this.$refs.input) {
                    this.$refs.input.value = text;
                    this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                setTimeout(() => {
                    this.isSelecting = false;
                }, 500);
            },

            syncValue(val) {
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.$wire.set(config.wireModel, val);
                }
            },

            clear() {
                this.applyLocation('');
                this.isOpen = false;
                this.suggestions = [];
                if (this.$refs.input) {
                    this.$refs.input.focus();
                }
            }
        };
    }
</script>
@endonce
