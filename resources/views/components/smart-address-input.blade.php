@props([
    'id' => 'smart-address-' . uniqid(),
    'name' => null,
    'placeholder' => 'Contoh: Jl. Merdeka No. 123, Kelurahan, Kecamatan...',
    'rows' => 3,
    'cityRef' => null,
    'required' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<div 
    x-data="smartAddressComponent({
        wireModel: '{{ $wireModel }}',
        cityRef: '{{ $cityRef }}',
        initialQuery: @js(old($name, $attributes->get('value', '')))
    })"
    class="relative w-full"
    @click.outside="isOpen = false"
>
    <!-- Textarea / Input wrapper -->
    <div class="relative">
        <textarea
            x-ref="input"
            id="{{ $id }}"
            @if($name) name="{{ $name }}" @endif
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            x-model="query"
            @input.debounce.300ms="search()"
            @keydown.arrow-down.prevent="navigateSuggestions(1)"
            @keydown.arrow-up.prevent="navigateSuggestions(-1)"
            @keydown.enter="handleEnter($event)"
            @keydown.escape="isOpen = false"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-3 text-sm rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition resize-none bg-white text-gray-800 placeholder-gray-400 pr-12 shadow-xs'
            ]) }}
        ></textarea>

        <!-- Right Side Icons: Loading & Clear Button -->
        <div class="absolute right-3 top-3 flex items-center space-x-1.5 z-10 pointer-events-auto">
            <!-- Loading Spinner -->
            <div x-show="isLoading" x-cloak class="text-blue-500 animate-spin">
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
                title="Hapus / Reset"
                class="text-gray-400 hover:text-gray-600 transition p-1 rounded-md hover:bg-gray-100 focus:outline-none"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
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
        class="absolute left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-xl border border-blue-100/80 overflow-hidden z-50 divide-y divide-gray-100 max-h-72 overflow-y-auto"
        style="backdrop-filter: blur(8px);"
    >
        <!-- Dropdown Header -->
        <div class="px-3.5 py-2 bg-gradient-to-r from-blue-50/70 to-indigo-50/50 flex items-center justify-between text-xs text-blue-800 font-semibold border-b border-blue-100">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                Rekomendasi Alamat Cerdas
            </span>
            <span class="text-[10px] text-blue-500 font-normal">Pilih salah satu</span>
        </div>

        <!-- Suggestion Items -->
        <template x-for="(item, idx) in suggestions" :key="idx">
            <div 
                @click="select(item)"
                @mouseenter="highlightedIndex = idx"
                class="px-4 py-3 flex items-start gap-3 cursor-pointer transition-colors duration-150 text-left"
                :class="highlightedIndex === idx ? 'bg-blue-50/90 text-blue-950 border-l-4 border-blue-500 pl-3' : 'hover:bg-gray-50 text-gray-800'"
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
                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5 leading-snug" x-text="item.subtitle"></p>
                </div>

                <!-- Select Action Arrow -->
                <div class="text-blue-500 self-center flex-shrink-0 opacity-0 group-hover:opacity-100" :class="highlightedIndex === idx ? '!opacity-100' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </template>
    </div>
</div>

@once
<script>
    function smartAddressComponent(config) {
        return {
            query: '',
            suggestions: [],
            isLoading: false,
            isOpen: false,
            highlightedIndex: -1,
            abortController: null,

            init() {
                // Initialize query from Livewire property or input value
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.query = this.$wire.get(config.wireModel) || '';
                    this.$watch('$wire.' + config.wireModel, (newVal) => {
                        if (newVal !== this.query) {
                            this.query = newVal || '';
                        }
                    });
                } else if (config.initialQuery) {
                    this.query = config.initialQuery;
                } else if (this.$refs.input) {
                    this.query = this.$refs.input.value || '';
                }
            },

            async search() {
                const trimmed = (this.query || '').trim();
                
                // Keep Livewire model synchronized while typing
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.$wire.set(config.wireModel, this.query);
                }

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
                            cityParam = cityEl.options ? (cityEl.options[cityEl.selectedIndex]?.text || '') : cityEl.value;
                            // Clean up '(Province)' suffix if present
                            cityParam = cityParam.replace(/\(.*\)/, '').replace(/Kota|Kabupaten/i, '').trim();
                        }
                    }

                    const url = new URL('/api/address/autocomplete', window.location.origin);
                    url.searchParams.set('q', trimmed);
                    if (cityParam) {
                        url.searchParams.set('city', cityParam);
                    }

                    const response = await fetch(url.toString(), {
                        signal: this.abortController.signal,
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.suggestions = Array.isArray(data) ? data : [];
                        this.isOpen = this.suggestions.length > 0;
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.warn('Smart address error:', err);
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
                this.query = item.full_address;

                // Sync with Livewire
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.$wire.set(config.wireModel, item.full_address);
                }

                // Trigger standard events for forms
                if (this.$refs.input) {
                    this.$refs.input.value = item.full_address;
                    this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                this.isOpen = false;
                this.suggestions = [];

                // Broadcast custom event for Leaflet maps, city selects, etc.
                window.dispatchEvent(new CustomEvent('smart-address-selected', {
                    detail: item
                }));
            },

            clear() {
                this.query = '';
                if (config.wireModel && typeof this.$wire !== 'undefined') {
                    this.$wire.set(config.wireModel, '');
                }
                if (this.$refs.input) {
                    this.$refs.input.value = '';
                    this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                    this.$refs.input.focus();
                }
                this.isOpen = false;
                this.suggestions = [];
            }
        };
    }
</script>
@endonce
