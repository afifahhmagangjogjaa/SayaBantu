@props([
    'cities' => [],
    'model' => null,
    'name' => 'city_id',
    'id' => null,
    'value' => null,
    'label' => 'Kota / Kabupaten',
    'required' => false,
    'showAsterisk' => false,
    'placeholder' => 'Cari kota atau kabupaten...',
    'error' => null,
    'syncProvince' => false,
])

@php
    $wireModel = $model ?? ($attributes->wire('model')->value() ?: null);
    $cityList = collect($cities)->map(function($c) {
        return [
            'id' => is_array($c) ? $c['id'] : $c->id,
            'name' => is_array($c) ? $c['name'] : $c->name,
            'province' => is_array($c) ? ($c['province'] ?? '') : ($c->province ?? ''),
        ];
    })->values();
    $initialValue = $value ?? old($name) ?? null;
@endphp

<div x-data="{
    open: false,
    search: '',
    selectedId: @if($wireModel) @entangle($wireModel).live @else @json($initialValue) @endif,
    selectedName: '',
    syncProvince: {{ $syncProvince ? 'true' : 'false' }},
    cities: {{ $cityList->toJson() }},
    init() {
        this.updateName();
        this.$watch('selectedId', () => {
            this.updateName();
            if (this.$refs.hiddenInput) {
                this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    },
    updateName() {
        const found = this.cities.find(c => String(c.id) === String(this.selectedId));
        this.selectedName = found ? `${found.name}${found.province ? ' (' + found.province + ')' : ''}` : '';
    },
    get filteredCities() {
        if (!this.search.trim()) return this.cities;
        const q = this.search.toLowerCase().trim();
        return this.cities.filter(c => 
            (c.name && c.name.toLowerCase().includes(q)) || 
            (c.province && c.province.toLowerCase().includes(q))
        );
    },
    selectCity(c) {
        this.selectedId = c.id;
        this.open = false;
        this.search = '';
        $dispatch('city-selected', { id: c.id, name: c.name, province: c.province });
        if (this.syncProvince && c.province && typeof $wire !== 'undefined') {
            $wire.set('province', c.province);
        }
    }
}" @click.outside="open = false" class="relative">
    @if($label)
        <label class="block text-xs font-bold text-slate-700 mb-1.5">
            {{ $label }} @if($showAsterisk)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <!-- Trigger Button -->
    <button type="button" 
            @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
            class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border rounded-xl text-left flex items-center justify-between transition focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] cursor-pointer {{ $error || $errors->has($name) || ($wireModel && $errors->has($wireModel)) ? 'border-rose-400 ring-1 ring-rose-200' : 'border-slate-200 hover:border-slate-300' }}">
        <span x-text="selectedName || '{{ $placeholder }}'" 
              :class="selectedName ? 'text-slate-900 font-medium' : 'text-slate-400 font-normal'"
              class="truncate pr-2"></span>
        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-150" :class="open ? 'rotate-180 text-[#0066FF]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Search Dropdown Panel -->
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="absolute left-0 right-0 z-50 mt-1.5 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden p-2 min-w-[260px]">

        <!-- Search Box -->
        <div class="relative mb-2">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input x-ref="searchInput" type="text" x-model="search" 
                   placeholder="Ketik nama kota atau kabupaten..."
                   class="w-full pl-8 pr-7 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder-slate-400 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] transition outline-none">
            <button x-show="search.length > 0" @click="search = ''; $refs.searchInput.focus()" type="button" 
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- City Options List -->
        <div class="max-h-56 overflow-y-auto space-y-0.5 pr-0.5 divide-y divide-slate-50">
            <template x-for="c in filteredCities" :key="c.id">
                <div @click="selectCity(c)"
                     class="px-3 py-2 text-xs rounded-xl cursor-pointer flex items-center justify-between transition-colors"
                     :class="String(selectedId) === String(c.id) ? 'bg-blue-50 text-[#0066FF] font-bold' : 'text-slate-700 hover:bg-slate-100'">
                    <div class="min-w-0 pr-2">
                        <span x-text="c.name" class="block font-medium truncate"></span>
                        <span x-text="c.province" class="block text-[10px] text-slate-400"></span>
                    </div>
                    <svg x-show="String(selectedId) === String(c.id)" class="w-4 h-4 text-[#0066FF] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </template>
            <div x-show="filteredCities.length === 0" class="py-4 text-center text-xs text-slate-400">
                Kota tidak ditemukan. Coba kata kunci lain.
            </div>
        </div>
    </div>

    <!-- Hidden Input for Standard Form Submission & JS References -->
    <input x-ref="hiddenInput" type="hidden" name="{{ $name }}" @if($id) id="{{ $id }}" @endif :value="selectedId">

    @if($error)
        <p class="text-rose-500 text-[11px] mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-rose-500 text-[11px] mt-1">{{ $errors->first($name) }}</p>
    @elseif($wireModel && $errors->has($wireModel))
        <p class="text-rose-500 text-[11px] mt-1">{{ $errors->first($wireModel) }}</p>
    @endif
</div>
