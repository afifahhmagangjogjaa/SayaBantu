@php
    $title = 'Pengaturan Banner, Ikon & Promosi';
    $breadcrumb = 'Super Admin / Pengaturan / Banner, Ikon & Promosi';
    $activePromosCount = count(array_filter($promos, fn($p) => ($p['is_active'] ?? true) == true));
@endphp

<div x-data="{ activeTab: 'promos' }" class="space-y-6">
    <!-- Notification Toast / Alert -->
    @if (session('message'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 shadow-sm transition-all animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('message') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-emerald-500 hover:text-emerald-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-2xl text-blue-800 shadow-sm transition-all animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('info') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-blue-500 hover:text-blue-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 shadow-sm transition-all animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Main Header Card with Quick Stats & Tabs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-gray-100">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Pengaturan Banner &amp; Tampilan Visual</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola iklan promosi customer, banner landing page, serta ikon branding aplikasi SayaBantu dalam satu tempat terpadu.
                </p>
            </div>

            <!-- Quick Stat Badges -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-800">{{ $activePromosCount }} Iklan Customer Aktif</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-purple-50 border border-purple-200 flex items-center gap-2">
                    <span class="text-xs font-bold text-purple-800">{{ count($homeBanners) }} Banner Beranda</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-800">{{ $appIcon ? 'Ikon Kustom' : 'Ikon Bawaan' }}</span>
                </div>
            </div>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="flex items-center gap-2 pt-4 overflow-x-auto no-scrollbar">
            <button type="button"
                @click="activeTab = 'promos'"
                :class="activeTab === 'promos' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span>Iklan &amp; Promosi Customer</span>
                <span :class="activeTab === 'promos' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700'" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                    {{ count($promos) }}
                </span>
            </button>

            <button type="button"
                @click="activeTab = 'home'"
                :class="activeTab === 'home' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Banner Beranda (Landing Page)</span>
                <span :class="activeTab === 'home' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700'" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                    {{ count($homeBanners) }}
                </span>
            </button>

            <button type="button"
                @click="activeTab = 'icon'"
                :class="activeTab === 'icon' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-900'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Ikon Aplikasi (Branding)</span>
                <span :class="activeTab === 'icon' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700'" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                    {{ $appIcon ? 'Kustom' : 'Default' }}
                </span>
            </button>
        </div>
    </div>

    <!-- TAB 1: Iklan & Promosi Dashboard Customer -->
    <div x-show="activeTab === 'promos'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left: Promos List (7 Cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Daftar Iklan &amp; Promosi</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Tampil sebagai slider berputar di atas dashboard Customer dengan link kunjungi.</p>
                    </div>

                    <button type="button" wire:click="openNewPromoModal"
                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Iklan Baru</span>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse($promos as $idx => $promo)
                        @php
                            $pId = $promo['id'] ?? 'promo_' . $idx;
                            $pActive = $promo['is_active'] ?? true;
                            $pLayout = $promo['layout'] ?? 'standard';
                            $pBadge = $promo['badge'] ?? '';
                            $pTitle = $promo['title'] ?? '';
                            $pDesc = $promo['description'] ?? '';
                            $pBtnText = $promo['button_text'] ?? 'Kunjungi';
                            $pLink = $promo['button_link'] ?? '';
                            $pTarget = $promo['target_blank'] ?? false;
                            $pImage = $promo['image'] ?? null;
                            $pImageUrl = $pImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($pImage)
                                ? asset('storage/' . $pImage)
                                : asset('images/sayabantu-mascot.jpg');
                        @endphp

                        <div class="p-4 rounded-xl border {{ $pActive ? 'border-gray-200 bg-white hover:border-emerald-300' : 'border-gray-200 bg-gray-50/70 opacity-60' }} transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 text-gray-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $loop->iteration }}
                                    </span>

                                    <!-- Thumbnail -->
                                    <div class="w-16 h-16 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center p-1 shrink-0 overflow-hidden">
                                        <img src="{{ $pImageUrl }}" alt="Promo" class="max-w-full max-h-full object-contain">
                                    </div>

                                    <!-- Content info -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                            @if(!empty($pBadge))
                                                <span class="inline-block px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                                                    {{ $pBadge }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $pLayout === 'full_image' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                                {{ $pLayout === 'full_image' ? 'Full Gambar' : 'Format Standar' }}
                                            </span>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $pActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                                {{ $pActive ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>

                                        <h3 class="text-xs font-bold text-gray-900 truncate">
                                            {{ $pTitle ?: '(Tanpa Judul Poster)' }}
                                        </h3>

                                        @if(!empty($pDesc))
                                            <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                                {{ $pDesc }}
                                            </p>
                                        @endif

                                        <div class="flex items-center gap-2 mt-2 text-[11px] text-gray-400 flex-wrap">
                                            <span class="font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded flex items-center gap-1 truncate max-w-[220px]">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                {{ $pLink }}
                                            </span>
                                            @if(!empty($pBtnText))
                                                <span>Tombol: <strong class="text-gray-700">{{ $pBtnText }}</strong></span>
                                            @endif
                                            @if($pTarget)
                                                <span class="text-blue-600">(Tab Baru)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" wire:click="togglePromoActive('{{ $pId }}')"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition cursor-pointer {{ $pActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200' }}"
                                        title="Ubah status">
                                        {{ $pActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                    <button type="button" wire:click="editPromo('{{ $pId }}')"
                                        class="p-1.5 text-gray-600 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition border border-gray-200 cursor-pointer"
                                        title="Edit Iklan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="deletePromo('{{ $pId }}')" wire:confirm="Yakin ingin menghapus iklan promosi ini?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition border border-gray-200 cursor-pointer"
                                        title="Hapus Iklan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            Belum ada iklan promosi. Klik tombol "Tambah Iklan Baru" untuk membuat iklan pertama Anda.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Interactive Live Simulation in Customer App (5 Cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl shadow-sm p-6 border border-gray-200 sticky top-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Simulasi Tampilan Customer</h2>
                        <p class="text-[11px] text-gray-500">Pratinjau slider bergerak otomatis di HP pengguna</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $activePromosCount }} Aktif
                    </span>
                </div>

                @php
                    $previewActivePromos = array_values(array_filter($promos, fn($p) => ($p['is_active'] ?? true) == true));
                @endphp

                @if(count($previewActivePromos) > 0)
                    <div class="w-full max-w-sm mx-auto"
                         x-data="{
                            current: 0,
                            total: {{ count($previewActivePromos) }},
                            timer: null,
                            init() {
                                if (this.total > 1) {
                                    this.timer = setInterval(() => { this.current = (this.current + 1) % this.total; }, 4500);
                                }
                            },
                            goTo(idx) { this.current = idx; }
                         }">
                        <!-- Phone Simulation Card -->
                        <div class="p-2.5 bg-slate-100 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="relative overflow-hidden rounded-2xl text-white select-none shadow-md"
                                 style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%);">
                                <!-- Decorative circles matching customer dashboard -->
                                <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                                <div class="absolute -left-6 -top-6 w-20 h-20 bg-white/10 rounded-full blur-lg pointer-events-none"></div>

                                <div class="flex h-full will-change-transform transition-transform duration-500 ease-out"
                                     :style="'transform: translateX(-' + (current * 100) + '%);'">
                                    @foreach($previewActivePromos as $p)
                                        @php
                                            $prevLayout = $p['layout'] ?? 'standard';
                                            $prevImg = $p['image'] ?? null;
                                            $prevImgUrl = $prevImg && \Illuminate\Support\Facades\Storage::disk('public')->exists($prevImg)
                                                ? asset('storage/' . $prevImg)
                                                : asset('images/sayabantu-mascot.jpg');
                                        @endphp
                                        @if($prevLayout === 'full_image')
                                            <div class="shrink-0 w-full relative block overflow-hidden min-h-[160px] max-h-[190px] bg-blue-900">
                                                <img src="{{ $prevImgUrl }}" alt="Preview" class="w-full h-full min-h-[160px] max-h-[190px] object-cover pointer-events-none select-none">
                                                @if(!empty($p['badge']) || !empty($p['title']) || !empty($p['button_text']))
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent flex items-end p-3.5 pointer-events-none">
                                                        <div class="w-full flex items-end justify-between gap-2">
                                                            <div class="max-w-[70%]">
                                                                @if(!empty($p['badge']))
                                                                    <span class="inline-block px-2 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-[9px] font-bold text-white mb-1 border border-white/20">
                                                                        {{ $p['badge'] }}
                                                                    </span>
                                                                @endif
                                                                @if(!empty($p['title']))
                                                                    <h4 class="text-xs font-extrabold text-white leading-tight drop-shadow truncate">
                                                                        {{ $p['title'] }}
                                                                    </h4>
                                                                @endif
                                                            </div>
                                                            @if(!empty($p['button_text']))
                                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-[#0066FF] font-bold text-[10px] rounded-full shadow-xs">
                                                                    <span>{{ $p['button_text'] }}</span>
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="shrink-0 w-full p-4 flex items-center justify-between gap-3 min-h-[160px]">
                                                <div class="flex-1 max-w-[65%]">
                                                    @if(!empty($p['badge']))
                                                        <span class="inline-block px-2 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-[9px] font-bold text-white mb-1">
                                                            {{ $p['badge'] }}
                                                        </span>
                                                    @endif
                                                    <h4 class="text-xs font-extrabold leading-tight line-clamp-2">
                                                        {{ $p['title'] ?: 'Butuh bantuan untuk pekerjaan kecil hingga besar?' }}
                                                    </h4>
                                                    @if(!empty($p['description']))
                                                        <p class="text-[10px] text-blue-100 mt-1 line-clamp-2 leading-relaxed">
                                                            {{ $p['description'] }}
                                                        </p>
                                                    @endif
                                                    <div class="mt-2.5 inline-flex items-center gap-1 px-3 py-1 bg-white text-[#0066FF] font-bold text-[10px] rounded-full shadow-xs pointer-events-none">
                                                        <span>{{ $p['button_text'] ?: 'Kunjungi' }}</span>
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                                    </div>
                                                </div>
                                                <div class="w-20 h-20 shrink-0 flex items-center justify-center p-1">
                                                    <img src="{{ $prevImgUrl }}" alt="Preview" class="max-w-full max-h-full object-contain rounded-lg drop-shadow">
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if(count($previewActivePromos) > 1)
                                    <div class="absolute bottom-2 right-3 flex items-center gap-1 z-20">
                                        @foreach($previewActivePromos as $dIdx => $dItem)
                                            <button type="button" @click="goTo({{ $dIdx }})"
                                                class="h-1 rounded-full transition-all duration-300 cursor-pointer"
                                                :class="current === {{ $dIdx }} ? 'w-4 bg-white' : 'w-1 bg-white/40'"></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400 text-center mt-2.5">
                            Slide bergerak otomatis setiap 4.5 detik jika terdapat lebih dari 1 iklan aktif.
                        </p>
                    </div>
                @else
                    <div class="py-12 text-center text-xs text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        Tidak ada iklan aktif yang dapat dipratinjau.
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- TAB 2: Banner Beranda (Landing Page) -->
    <div x-show="activeTab === 'home'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Banner Beranda (Landing Page)</h2>
                        <p class="text-xs text-gray-500">Banner visual yang tampil bergantian di halaman depan utama SayaBantu.</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full">
                    {{ count($homeBanners) }} Banner Terpasang
                </span>
            </div>

            <!-- Gallery Home -->
            <div class="mb-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                    @forelse($homeBanners as $i => $b)
                        <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50 group shadow-2xs">
                            <img src="{{ asset('storage/' . $b) }}" alt="banner-home-{{ $i }}" class="w-full h-24 sm:h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                            <button wire:click="removeHome({{ $i }})" wire:confirm="Yakin ingin menghapus banner beranda ini?" type="button"
                                class="absolute top-2 right-2 w-7 h-7 bg-white/95 hover:bg-red-500 text-gray-700 hover:text-white rounded-md flex items-center justify-center shadow-md transition-all cursor-pointer"
                                title="Hapus Banner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            Belum ada banner untuk halaman beranda. Silakan unggah banner baru di bawah ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upload Area Home -->
            <div class="space-y-4 pt-5 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Unggah Banner Beranda Baru</label>

                <div x-data="{
                    isDragging: false,
                    async handleFiles(files) {
                        if (!files || !files.length) return;
                        const compressedList = [];
                        for (let i = 0; i < files.length; i++) {
                            const comp = await window.compressImageFile(files[i]);
                            compressedList.push(comp);
                        }
                        $wire.uploadMultiple('homeUploads', compressedList);
                    }
                }">
                    <label for="home-file-input"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                        :class="isDragging ? 'border-primary-500 bg-primary-50/40' : 'border-gray-300 bg-gray-50/60 hover:bg-gray-50 hover:border-primary-400'"
                        class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer block">
                        
                        <input type="file" id="home-file-input" @change="handleFiles($event.target.files); $event.target.value = ''" accept="image/*" multiple class="hidden" />
                        
                        <div class="space-y-1.5">
                            <svg class="mx-auto h-9 w-9 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-xs text-gray-600">
                                <span class="font-bold text-primary-600 hover:text-primary-700">Pilih file gambar</span>
                                <span> atau tarik &amp; lepas ke sini</span>
                            </div>
                            <p class="text-[11px] text-gray-400">PNG, JPG, JPEG hingga 10MB per file. Mendukung multi-file sekaligus.</p>
                        </div>
                    </label>
                </div>

                <div wire:loading wire:target="homeUploads" class="text-xs text-primary-600 font-semibold flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses file banner beranda...
                </div>

                @error('homeUploads.*') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

                @if (!empty($homeUploads))
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 pt-2">
                        @foreach ($homeUploads as $idx => $upload)
                            <div class="relative rounded-lg overflow-hidden border-2 border-primary-300 shadow-2xs">
                                <img src="{{ $upload->temporaryUrl() }}" alt="preview-home-{{ $idx }}" class="w-full h-20 object-cover">
                                <button type="button" wire:click="removeHomeUpload({{ $idx }})" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-0.5 shadow cursor-pointer" title="Hapus">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-2 pt-2">
                    <button type="button" wire:click.prevent="saveHome" wire:loading.attr="disabled" wire:target="homeUploads, saveHome"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-primary-500/20 transition cursor-pointer">
                        <span wire:loading.remove wire:target="saveHome">Unggah &amp; Simpan Banner</span>
                        <span wire:loading wire:target="saveHome">Menyimpan...</span>
                    </button>
                    @if (!empty($homeUploads))
                        <button type="button" wire:click.prevent="$set('homeUploads', [])"
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Ikon Aplikasi (Branding) -->
    <div x-show="activeTab === 'icon'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Pengaturan Ikon Aplikasi (Logo)</h2>
                        <p class="text-xs text-gray-500">Ubah ikon resmi SayaBantu yang tampil di seluruh sistem web dan navbar aplikasi.</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                    {{ $appIcon ? 'Ikon Kustom Aktif' : 'Ikon Default Sistem' }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Current Icon Showcase (5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Ikon Yang Sedang Digunakan</label>

                    <div class="p-5 rounded-2xl border border-gray-200 bg-gray-50/60 space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-gray-200 p-2 shadow-xs flex items-center justify-center shrink-0">
                                @if($appIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($appIcon))
                                    <img src="{{ asset('storage/' . $appIcon) }}" alt="Ikon Aktif" class="w-full h-full object-contain">
                                @else
                                    <img src="{{ asset('images/sayabantu-hand-icon.png') }}" alt="Ikon Default" class="w-full h-full object-contain">
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-gray-800">
                                    {{ $appIcon ? 'Ikon Kustom Terpasang' : 'Ikon Tangan Kuning (Default)' }}
                                </p>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    Tampil otomatis di header Super Admin, Admin, Mitra, dan Customer.
                                </p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-200/70 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">Ingin kembali ke logo asli?</span>
                            @if($appIcon)
                                <button type="button" wire:click="resetIcon" wire:confirm="Kembalikan ikon ke default bawaan sistem?"
                                    class="text-xs text-red-600 hover:text-red-700 font-bold hover:underline cursor-pointer">
                                    Kembalikan ke Default
                                </button>
                            @else
                                <span class="text-xs text-gray-400 font-medium">Sudah Default</span>
                            @endif
                        </div>
                    </div>

                    <!-- Live Logo Preview in Navbar Context -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-[11px] font-semibold text-gray-500">Pratinjau di Navbar Website:</label>
                        <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-xs flex items-center gap-3">
                            @if($appIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($appIcon))
                                <x-sayabantu-logo size="md" :customIconUrl="asset('storage/' . $appIcon)" />
                            @else
                                <x-sayabantu-logo size="md" />
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Upload New Icon (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Unggah Ikon Baru</label>

                    <div x-data="{
                        isDragging: false,
                        handleFile(file) {
                            if (!file) return;
                            $wire.upload('iconUpload', file);
                        }
                    }">
                        <label for="icon-file-input"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleFile($event.dataTransfer.files[0])"
                            :class="isDragging ? 'border-primary-500 bg-primary-50/40' : 'border-gray-300 bg-gray-50/60 hover:bg-gray-50 hover:border-primary-400'"
                            class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer block">
                            
                            <input type="file" id="icon-file-input" wire:model="iconUpload" accept="image/png,image/jpeg,image/svg+xml,image/webp,image/x-icon" class="hidden" />
                            
                            <div class="space-y-2">
                                <div class="mx-auto w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-bold text-primary-600 hover:text-primary-700">Pilih file ikon baru</span>
                                    <span> atau tarik &amp; lepas ke sini</span>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-relaxed max-w-sm mx-auto">
                                    Format PNG, SVG, JPG, WebP. Disarankan format PNG transparan atau SVG rasio 1:1 (persegi) minimal 512x512 px. Maksimal 5MB.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div wire:loading wire:target="iconUpload" class="text-xs text-primary-600 font-semibold flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengunggah pratinjau file ikon...
                    </div>

                    @error('iconUpload') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

                    @if ($iconUpload)
                        <div class="p-4 bg-amber-50/60 border border-amber-200 rounded-xl flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 bg-white border border-amber-200 rounded-lg p-1.5 flex items-center justify-center shrink-0 shadow-2xs">
                                    <img src="{{ $iconUpload->temporaryUrl() }}" alt="Pratinjau Ikon Baru" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Pratinjau File Terpilih</p>
                                    <p class="text-[11px] text-gray-500">Klik "Simpan Ikon Baru" untuk menerapkan ke aplikasi.</p>
                                </div>
                            </div>
                            <button type="button" wire:click="removeIconUpload" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-white rounded-lg transition cursor-pointer" title="Batal upload">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" wire:click.prevent="saveIcon" wire:loading.attr="disabled" wire:target="iconUpload, saveIcon"
                            class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-primary-500/20 transition cursor-pointer">
                            <span wire:loading.remove wire:target="saveIcon">Simpan Ikon Baru</span>
                            <span wire:loading wire:target="saveIcon">Menyimpan...</span>
                        </button>
                        @if ($iconUpload)
                            <button type="button" wire:click.prevent="removeIconUpload"
                                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                                Batal
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form: Tambah / Edit Iklan Promosi -->
    @if ($showPromoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-fade-in"
             @keydown.escape.window="$wire.closePromoModal()">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">
                            {{ $editingPromoId ? 'Edit Iklan Promosi' : 'Tambah Iklan Promosi Baru' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closePromoModal" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4">
                    <!-- Format Tampilan Iklan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Format Tampilan Iklan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none {{ $promoFormLayout === 'standard' ? 'border-emerald-500 bg-emerald-50/50 shadow-xs ring-1 ring-emerald-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="promoFormLayout" value="standard" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <p class="text-xs font-bold text-gray-900">Format Standar (Kartu Teks &amp; Maskot)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Teks judul, deskripsi, tombol dan gambar maskot di sisi kanan yang otomatis menyesuaikan secara proporsional.</p>
                                </div>
                            </label>

                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none {{ $promoFormLayout === 'full_image' ? 'border-emerald-500 bg-emerald-50/50 shadow-xs ring-1 ring-emerald-500' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="promoFormLayout" value="full_image" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <p class="text-xs font-bold text-gray-900">Full Gambar (Poster Penuh)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Gambar banner poster penuh mengisi seluruh kartu secara responsif dan bisa langsung diklik untuk dialihkan ke link tujuan.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Teks Badge / Label {{ $promoFormLayout === 'full_image' ? '(Opsional)' : '' }}
                        </label>
                        <input type="text" wire:model="promoFormBadge" placeholder="Contoh: sayabantu.com 🤝 atau Promo Diskon 50%"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                        @error('promoFormBadge') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Judul Iklan / Promosi {{ $promoFormLayout === 'full_image' ? '(Opsional)' : '' }}
                        </label>
                        <input type="text" wire:model="promoFormTitle" placeholder="Contoh: Butuh bantuan untuk pekerjaan kecil hingga besar?"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition font-medium" />
                        @error('promoFormTitle') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Singkat (Opsional)
                        </label>
                        <textarea wire:model="promoFormDesc" rows="2" placeholder="Contoh: Pesan jasa serabutan cepat, mudah, dan terpercaya kapan saja."
                            class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition resize-none"></textarea>
                        @error('promoFormDesc') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Teks Tombol Kunjungi {{ $promoFormLayout === 'full_image' ? '(Opsional)' : '' }}
                            </label>
                            <input type="text" wire:model="promoFormButtonText" placeholder="Contoh: Kunjungi atau Pesan Sekarang"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition font-semibold" />
                            @error('promoFormButtonText') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tautan / Link Tujuan (Redirect URL)</label>
                            <input type="text" wire:model="promoFormButtonLink" placeholder="Contoh: https://wa.me/... atau /customer/helps/create"
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-300 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" />
                            @error('promoFormButtonLink') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="modalTargetBlank" wire:model="promoFormTargetBlank" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                        <label for="modalTargetBlank" class="text-xs text-gray-600 cursor-pointer select-none">
                            Buka tautan di tab baru (untuk URL eksternal seperti WhatsApp / Instagram / Website luar)
                        </label>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="modalActive" wire:model="promoFormActive" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                        <label for="modalActive" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">
                            Aktifkan iklan ini (langsung tampil di dashboard)
                        </label>
                    </div>

                    <!-- Promo Image Upload in Modal -->
                    <div class="space-y-2 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                {{ $promoFormLayout === 'full_image' ? 'File Gambar Banner Poster' : 'Gambar / Maskot Iklan' }}
                            </label>
                            <span class="text-[11px] text-gray-400">
                                {{ $promoFormLayout === 'full_image' ? 'Wajib untuk Full Gambar' : 'Opsional (menyesuaikan otomatis)' }}
                            </span>
                        </div>

                        <div class="border-2 border-dashed border-gray-300 hover:border-emerald-400 rounded-xl p-4 text-center bg-gray-50/60 transition">
                            <input type="file" id="modal-promo-image" wire:model="promoFormImageUpload" accept="image/*" class="hidden" />
                            <label for="modal-promo-image" class="cursor-pointer block">
                                <svg class="mx-auto h-7 w-7 text-gray-400 mb-1" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Pilih gambar dari perangkat</span>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $promoFormLayout === 'full_image' ? 'Disarankan gambar banner landscape rasio 16:9, 21:9, atau poster lebar. Maksimal 10MB.' : 'PNG, JPG, WebP hingga 10MB. Otomatis menyesuaikan proporsi tanpa gepeng atau terpotong.' }}
                                </p>
                            </label>
                        </div>

                        <div wire:loading wire:target="promoFormImageUpload" class="text-xs text-emerald-600 font-semibold flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengunggah pratinjau gambar...
                        </div>

                        @error('promoFormImageUpload') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

                        @if ($promoFormImageUpload)
                            <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-12 h-12 bg-white border border-emerald-200 rounded-lg p-1 flex items-center justify-center shrink-0 shadow-2xs">
                                        <img src="{{ $promoFormImageUpload->temporaryUrl() }}" alt="Preview" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">Gambar Baru Dipilih</p>
                                        <p class="text-[11px] text-gray-500">Akan disimpan saat klik tombol Simpan Iklan.</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removePromoFormImageUpload" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-white rounded-lg transition" title="Batal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @elseif ($promoFormExistingImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($promoFormExistingImage))
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-12 h-12 bg-white border border-gray-200 rounded-lg p-1 flex items-center justify-center shrink-0 shadow-2xs">
                                        <img src="{{ asset('storage/' . $promoFormExistingImage) }}" alt="Existing" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">Gambar Saat Ini</p>
                                        <p class="text-[11px] text-gray-500">Gambar kustom yang sudah terpasang.</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removePromoFormExistingImage" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">
                                    Hapus Gambar (Gunakan Maskot)
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 border-t border-gray-100 flex items-center justify-end gap-2.5 bg-gray-50/50 rounded-b-2xl">
                    <button type="button" wire:click="closePromoModal"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="savePromo" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition cursor-pointer">
                        <span wire:loading.remove wire:target="savePromo">Simpan Iklan</span>
                        <span wire:loading wire:target="savePromo">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    if (typeof window.compressImageFile !== 'function') {
        window.compressImageFile = async function(file, maxWidth = 1920, maxHeight = 1080, quality = 0.85) {
            if (!file || !file.type.startsWith('image/')) return file;
            if (file.type === 'image/svg+xml' || file.type === 'image/gif') return file;

            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        let w = img.width;
                        let h = img.height;
                        if (w > maxWidth || h > maxHeight) {
                            if (w / h > maxWidth / maxHeight) {
                                h = Math.round(h * (maxWidth / w));
                                w = maxWidth;
                            } else {
                                w = Math.round(w * (maxHeight / h));
                                h = maxHeight;
                            }
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);
                        canvas.toBlob(
                            (blob) => {
                                if (blob && blob.size < file.size) {
                                    resolve(new File([blob], file.name, { type: file.type, lastModified: Date.now() }));
                                } else {
                                    resolve(file);
                                }
                            },
                            file.type,
                            quality
                        );
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        };
    }
</script>
