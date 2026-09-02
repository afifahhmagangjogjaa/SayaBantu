@php
    $userAvg = $user->isMitra() ? $user->mitra_average_rating : $user->customer_average_rating;
    $totalCount = $ratings->count();
    $starsCount = [
        5 => $ratings->where('rating', 5)->count(),
        4 => $ratings->where('rating', 4)->count(),
        3 => $ratings->where('rating', 3)->count(),
        2 => $ratings->where('rating', 2)->count(),
        1 => $ratings->where('rating', 1)->count(),
    ];
    $withCommentCount = $ratings->filter(fn($r) => !empty(trim((string)$r->review)))->count();
    $anonymousCount = $ratings->where('is_anonymous', true)->count();
@endphp

<div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="user-ratings-modal-backdrop"></div>

    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full z-10 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
        <!-- Header -->
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/90 flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="truncate">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $user->isMitra() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $user->isMitra() ? 'Mitra' : 'Customer' }}
                        </span>
                        @if($user->isShadowBanned())
                            <span id="header-shadow-ban-badge" class="px-2 py-0.5 text-[10px] font-bold rounded-full border" style="background-color: #f3e8ff; color: #6b21a8; border-color: #d8b4fe;">
                                👻 Shadow Banned
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-gray-500 truncate">{{ $user->email }} • {{ $user->city?->name ?? 'Kota -' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Tombol Shadow Ban -->
                <button type="button" id="admin-toggle-shadow-ban-btn"
                    data-url="{{ route('admin.ratings.toggle-shadow-ban', $user->id) }}"
                    style="{{ $user->isShadowBanned() ? 'background-color: #7e22ce; color: #ffffff; border-color: #6b21a8;' : 'background-color: #ffffff; color: #374151; border-color: #d1d5db;' }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border shadow-2xs cursor-pointer hover:opacity-90"
                    title="{{ $user->isShadowBanned() ? 'Klik untuk melepaskan Shadow Ban' : 'Aktifkan Shadow Ban (order/bantuan tidak akan masuk/tampil)' }}">
                    <span>👻</span>
                    <span id="shadow-ban-btn-text" class="whitespace-nowrap font-medium" style="{{ $user->isShadowBanned() ? 'color: #ffffff;' : 'color: #374151;' }}">{{ $user->isShadowBanned() ? 'Shadow Banned (Aktif)' : 'Shadow Ban' }}</span>
                </button>

                <button type="button" id="user-ratings-modal-close-btn"
                    class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xs flex-shrink-0">✕</button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 overflow-y-auto space-y-4 flex-1">
            <!-- Shopee Score & Filter Pills Header -->
            <div class="p-4 bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50 rounded-xl border border-amber-200 flex flex-col sm:flex-row sm:items-center gap-4">
                <!-- Score Display -->
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
                        {{ $user->isMitra() ? 'Ulasan Customer' : 'Ulasan Mitra' }}
                    </p>
                </div>

                <!-- Interactive Filter Pills -->
                <div class="flex-1 flex flex-wrap items-center gap-1.5" id="shopee-filter-pills">
                    <button type="button" data-filter="all"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-gray-200 border-gray-400 text-gray-900 font-bold">
                        Semua ({{ $totalCount }})
                    </button>
                    <button type="button" data-filter="5"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50">
                        5 Bintang ({{ $starsCount[5] }})
                    </button>
                    <button type="button" data-filter="4"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50">
                        4 Bintang ({{ $starsCount[4] }})
                    </button>
                    <button type="button" data-filter="3"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50">
                        3 Bintang ({{ $starsCount[3] }})
                    </button>
                    <button type="button" data-filter="2"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-red-600 hover:bg-red-50">
                        2 Bintang ({{ $starsCount[2] }})
                    </button>
                    <button type="button" data-filter="1"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-red-600 hover:bg-red-50">
                        1 Bintang ({{ $starsCount[1] }})
                    </button>
                    <button type="button" data-filter="with_comment"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50">
                        Komentar ({{ $withCommentCount }})
                    </button>
                    <button type="button" data-filter="anonymous"
                        class="shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50">
                        Anonim Publik ({{ $anonymousCount }})
                    </button>
                </div>
            </div>

            <!-- Reviews List Feed -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-[11px] font-bold text-gray-700 uppercase tracking-wider">
                        Daftar Ulasan
                    </h4>
                </div>

                <div id="shopee-reviews-list" class="space-y-2.5">
                    @forelse($ratings as $r)
                        @php
                            $fromUser = $r->rater ?? $r->user;
                            $hasComment = !empty(trim((string)$r->review));
                        @endphp
                        <div class="shopee-review-item p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-2 hover:bg-white hover:border-gray-300 transition"
                            data-rating="{{ $r->rating }}"
                            data-has-comment="{{ $hasComment ? '1' : '0' }}"
                            data-is-anonymous="{{ $r->is_anonymous ? '1' : '0' }}">
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
                                                <span class="px-1.5 py-0.2 text-[9px] font-semibold bg-amber-100 text-amber-800 rounded border border-amber-200" title="Nama disembunyikan dari publik">
                                                    Anonim bagi publik
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400">{{ optional($fromUser)->email ?? '-' }}</div>
                                    </div>
                                </div>

                                <!-- Bintang + Tanggal -->
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
                            Pengguna ini belum pernah menerima ulasan dari pihak lain.
                        </div>
                    @endforelse
                </div>

                <div id="shopee-empty-filter" class="hidden p-6 bg-gray-50 rounded-xl text-center text-xs text-gray-500 border border-gray-200">
                    Tidak ada ulasan pada filter bintang ini.
                </div>
            </div>
        </div>
    </div>
</div>
