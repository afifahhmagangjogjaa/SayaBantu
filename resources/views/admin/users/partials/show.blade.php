<div id="user-detail-modal-root" class="fixed inset-0 overflow-y-auto" style="z-index: 999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity cursor-pointer" id="modal-backdrop"></div>

    <!-- Centering wrapper with safe padding so modal never clips behind navbar -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <!-- Modal Card -->
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[85vh] sm:max-h-[88vh] border border-gray-100 text-left my-auto animate-in fade-in zoom-in duration-200">
            
            <!-- Modal Header (Pinned at top) -->
            <div class="px-6 sm:px-7 py-5 border-b border-gray-100 flex items-center justify-between bg-white flex-shrink-0">
                <div class="flex items-center gap-4 sm:gap-5 min-w-0" style="gap: 18px;">
                    <!-- Avatar Icon -->
                    <div class="w-12 h-12 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm ring-4 ring-primary-50" style="width: 48px; height: 48px; min-width: 48px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <!-- User Details -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2.5 flex-wrap" style="gap: 10px;">
                            <h3 id="modal-title" class="text-base sm:text-lg font-bold text-gray-900 leading-tight truncate">{{ $user->name }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->isMitra() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $user->isMitra() ? 'Mitra' : 'Customer' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">{{ $user->email }}</p>
                    </div>
                </div>
                <!-- Close Button -->
                <button type="button" id="modal-close-btn" class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-sm flex-shrink-0 ml-4 shadow-2xs cursor-pointer" title="Tutup Pop-up">
                    ✕
                </button>
            </div>

            <!-- Modal Body (Scrollable with comfortable padding) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1 text-sm bg-gray-50/50">
            @php
                use Illuminate\Support\Facades\Storage;
                $ktpUrl = $user->ktp_url ?? null;
                if (!$ktpUrl) {
                    if (!empty($user->ktp_path)) $ktpUrl = Storage::url($user->ktp_path);
                    elseif (!empty($user->ktp_photo)) $ktpUrl = Storage::url($user->ktp_photo);
                }
                $selfieUrl = $user->selfie_url ?? null;
                if (!$selfieUrl && !empty($user->selfie_photo)) $selfieUrl = Storage::url($user->selfie_photo);

                $avgScore = $user->average_rating ?? 0;
                $ratingTotal = $user->ratings_count ?? 0;
            @endphp

            <!-- 1. Rating & Ulasan Card -->
            <div class="bg-gradient-to-r from-amber-50/90 via-orange-50/50 to-amber-50/90 rounded-2xl p-4 sm:p-5 border border-amber-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-950 uppercase tracking-wider">
                            ⭐ {{ $user->isMitra() ? 'Rating Mitra' : 'Penilaian Customer' }}
                        </span>
                        @if(!$user->isMitra())
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-purple-100 text-purple-800 rounded-full">Internal</span>
                        @endif
                        @if($ratingTotal > 0 && isset($user->rating_badge['text']))
                            <span class="px-2.5 py-0.5 text-[10px] font-semibold rounded-full bg-white border border-amber-200 text-amber-800 shadow-2xs">
                                {{ $user->rating_badge['emoji'] ?? '⭐' }} {{ $user->rating_badge['text'] }}
                            </span>
                        @endif
                    </div>

                    @if($ratingTotal > 0 && $avgScore > 0)
                        <div class="flex items-center gap-2.5 pt-0.5">
                            <div class="flex items-center text-amber-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $avgScore >= $i ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-base font-bold text-gray-900">{{ number_format($avgScore, 1) }}</span>
                            <span class="text-xs text-gray-500 font-medium">({{ $ratingTotal }} total ulasan)</span>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada riwayat rating atau ulasan.</p>
                    @endif
                </div>

                <a href="{{ route('admin.ratings.index', ['search' => $user->email ?? $user->name], false) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-black text-xs font-bold rounded-xl shadow-2xs hover:shadow transition flex-shrink-0 cursor-pointer"
                    title="Buka menu Rating & Ulasan">
                    <span>Lihat Ulasan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <!-- 2. Informasi Akun Section -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs space-y-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2.5">
                    Informasi Akun & Kontak
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Status Akun:</span>
                        @if ($user->status === 'blocked')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                Diblokir
                            </span>
                        @elseif ($user->status === 'inactive')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                Nonaktif
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Kota:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $user->city_name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Nomor HP / WhatsApp:</span>
                        <span class="font-semibold text-gray-900 font-mono text-sm">{{ $user->phone ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Terdaftar Sejak:</span>
                        <span class="font-semibold text-gray-900">{{ optional($user->created_at)->format('d M Y H:i') ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Tempat, Tanggal Lahir:</span>
                        <span class="font-semibold text-gray-900">
                            {{ $user->place_of_birth ?? '-' }}{{ $user->date_of_birth ? ', ' . optional(\Carbon\Carbon::parse($user->date_of_birth))->format('d M Y') : '' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Jenis Kelamin:</span>
                        <span class="font-semibold text-gray-900">{{ $user->gender ? ucfirst($user->gender) : '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. Data KTP & Verifikasi Section -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                        Data Identitas & KTP
                    </h4>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->verified ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                        {{ $user->verified ? '✓ Terverifikasi' : 'Belum Verifikasi' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Nomor Induk Kependudukan (NIK):</span>
                        <span class="font-semibold text-gray-900 font-mono text-sm">{{ $user->nik ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Status Verifikasi KTP:</span>
                        <span class="font-semibold {{ $user->verified ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $user->verified ? 'Sudah Diverifikasi Admin' : 'Menunggu / Belum Diverifikasi' }}
                        </span>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="block text-gray-400 font-medium mb-1">Alamat Lengkap KTP:</span>
                        <div class="p-3 bg-gray-50 rounded-xl text-gray-800 font-medium border border-gray-100 leading-relaxed">
                            {{ $user->address ?? '-' }}
                        </div>
                    </div>
                </div>

                <!-- Foto KTP & Selfie Berdampingan dengan Preview Rapi -->
                @if(!empty($ktpUrl) || !empty($selfieUrl))
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-600 mb-3">Dokumen Foto</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($ktpUrl))
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200/80 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-700">Foto KTP</span>
                                        <a href="{{ $ktpUrl }}" target="_blank" class="text-primary-600 hover:text-primary-700 font-semibold hover:underline">Buka Gambar ↗</a>
                                    </div>
                                    <a href="{{ $ktpUrl }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-white group shadow-2xs">
                                        <img src="{{ $ktpUrl }}" alt="Foto KTP" class="w-full h-40 object-contain p-1 group-hover:scale-105 transition duration-200" />
                                    </a>
                                </div>
                            @endif

                            @if(!empty($selfieUrl))
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200/80 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-700">Foto Selfie</span>
                                        <a href="{{ $selfieUrl }}" target="_blank" class="text-primary-600 hover:text-primary-700 font-semibold hover:underline">Buka Gambar ↗</a>
                                    </div>
                                    <a href="{{ $selfieUrl }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-white group shadow-2xs">
                                        <img src="{{ $selfieUrl }}" alt="Foto Selfie" class="w-full h-40 object-contain p-1 group-hover:scale-105 transition duration-200" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 bg-white text-right flex-shrink-0 flex items-center justify-end gap-3">
            <button type="button" id="modal-close-btn-2"
                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition shadow-2xs cursor-pointer">
                Tutup
            </button>
        </div>
        </div>
    </div>
</div>