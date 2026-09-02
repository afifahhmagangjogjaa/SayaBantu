<div class="space-y-4">
    <!-- Alert Messages -->
    @if(session()->has('message'))
        <div class="p-3.5 text-xs text-green-800 bg-green-50 rounded-xl border border-green-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Bar (Compact & Responsive) -->
    <div class="bg-white rounded-xl shadow-2xs border border-gray-200 p-3.5 sm:p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama pengguna, email, no. hp..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-lg text-xs text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
            </div>

            <!-- Role Filter -->
            <div>
                <select wire:model.live="roleFilter"
                    class="w-full px-3 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-lg text-xs text-gray-800 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                    <option value="">Semua Role (Mitra & Customer)</option>
                    <option value="mitra">Hanya Mitra</option>
                    <option value="customer">Hanya Customer</option>
                </select>
            </div>

            <!-- City Filter -->
            <div>
                <select wire:model.live="cityFilter"
                    class="w-full px-3 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-lg text-xs text-gray-800 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                    <option value="">Semua Kota</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table Rekap Pengguna (Fits cleanly on screen) -->
    <div class="bg-white rounded-xl shadow-2xs border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Rekap Rating & Ulasan Pengguna</h2>
                <p class="text-[11px] text-gray-500">Daftar performa ulasan mitra dan customer dalam platform.</p>
            </div>
            @if($users->total() > 0)
                <span class="text-[11px] text-gray-500 font-medium">Total {{ $users->total() }} pengguna</span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-bold text-gray-600 uppercase tracking-wider">Nama & Kontak</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-24">Role</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-bold text-gray-600 uppercase tracking-wider w-28">Kota</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-32">Rata-rata Rating</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-24">Ulasan</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $u)
                        @php
                            $avgRating = $u->isMitra() ? $u->mitra_average_rating : $u->customer_average_rating;
                            $ratingCount = $u->isMitra() ? $u->mitra_rating_count : $u->customer_rating_count;
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors {{ $avgRating > 0 && $avgRating <= 2.5 ? 'bg-red-50/20' : '' }}">
                            <!-- No -->
                            <td class="px-3 py-2.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <!-- Nama & Kontak -->
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <div class="text-xs font-semibold text-gray-900 truncate leading-tight">{{ $u->name }}</div>
                                            @if($u->isShadowBanned())
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border" style="background-color: #f3e8ff; color: #6b21a8; border-color: #d8b4fe;">
                                                    👻 Shadow Ban
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-500 truncate mt-0.5">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $u->isMitra() ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $u->isMitra() ? 'Mitra' : 'Customer' }}
                                </span>
                            </td>

                            <!-- Kota -->
                            <td class="px-3 py-2.5 text-xs text-gray-600 whitespace-nowrap">
                                {{ $u->city?->name ?? $u->city_name ?? '-' }}
                            </td>

                            <!-- Rata-rata Rating -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                @if($ratingCount > 0)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $avgRating <= 2.5 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        <span class="text-yellow-500 text-xs">★</span>
                                        <span class="text-xs font-bold">{{ number_format($avgRating, 1) }}</span>
                                        <span class="text-[10px] text-gray-400">/ 5.0</span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum ada</span>
                                @endif
                            </td>

                            <!-- Total Ulasan -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap text-xs font-bold text-gray-700">
                                {{ $ratingCount }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <button wire:click="viewUserRatings({{ $u->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-black rounded-lg text-xs font-semibold transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 text-xs">
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- ================= MODAL DETAIL ULASAN SHOPEE-STYLE ================= -->
    @if($showUserRatingsModal && $selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
                <!-- Header Pengguna -->
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/90 flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-gray-900 truncate">{{ $selectedUser->name }}</h3>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $selectedUser->isMitra() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $selectedUser->isMitra() ? 'Mitra' : 'Customer' }}
                                </span>
                                @if($selectedUser->isShadowBanned())
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border" style="background-color: #f3e8ff; color: #6b21a8; border-color: #d8b4fe;">
                                        👻 Shadow Banned
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-gray-500 truncate">{{ $selectedUser->email }} • {{ $selectedUser->city?->name ?? 'Kota -' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- Tombol Shadow Ban -->
                        <button type="button" 
                            wire:click="toggleShadowBan({{ $selectedUser->id }})" 
                            wire:loading.attr="disabled"
                            wire:target="toggleShadowBan"
                            style="{{ $selectedUser->isShadowBanned() ? 'background-color: #7e22ce; color: #ffffff; border-color: #6b21a8;' : 'background-color: #ffffff; color: #374151; border-color: #d1d5db;' }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border shadow-2xs cursor-pointer hover:opacity-90"
                            title="{{ $selectedUser->isShadowBanned() ? 'Klik untuk melepaskan Shadow Ban' : 'Aktifkan Shadow Ban (order/bantuan tidak akan masuk/tampil)' }}">
                            <span wire:loading.remove wire:target="toggleShadowBan">👻</span>
                            <svg wire:loading wire:target="toggleShadowBan" class="w-3.5 h-3.5 animate-spin text-current" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span class="whitespace-nowrap font-medium" style="{{ $selectedUser->isShadowBanned() ? 'color: #ffffff;' : 'color: #374151;' }}">{{ $selectedUser->isShadowBanned() ? 'Shadow Banned (Aktif)' : 'Shadow Ban' }}</span>
                        </button>

                        <button type="button" wire:click="closeModal"
                            class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xs flex-shrink-0">✕</button>
                    </div>
                </div>

                <!-- Body (Shopee Header & List) -->
                <div class="p-5 overflow-y-auto space-y-4 flex-1">
                    @php
                        $userAvg = $selectedUser->isMitra() ? $selectedUser->mitra_average_rating : $selectedUser->customer_average_rating;
                    @endphp

                    <!-- Shopee Header Box: Score + Filter Pills -->
                    <div class="p-4 bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50 rounded-xl border border-amber-200 flex flex-col sm:flex-row sm:items-center gap-4">
                        <!-- Score Display Left -->
                        <div class="sm:w-36 flex-shrink-0 text-center sm:border-r sm:border-amber-200 sm:pr-3">
                            <div class="flex items-baseline justify-center gap-1">
                                <span class="text-2xl font-extrabold text-amber-600">{{ number_format($userAvg, 1) }}</span>
                                <span class="text-xs font-semibold text-gray-500">/ 5</span>
                            </div>
                            <div class="flex justify-center text-amber-400 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $userAvg ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-[10px] text-gray-500 mt-0.5">
                                {{ $selectedUser->isMitra() ? 'Ulasan Customer' : 'Ulasan Mitra' }}
                            </p>
                        </div>

                        <!-- Interactive Filter Pills (Shopee style) -->
                        <div class="flex-1 flex flex-wrap items-center gap-1.5">
                            <button wire:click="setModalRatingFilter('all')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === 'all' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                Semua ({{ $modalRatingsBreakdown['total'] }})
                            </button>

                            <button wire:click="setModalRatingFilter('5')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === '5' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                5 Bintang ({{ $modalRatingsBreakdown['stars'][5] }})
                            </button>

                            <button wire:click="setModalRatingFilter('4')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === '4' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                4 Bintang ({{ $modalRatingsBreakdown['stars'][4] }})
                            </button>

                            <button wire:click="setModalRatingFilter('3')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === '3' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                3 Bintang ({{ $modalRatingsBreakdown['stars'][3] }})
                            </button>

                            <button wire:click="setModalRatingFilter('2')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === '2' ? 'bg-red-500 border-red-600 text-white font-bold' : 'bg-white border-gray-200 text-red-600 hover:bg-red-50' }}">
                                2 Bintang ({{ $modalRatingsBreakdown['stars'][2] }})
                            </button>

                            <button wire:click="setModalRatingFilter('1')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === '1' ? 'bg-red-500 border-red-600 text-white font-bold' : 'bg-white border-gray-200 text-red-600 hover:bg-red-50' }}">
                                1 Bintang ({{ $modalRatingsBreakdown['stars'][1] }})
                            </button>

                            <button wire:click="setModalRatingFilter('with_comment')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === 'with_comment' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                Komentar ({{ $modalRatingsBreakdown['with_comment'] }})
                            </button>

                            <button wire:click="setModalRatingFilter('anonymous')"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs {{ $modalRatingFilter === 'anonymous' ? 'bg-gray-200 border-gray-400 text-gray-900 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                Anonim Publik ({{ $modalRatingsBreakdown['anonymous'] }})
                            </button>
                        </div>
                    </div>

                    <!-- Reviews List Feed -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                                Daftar Ulasan ({{ $filteredModalRatings->count() }})
                            </h4>
                        </div>

                        @forelse($filteredModalRatings as $r)
                            @php
                                $fromUser = $r->rater ?? $r->user;
                            @endphp
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-2 hover:bg-white hover:border-gray-300 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <!-- Pengirim Info -->
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-[11px] flex-shrink-0">
                                            {{ strtoupper(substr(optional($fromUser)->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-900 text-xs">{{ optional($fromUser)->name ?? 'Pengguna' }}</span>
                                                @if($r->is_anonymous)
                                                    <span class="px-1.5 py-0.2 text-[9px] font-semibold bg-amber-100 text-amber-800 rounded border border-amber-200" title="Nama disembunyikan dari lawan transaksi/publik">
                                                        Anonim bagi publik
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-gray-400">{{ optional($fromUser)->email ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <!-- Bintang + Tanggal + Hapus -->
                                    <div class="flex items-center gap-2.5">
                                        <div class="text-right">
                                            <div class="flex text-amber-400 justify-end">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $r->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-gray-400 text-[10px]">{{ $r->created_at->format('d M Y H:i') }}</span>
                                        </div>

                                        <!-- Hapus Ulasan -->
                                        <button wire:click="confirmDelete({{ $r->id }})"
                                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                                            title="Hapus ulasan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Terkait Bantuan -->
                                @if($r->help)
                                    <div class="flex items-center gap-1 text-[11px] text-primary-600 font-medium">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Bantuan: {{ $r->help->title }}</span>
                                    </div>
                                @endif

                                <!-- Teks Ulasan -->
                                @if($r->review)
                                    <div class="text-gray-800 bg-white p-2.5 rounded-lg border border-gray-200 text-xs italic">
                                        "{{ $r->review }}"
                                    </div>
                                @else
                                    <div class="text-gray-400 italic text-[10px]">(Tanpa komentar teks)</div>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 bg-gray-50 rounded-xl text-center text-xs text-gray-500 border border-gray-200">
                                Tidak ada ulasan pada filter bintang ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-5 animate-in fade-in zoom-in duration-200">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 text-center">Hapus Ulasan Ini?</h3>
                <p class="text-xs text-gray-500 text-center mt-1.5">
                    Tindakan ini akan menghapus ulasan secara permanen.
                </p>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <button type="button" wire:click="closeModal"
                        class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteRating"
                        class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition shadow-2xs">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
