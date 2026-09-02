<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="modal-backdrop"></div>

    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full z-10 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/80 flex-shrink-0">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-base flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="truncate ml-2">
                    <h3 class="text-base font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                </div>
            </div>
            <button type="button" id="modal-close-btn" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-sm flex-shrink-0 ml-2 shadow-2xs">✕</button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-4 flex-1" style="max-height: calc(88vh - 120px);">
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

            <!-- Rating & Kepuasan Card -->
            <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 rounded-xl p-4 border border-amber-200/70">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="text-xs font-semibold text-amber-800 uppercase tracking-wide">
                            {{ $user->role === 'mitra' ? 'Rating & Ulasan Mitra' : 'Penilaian Mitra untuk Customer' }}
                        </div>
                        @if($user->role !== 'mitra')
                            <span class="px-1.5 py-0.5 text-[9px] font-semibold bg-purple-100 text-purple-800 rounded">Internal</span>
                        @endif
                    </div>
                    @if($ratingTotal > 0 && isset($user->rating_badge['text']))
                        <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-white shadow-xs border border-amber-200 text-amber-800">
                            {{ $user->rating_badge['emoji'] ?? '⭐' }} {{ $user->rating_badge['text'] }}
                        </span>
                    @endif
                </div>

                @if($ratingTotal > 0 && $avgScore > 0)
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex items-center text-amber-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $avgScore >= $i ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-base font-bold text-gray-900">{{ number_format($avgScore, 1) }}</span>
                        <span class="text-xs text-gray-500">({{ $ratingTotal }} ulasan)</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-xs text-gray-400">Belum ada ulasan</span>
                    </div>
                @endif

                @php
                    $ratingsList = $user->isMitra()
                        ? $user->mitraRatings()->with(['rater', 'help', 'user'])->latest()->get()
                        : $user->customerRatings()->with(['rater', 'help', 'user'])->latest()->get();
                @endphp

                @if($ratingsList->count() > 0)
                    <div class="mt-3 pt-3 border-t border-amber-200/60 space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($ratingsList as $rItem)
                            @php
                                $raterUser = $rItem->rater ?? $rItem->user;
                            @endphp
                            <div class="p-2.5 bg-white/80 rounded-lg border border-amber-200/50 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-semibold text-gray-900">{{ optional($raterUser)->name ?? ($user->isMitra() ? 'Customer' : 'Mitra') }}</span>
                                        @if(optional($raterUser)->email)
                                            <span class="text-gray-400 text-[10px]">({{ $raterUser->email }})</span>
                                        @endif
                                        @if($rItem->is_anonymous)
                                            <span class="px-1.5 py-0.5 text-[9px] font-medium bg-amber-100 text-amber-800 rounded border border-amber-200">Anonim bagi publik</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= $rItem->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-gray-400 text-[10px] ml-1">{{ $rItem->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                @if($rItem->help)
                                    <div class="text-[10px] text-primary-600 mb-0.5 font-medium">Bantuan: {{ $rItem->help->title }}</div>
                                @endif
                                @if($rItem->review)
                                    <div class="text-gray-700 bg-white p-1.5 rounded border border-gray-100 italic text-[11px]">"{{ $rItem->review }}"</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Info Akun Grid -->
            <div class="grid grid-cols-2 gap-3.5 text-xs bg-gray-50 rounded-xl p-4 border border-gray-100">
                <div>
                    <span class="text-gray-400">Role:</span>
                    <span class="font-semibold text-gray-800 ml-1.5">{{ ucfirst($user->role) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Status Akun:</span>
                    <span class="font-semibold {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }} ml-1.5">{{ ucfirst($user->status) }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Kota:</span>
                    <span class="font-semibold text-gray-800 ml-1.5">{{ $user->city_name ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">No. HP:</span>
                    <span class="font-semibold text-gray-800 ml-1.5">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="col-span-2 pt-1 border-t border-gray-200/50">
                    <span class="text-gray-400">Tempat, Tgl Lahir:</span>
                    <span class="font-medium text-gray-700 ml-1.5">{{ $user->place_of_birth ?? '-' }}, {{ optional($user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null)->format('d M Y') ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Jenis Kelamin:</span>
                    <span class="font-medium text-gray-700 ml-1.5">{{ $user->gender ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400">Terdaftar:</span>
                    <span class="font-medium text-gray-700 ml-1.5">{{ optional($user->created_at)->format('d M Y H:i') }}</span>
                </div>
            </div>

            <!-- KTP Information -->
            <div class="bg-white rounded-xl p-4 border border-gray-100 space-y-3">
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Data KTP & Verifikasi</h4>
                <div class="grid grid-cols-2 gap-3 text-xs text-gray-700">
                    <div>
                        <span class="text-gray-400">NIK:</span>
                        <span class="font-medium ml-1.5">{{ $user->nik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Status KTP:</span>
                        <span class="font-semibold {{ $user->verified ? 'text-green-600' : 'text-yellow-600' }} ml-1.5">{{ $user->verified ? 'Terverifikasi' : 'Belum' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-400">Alamat:</span>
                        <span class="font-medium ml-1.5">{{ $user->address ?? '-' }}</span>
                    </div>
                </div>

                <!-- Foto KTP & Selfie -->
                @if(!empty($ktpUrl) || !empty($selfieUrl))
                    <div class="grid grid-cols-2 gap-4 pt-3.5 border-t border-gray-100">
                        @if(!empty($ktpUrl))
                            <div class="space-y-1.5">
                                <p class="text-xs text-gray-600 font-semibold">Foto KTP</p>
                                <a href="{{ $ktpUrl }}" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 transition bg-gray-50 group shadow-2xs">
                                    <img src="{{ $ktpUrl }}" alt="KTP" class="w-full h-32 sm:h-36 object-contain bg-white group-hover:scale-105 transition duration-200" />
                                </a>
                                <a href="{{ $ktpUrl }}" target="_blank" class="text-xs text-primary-600 hover:underline block text-center mt-1 font-medium">Buka Gambar ↗</a>
                            </div>
                        @endif
                        @if(!empty($selfieUrl))
                            <div class="space-y-1.5">
                                <p class="text-xs text-gray-600 font-semibold">Foto Selfie</p>
                                <a href="{{ $selfieUrl }}" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 hover:border-primary-500 transition bg-gray-50 group shadow-2xs">
                                    <img src="{{ $selfieUrl }}" alt="Selfie" class="w-full h-32 sm:h-36 object-contain bg-white group-hover:scale-105 transition duration-200" />
                                </a>
                                <a href="{{ $selfieUrl }}" target="_blank" class="text-xs text-primary-600 hover:underline block text-center mt-1 font-medium">Buka Gambar ↗</a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-gray-100 bg-gray-50 text-right flex-shrink-0">
            <button type="button" id="modal-close-btn-2"
                class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition shadow-2xs">Tutup</button>
        </div>
    </div>
</div>