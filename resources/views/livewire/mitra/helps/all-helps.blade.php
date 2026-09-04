<div class="min-h-screen bg-white">
    <style>
        :root{
            --brand-500: #0ea5a4;
            --brand-600: #08979a;
            --muted-600: #6b7280;
        }

        .card-shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card-shadow-hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .focus-ring:focus { outline: none; box-shadow: 0 0 0 3px rgba(14,165,164,0.2); }
        
        /* BRImo-style decorative pattern */
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
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-pattern::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden header-pattern" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <button onclick="window.history.back()" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold">Semua Bantuan</h1>
                        <p class="text-xs text-white/90 mt-0.5">Cari bantuan yang tersedia</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('mitra.notifications.index')])
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative mt-3">
                    <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama, lokasi, atau deskripsi..."
                        class="w-full pl-10 pr-10 py-2.5 rounded-2xl bg-white/95 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-white/80 focus:shadow-md outline-none transition text-xs font-medium">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(!empty($search))
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Curved separator (SVG) to create non-flat divider into content -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-6 min-h-[60vh]">
            <!-- Modern Sort & Filter Header -->
            <div class="mb-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-[#0098e7] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-gray-800">Urutkan Bantuan</span>
                        <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ $helps->total() ?? count($helps) }} tersedia
                        </span>
                    </div>

                    <!-- Sleek Custom Dropdown Filter -->
                    <div class="relative inline-flex items-center">
                        <select wire:model.live="sortBy" 
                            onchange="if(this.value === 'nearby' && navigator.geolocation){ navigator.geolocation.getCurrentPosition(p => { @this.setCoordinates(p.coords.latitude, p.coords.longitude); }); }" 
                            class="appearance-none pl-3.5 pr-8 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-semibold text-gray-700 shadow-2xs hover:border-[#0098e7]/60 focus:border-[#0098e7] focus:ring-2 focus:ring-blue-100 outline-none transition cursor-pointer">
                            <option value="latest">✨ Terbaru</option>
                            <option value="nearby">📍 Terdekat</option>
                            <option value="price_high">💰 Harga Tertinggi</option>
                            <option value="price_low">🏷️ Harga Terendah</option>
                            <option value="oldest">⏳ Terlama</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="space-y-4">
                @if(auth()->check() && !empty(auth()->user()->getMissingBiodataFields()))
                    @php
                        $missing = auth()->user()->getMissingBiodataFields();
                    @endphp
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 shadow-xs mb-4" style="display: flex; align-items: flex-start; gap: 14px;">
                        <div class="rounded-xl shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; width: 40px; height: 40px; min-width: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="font-bold text-amber-950 text-sm">Lengkapi Biodata Diri Anda</div>
                            <div class="mt-1 text-amber-800 leading-relaxed">
                                Mohon lengkapi data profil wajib (<strong>{{ implode(', ', $missing) }}</strong>) agar akun Anda lengkap dan dapat mengambil serta menjalankan bantuan.
                            </div>
                            <a href="{{ route('mitra.profile.edit') }}" class="inline-flex items-center gap-1 font-bold text-amber-900 bg-amber-200 hover:bg-amber-300 px-3.5 py-1.5 rounded-lg mt-2.5 transition text-xs shadow-2xs">
                                <span>Lengkapi Biodata Sekarang &rarr;</span>
                            </a>
                        </div>
                    </div>
                @endif

                @if(!empty($needsCity))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                        <div class="font-semibold">Atur kota Anda terlebih dahulu</div>
                        <div class="mt-1">Untuk melihat bantuan yang tersedia di daerah Anda, silakan tentukan Kota/Kabupaten Anda di halaman profil.</div>
                        <div class="mt-3">
                            <a href="{{ route('mitra.profile') }}" class="inline-block px-3 py-2 bg-yellow-600 text-white rounded-lg text-xs">Buka Profil</a>
                        </div>
                    </div>
                @endif
                {{-- Loading skeleton --}}
                <div wire:loading class="space-y-3">
                    @for($i=0;$i<4;$i++)
                        <div class="bg-gray-50 rounded-2xl p-4 card-shadow animate-pulse">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/5 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-4/5"></div>
                                </div>
                                <div class="w-16 h-4 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- List based on filter --}}
                @forelse($helps as $help)
                    <div class="bg-white rounded-xl p-3.5 shadow-sm hover:shadow-md transition-all border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($help->photo)
                                    <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-lg">
                                        {{ $help->category?->icon ?? '📦' }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $help->title }}</h3>
                                    <span class="text-xs font-bold whitespace-nowrap" style="color: #0098e7;">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex items-center gap-2 mb-2">
                                    @if($help->status === 'menunggu_mitra')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(255, 159, 67, 0.08); color:#ff8a00; border:1px solid rgba(255,159,67,0.12);">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(107, 114, 128, 0.08); color:#6b7280;">
                                            {{ ucfirst(str_replace('_',' ', $help->status)) }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ optional($help->created_at)->diffForHumans() }}</span>
                                </div>

                                <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ Str::limit($help->description, 100) }}</p>

                                @if($help->scheduled_at)
                                    <div class="text-xs text-gray-500 mb-2">📅 {{ \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('d M Y, H:i') }}</div>
                                @endif

                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-xs text-gray-500">📍 {{ $help->city->name ?? '-' }}</span>
                                    <div class="flex items-center gap-2">
                                            @php 
                                                $schedLabel = $help->scheduled_at ? \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('d M Y, H:i') : '' ;
                                                $catLabel = $help->category ? ($help->category->icon . ' ' . $help->category->name) : '📦 Lainnya';
                                            @endphp
                                            @if(is_null($help->mitra_id))
                                                <button type="button" onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}')" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md text-xs hover:bg-gray-200 transition">Lihat</button>
                                                <button type="button" onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}')" class="px-3 py-1.5 bg-blue-500 text-white rounded-md text-xs hover:bg-blue-600 transition">Ambil</button>
                                            @else
                                            <span class="px-3 py-1.5 bg-gray-50 text-gray-400 rounded-md text-xs">Diambil</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-700">{{ $search ? 'Tidak ada bantuan ditemukan' : 'Belum ada bantuan tersedia' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $search ? 'Coba cari dengan kata kunci lain' : 'Cek kembali nanti untuk bantuan baru' }}
                        </p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($helps->hasPages())
                    <div class="mt-6">
                        {{ $helps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Modal Preview Bantuan (Bottom Sheet Style) -->
    <div id="helpPreviewModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 hidden pb-16">
        <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[75vh] overflow-y-auto" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b px-5 py-4 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Preview Bantuan</h3>
                    <button type="button" onclick="closePreviewModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-5 pb-6">
                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Judul Bantuan</p>
                    <p id="previewTitle" class="text-base font-bold text-gray-900">-</p>
                </div>

                <div class="mb-2">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Nominal untuk Mitra</p>
                    <div id="previewAmount" class="inline-block bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-bold text-sm">
                        Rp 0
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Kategori</p>
                    <div id="previewCategory" class="text-sm text-gray-700">-</div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Jadwal Permintaan</p>
                    <div id="previewScheduled" class="text-sm text-gray-700">-</div>
                </div>

                <!-- Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-xs font-semibold text-blue-800 mb-1">Informasi Terbatas</p>
                    <p class="text-xs text-blue-700">
                        Deskripsi, alamat lengkap, lokasi di peta, foto, dan kontak customer akan ditampilkan setelah Anda mengambil bantuan ini.
                    </p>
                </div>
            </div>

            <!-- Sticky footer -->
            <div class="sticky bottom-0 bg-white border-t pt-4 px-5 pb-5">
                <div class="flex gap-3">
                    <button type="button" onclick="closePreviewModal()" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="button" id="previewTakeBtn" onclick="takeHelpFromModal()" class="flex-1 bg-primary-500 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-primary-600 transition">
                        Ambil Bantuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentHelpId = null;

        function showHelpPreview(helpId, title, amount, scheduled, category) {
            currentHelpId = helpId;
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewCategory').textContent = category || '📦 Lainnya';
            document.getElementById('previewAmount').textContent = '💰 Rp ' + amount.toLocaleString('id-ID');
            const schedEl = document.getElementById('previewScheduled');
            if (schedEl) {
                if (scheduled && scheduled.length) {
                    schedEl.textContent = scheduled;
                } else {
                    // fallback: fetch latest help data
                    fetch('/helps/' + helpId + '/json', { credentials: 'same-origin' })
                        .then(r => r.ok ? r.json() : Promise.reject(r))
                        .then(data => {
                            schedEl.textContent = data.scheduled_at ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(data.scheduled_at)) : '-';
                        }).catch(() => { schedEl.textContent = '-'; });
                }
            }
            document.getElementById('helpPreviewModal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('helpPreviewModal').classList.add('hidden');
            currentHelpId = null;
        }

        function takeHelpFromModal() {
            if (!currentHelpId) return;
            
            const btn = document.querySelector('[onclick="takeHelpFromModal()"]');
            const originalText = btn.textContent;
            
            // Fungsi untuk reset button
            const resetButton = () => {
                btn.textContent = originalText;
                btn.disabled = false;
            };
            
            // Fungsi untuk ambil bantuan dengan/tanpa lokasi
            const takeBantuanWithLocation = (lat = null, lng = null) => {
                if (lat && lng) {
                    console.log('📍 Mengambil bantuan dengan lokasi:', { lat, lng });
                    @this.takeHelp(currentHelpId, lat, lng);
                } else {
                    console.log('📍 Mengambil bantuan tanpa lokasi GPS');
                    @this.takeHelp(currentHelpId);
                }
                closePreviewModal();
                resetButton();
            };
            
            // Fungsi fallback: Coba gunakan IP-based location
            const tryIPBasedLocation = () => {
                console.log('🌐 Mencoba IP-based location...');
                btn.textContent = 'Mendeteksi lokasi dari IP...';
                
                // Gunakan ipapi.co untuk mendapatkan koordinat dari IP
                fetch('https://ipapi.co/json/', { timeout: 3000 })
                    .then(response => response.json())
                    .then(data => {
                        if (data.latitude && data.longitude) {
                            console.log('✅ IP-based location berhasil:', data);
                            takeBantuanWithLocation(data.latitude, data.longitude);
                        } else {
                            throw new Error('Invalid location data');
                        }
                    })
                    .catch(error => {
                        console.error('❌ IP-based location gagal:', error);
                        // Tetap tanyakan apakah mau ambil tanpa lokasi
                        if (confirm('📍 Lokasi tidak dapat dideteksi.\n\n✅ Ambil bantuan tanpa GPS tracking?\n\n(Lokasi dapat diupdate nanti saat Anda mulai bergerak)')) {
                            takeBantuanWithLocation();
                        } else {
                            resetButton();
                        }
                    });
            };
            
            // Request GPS permission dan ambil lokasi
            if (navigator.geolocation) {
                btn.textContent = 'Mendapatkan lokasi GPS...';
                btn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        
                        console.log('✅ GPS Location obtained:', { lat, lng, accuracy: accuracy + 'm' });
                        takeBantuanWithLocation(lat, lng);
                    },
                    (error) => {
                        console.error('❌ GPS Error:', error);
                        
                        // Error codes:
                        // 1 = PERMISSION_DENIED
                        // 2 = POSITION_UNAVAILABLE (no GPS hardware atau signal)
                        // 3 = TIMEOUT
                        
                        if (error.code === 2) {
                            // GPS tidak tersedia (laptop/desktop tanpa GPS)
                            console.log('💻 Device tidak memiliki GPS, mencoba IP-based location...');
                            tryIPBasedLocation();
                        } else if (error.code === 1) {
                            // User menolak permission
                            if (confirm('📍 Akses lokasi ditolak.\n\n🌐 Coba deteksi lokasi dari IP address?\n\n(Akurasi lebih rendah tetapi cukup untuk tracking)')) {
                                tryIPBasedLocation();
                            } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?\n\n(Lokasi dapat diupdate nanti)')) {
                                takeBantuanWithLocation();
                            } else {
                                resetButton();
                            }
                        } else {
                            // Timeout atau error lain
                            if (confirm('⏱️ GPS timeout atau error.\n\n🌐 Coba deteksi lokasi dari IP address?')) {
                                tryIPBasedLocation();
                            } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?')) {
                                takeBantuanWithLocation();
                            } else {
                                resetButton();
                            }
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 8000, // 8 detik timeout (lebih lama untuk laptop)
                        maximumAge: 0
                    }
                );
            } else {
                // Browser tidak support GPS (browser lama)
                console.warn('⚠️ Browser tidak support Geolocation API');
                btn.disabled = true;
                
                if (confirm('🌐 Browser tidak mendukung GPS.\n\nCoba deteksi lokasi dari IP address?')) {
                    tryIPBasedLocation();
                } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?')) {
                    takeBantuanWithLocation();
                } else {
                    resetButton();
                }
            }
        }

        // Close modal when clicking outside
        document.getElementById('helpPreviewModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePreviewModal();
            }
        });

        // Listen for help-taken event from Livewire
        window.addEventListener('help-taken', function(event) {
            // event.detail may contain the helpId depending on how Livewire dispatched it.
            var helpId = event?.detail?.helpId ?? event?.detail ?? null;

            if (!helpId) {
                // fallback: reload the page
                window.location.reload();
                return;
            }

            // Template URL with placeholder id, generated by Laravel route helper
            var detailUrlTemplate = @json(route('mitra.helps.detail', ['id' => 'REPLACE_ID']));

            // Replace placeholder with actual id and redirect to detail page
            window.location.href = detailUrlTemplate.replace('REPLACE_ID', helpId);
        });
    </script>
</div>