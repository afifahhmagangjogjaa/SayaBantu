<div id="user-detail-modal-root" class="fixed inset-0 overflow-y-auto" style="z-index: 999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity cursor-pointer" id="modal-backdrop"></div>

    <!-- Centering wrapper with safe padding so modal never clips behind navbar -->
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <!-- Modal Card -->
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl max-w-2xl sm:max-w-3xl w-full overflow-hidden flex flex-col max-h-[85vh] sm:max-h-[88vh] border border-gray-100 text-left my-auto animate-in fade-in zoom-in duration-200">
            
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
                            @if($user->isShadowBanned())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    👻 Shadow Banned
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">{{ $user->email }}</p>
                    </div>
                </div>
                <!-- Actions & Close Button -->
                <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                    @if($user->isMitra())
                        <form action="{{ route('admin.partners.toggle-shadow-ban', $user->id) }}" method="POST" class="inline-block m-0"
                            onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->isShadowBanned() ? 'melepaskan' : 'menerapkan' }} Shadow Ban pada pengguna {{ $user->name }}?');">
                            @csrf
                            <button type="submit"
                                style="background-color: {{ $user->isShadowBanned() ? '#f3e8ff' : '#ffffff' }}; color: {{ $user->isShadowBanned() ? '#6b21a8' : '#7e22ce' }}; border: 1px solid {{ $user->isShadowBanned() ? '#d8b4fe' : '#c084fc' }};"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-2xs hover:opacity-90 transition cursor-pointer"
                                title="{{ $user->isShadowBanned() ? 'Lepaskan status Shadow Ban agar mitra kembali dapat melihat pesanan' : 'Aktifkan Shadow Ban agar pesanan disembunyikan dari mitra ini' }}">
                                <span>👻</span>
                                <span class="text-[11px] font-bold">{{ $user->isShadowBanned() ? 'Lepas Shadow Ban' : 'Shadow Ban' }}</span>
                            </button>
                        </form>
                    @endif

                    <button type="button" id="modal-close-btn" class="w-8 h-8 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-sm shadow-2xs cursor-pointer" title="Tutup Pop-up">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable with comfortable padding) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1 text-sm bg-gray-50/50">
            @php
                use Illuminate\Support\Facades\Storage;
                $ktpUrl = $user->ktp_url ?? null;
                if (!$ktpUrl) {
                    if (!empty($user->ktp_path)) $ktpUrl = Storage::url($user->ktp_path);
                    elseif (!empty($user->ktp_photo)) $ktpUrl = Storage::url($user->ktp_photo);
                    elseif (!empty($user->registration?->ktp_photo_path)) $ktpUrl = Storage::url($user->registration->ktp_photo_path);
                }
                $selfieUrl = $user->selfie_url ?? null;
                if (!$selfieUrl) {
                    if (!empty($user->selfie_photo)) $selfieUrl = Storage::url($user->selfie_photo);
                    elseif (!empty($user->registration?->selfie_photo_path)) $selfieUrl = Storage::url($user->registration->selfie_photo_path);
                }

                $avgScore = $user->average_rating ?? 0;
                $ratingTotal = $user->ratings_count ?? 0;

                // Fallback data identitas dari registrasi & parsing alamat
                $nik = $user->nik ?: optional($user->registration)->nik;
                $pob = $user->place_of_birth ?: optional($user->registration)->place_of_birth;
                $dob = $user->date_of_birth ?: optional($user->registration)->date_of_birth;
                $gender = $user->gender ?: optional($user->registration)->gender;
                $religion = $user->religion ?: optional($user->registration)->religion;
                $maritalStatus = $user->marital_status ?: optional($user->registration)->marital_status;
                $occupation = $user->occupation ?: optional($user->registration)->occupation;
                $address = $user->address ?: optional($user->registration)->address;

                // Auto parse RT/RW from address if empty
                $rt = $user->rt ?: optional($user->registration)->rt;
                $rw = $user->rw ?: optional($user->registration)->rw;
                if (empty($rt) && !empty($address) && preg_match('/\bRT[\.\s]*0*(\d+)/i', $address, $m)) {
                    $rt = sprintf('%02d', $m[1]);
                }
                if (empty($rw) && !empty($address) && preg_match('/\bRW[\.\s]*0*(\d+)/i', $address, $m)) {
                    $rw = sprintf('%02d', $m[1]);
                }

                if (!empty($rt) && !empty($rw)) {
                    $rtRwFormatted = $rt . ' / ' . $rw;
                } elseif (!empty($rt)) {
                    $rtRwFormatted = 'RT ' . $rt;
                } elseif (!empty($rw)) {
                    $rtRwFormatted = 'RW ' . $rw;
                } else {
                    $rtRwFormatted = '-';
                }

                // Auto parse kelurahan, kecamatan, city, province, postal code if empty
                $kelurahan = $user->kelurahan ?: optional($user->registration)->kelurahan;
                if (empty($kelurahan) && !empty($address) && preg_match('/\b(?:Kelurahan|Kel)\.?\s*([^,]+)/i', $address, $m)) {
                    $kelurahan = trim($m[1]);
                }

                $kecamatan = $user->kecamatan ?: optional($user->registration)->kecamatan;
                if (empty($kecamatan) && !empty($address) && preg_match('/\b(?:Kecamatan|Kec)\.?\s*([^,]+)/i', $address, $m)) {
                    $kecamatan = trim($m[1]);
                }

                $cityName = $user->city_name ?: ($user->city ?: optional($user->registration)->city);
                if (!empty($address) && preg_match('/\b(?:Kota|Kabupaten|Kab)\.?\s*([^,]+)/i', $address, $m)) {
                    $parsedCity = trim($m[1]);
                    if (empty($cityName) || strcasecmp($cityName, 'Jakarta') === 0) {
                        $cityName = $parsedCity;
                    }
                }

                $province = $user->province ?: optional($user->registration)->province;
                if (empty($province) && !empty($address) && preg_match('/\b(?:DKI\s+[A-Za-z]+|Jawa\s+[A-Za-z]+|Bali|DI\s+[A-Za-z]+|Sumatera\s+[A-Za-z]+)/i', $address, $m)) {
                    $province = trim($m[0]);
                }

                $postalCode = null;
                if (!empty($address) && preg_match('/\b(\d{5})\b/', $address, $m)) {
                    $postalCode = trim($m[1]);
                }
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

            @if($user->isMitra())
                <!-- Riwayat Pembatalan Card -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">🚫 Riwayat Pembatalan Pesanan</span>
                        @php
                            $cancels = $user->cancellations_count ?? 0;
                            $badgeClass = $cancels === 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($cancels <= 2 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-red-50 text-red-700 border-red-200');
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $badgeClass }}">
                            {{ $cancels }}x Batal
                        </span>
                    </div>
                    @if(($user->cancellations_count ?? 0) > 0)
                        <div class="space-y-1.5 mt-2">
                            @foreach(($user->recent_cancellations ?? collect()) as $act)
                                <div class="p-2.5 bg-gray-50 rounded-xl text-xs border border-gray-100 flex items-center justify-between gap-2">
                                    <span class="font-medium text-gray-800">{{ $act->description }}</span>
                                    <span class="text-[10px] text-gray-400 shrink-0">{{ \Carbon\Carbon::parse($act->created_at)->translatedFormat('d M, H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Mitra ini belum pernah membatalkan pesanan (kinerja baik).</p>
                    @endif
                </div>
            @endif

            <!-- Riwayat SP & Sanksi Card -->
            @php
                $currentSp = (int) ($user->warning_level ?? 0);
                $isBanned  = (bool) ($user->is_banned ?? false) || $user->status === 'blocked';

                // Kumpulkan riwayat SP dari notifikasi database
                $spHistory = collect();
                $notifs = isset($sanctionNotifs) ? $sanctionNotifs : $user->notifications()->where('type', \App\Notifications\SanctionNotification::class)->latest()->get();
                foreach ($notifs as $notif) {
                    $data = $notif->data ?? [];
                    $lvl  = (int) ($data['warning_level'] ?? 0);
                    $spHistory->push([
                        'level'  => $lvl,
                        'reason' => $data['reason'] ?? null,
                        'date'   => \Carbon\Carbon::parse($notif->created_at)->translatedFormat('d M Y, H:i'),
                        'type'   => $lvl === 0 ? 'reset' : 'sanction',
                    ]);
                }

                // Fallback jika tidak ada notifikasi, gunakan spLogs dari admin_notes
                if ($spHistory->isEmpty()) {
                    if (isset($spLogs) && $spLogs->isNotEmpty()) {
                        foreach ($spLogs as $log) {
                            $spHistory->push([
                                'level'  => null,
                                'reason' => null,
                                'log'    => $log['log'],
                                'type'   => 'log',
                            ]);
                        }
                    }
                }
            @endphp

            <div class="bg-white rounded-2xl p-4 sm:p-5 border {{ $currentSp > 0 ? 'border-red-200' : 'border-gray-200/80' }} shadow-2xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 {{ $currentSp > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-500' }} rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">Surat Peringatan (SP) & Sanksi</span>
                    </div>

                    {{-- Badge status SP aktif --}}
                    @if ($isBanned)
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-800">🔴 Dibanned</span>
                    @elseif ($currentSp >= 3)
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-800">SP 3 — Berat</span>
                    @elseif ($currentSp === 2)
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-orange-100 text-orange-800">SP 2 — Sedang</span>
                    @elseif ($currentSp === 1)
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-800">SP 1 — Ringan</span>
                    @else
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800">✓ Normal</span>
                    @endif
                </div>

                {{-- Progress bar SP (3 segmen) --}}
                <div class="mb-3">
                    <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                        <span>Level SP Aktif</span>
                        <span>{{ $currentSp }} / 3</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden flex gap-0.5 border border-gray-100">
                        @for ($i = 1; $i <= 3; $i++)
                            <div class="flex-1 h-full rounded-full transition-all {{ $i <= $currentSp ? ($currentSp >= 3 ? 'bg-red-500' : ($currentSp === 2 ? 'bg-orange-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></div>
                        @endfor
                    </div>
                </div>

                {{-- Timeline riwayat SP --}}
                @if ($spHistory->isNotEmpty())
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @foreach ($spHistory as $item)
                            @php
                                $isReset = ($item['type'] ?? '') === 'reset' || ($item['level'] ?? -1) === 0;
                                $isLog   = ($item['type'] ?? '') === 'log';
                                $lvl     = $item['level'] ?? null;
                            @endphp
                            <div class="flex items-start gap-2 p-2.5 rounded-xl text-xs
                                {{ $isReset ? 'bg-green-50 border border-green-100 text-green-800'
                                           : 'bg-red-50/70 border border-red-100 text-red-900' }}">
                                <span class="shrink-0 mt-0.5">
                                    @if ($isReset)
                                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @endif
                                </span>
                                <div class="flex-1 min-w-0">
                                    @if ($isLog)
                                        <p class="break-words leading-snug">{{ $item['log'] }}</p>
                                    @else
                                        <p class="font-semibold leading-tight">
                                            @if ($isReset) SP Dicabut / Reset ke Normal
                                            @else Surat Peringatan{{ $lvl > 0 ? ' SP ' . $lvl : '' }}
                                            @endif
                                        </p>
                                        @if (!empty($item['reason']))
                                            <p class="text-[10px] mt-0.5 opacity-80 break-words">Alasan: {{ $item['reason'] }}</p>
                                        @endif
                                        @if (!empty($item['date']))
                                            <p class="text-[10px] mt-0.5 opacity-60">{{ $item['date'] }} WIB</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Belum pernah kena SP.</p>
                @endif

                {{-- Tautan ke laporan yang ada jika ada spLogs --}}
                @if (isset($spLogs) && $spLogs->isNotEmpty())
                    <div class="mt-3 pt-2.5 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 mb-1.5">Sumber laporan terkait:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($spLogs->unique('report_id') as $log)
                                <a href="{{ route('admin.partners.reports.show', $log['report_id']) }}"
                                   class="inline-flex items-center gap-1 text-[10px] text-primary-600 hover:text-primary-700 font-medium bg-primary-50 hover:bg-primary-100 px-2 py-1 rounded-lg transition">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Laporan #{{ $log['report_id'] }}{{ $log['report_title'] ? ': ' . \Illuminate\Support\Str::limit($log['report_title'], 25) : '' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- 2. Informasi Akun Section -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                        Informasi Akun & Kontak
                    </h4>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->isMitra() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        Role: {{ $user->isMitra() ? 'Mitra' : 'Customer' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-xs">
                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Status Akun:</span>
                        <div class="flex items-center gap-2 flex-wrap">
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

                            @if ($user->status !== 'blocked')
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline-block m-0"
                                    onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->status === 'active' ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer shadow-2xs {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }}">
                                        {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Kota:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $cityName ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Nomor HP / WhatsApp:</span>
                        <span class="font-semibold text-gray-900 font-mono text-sm">{{ $user->phone ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Email:</span>
                        <span class="font-semibold text-gray-900 text-sm break-all">{{ $user->email }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Terdaftar Sejak:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ optional($user->created_at)->format('d M Y H:i') ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Pekerjaan:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $occupation ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. Data KTP & Verifikasi Section (Lengkap Sesuai Super Admin + Tombol ACC KTP) -->
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Data Identitas & KTP Lengkap
                            </h4>
                            <p class="text-[11px] text-gray-400">Data kependudukan untuk validasi dan persetujuan KTP</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->verified ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $user->verified ? '✓ Terverifikasi' : 'Menunggu / Belum Verifikasi' }}
                        </span>

                        @if(!$user->verified)
                            <form action="{{ route('admin.users.verify-ktp', $user->id) }}" method="POST" class="inline-block m-0"
                                onsubmit="return confirm('Apakah Anda yakin ingin menyetujui (ACC) dan memverifikasi KTP pengguna {{ $user->name }}?');">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs transition flex items-center gap-1.5 cursor-pointer"
                                    title="Setujui dan verifikasi KTP pengguna ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>ACC / Setujui KTP</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.reject-ktp', $user->id) }}" method="POST" class="inline-block m-0"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan status verifikasi KTP pengguna {{ $user->name }}?');">
                                @csrf
                                <button type="submit"
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium text-gray-500 hover:text-red-700 hover:bg-red-50 border border-gray-200 hover:border-red-200 transition cursor-pointer"
                                    title="Batalkan verifikasi KTP">
                                    Batalkan Verifikasi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- 1. Data Personal KTP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-xs pt-1">
                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Nomor Induk Kependudukan (NIK):</span>
                        <span class="font-bold text-gray-900 font-mono text-sm tracking-wide">{{ $nik ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Tempat, Tanggal Lahir:</span>
                        <span class="font-semibold text-gray-900 text-sm">
                            {{ $pob ?? '-' }}{{ $dob ? ', ' . optional(\Carbon\Carbon::parse($dob))->format('d M Y') : '' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Jenis Kelamin:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $gender ? ucfirst($gender) : '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Agama:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $religion ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Status Perkawinan:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $maritalStatus ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Pekerjaan:</span>
                        <span class="font-semibold text-gray-900 text-sm">{{ $occupation ?? '-' }}</span>
                    </div>
                </div>

                <!-- 2. Alamat Domisili KTP Lengkap -->
                <div class="pt-4 border-t border-gray-100 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Alamat Domisili KTP
                            </span>
                        </div>
                        <span class="text-[11px] text-gray-400 font-medium">Sesuai KTP Pengguna</span>
                    </div>

                    {{-- Unified Card Alamat --}}
                    <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-200/80 space-y-4 shadow-2xs">
                        <div>
                            <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-1.5">
                                Alamat Jalan / Rumah
                            </span>
                            <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 text-gray-900 text-xs sm:text-sm font-medium leading-relaxed shadow-2xs flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-primary-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="break-words">{{ $address ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Rincian Wilayah Administrasi Bawah Alamat --}}
                        <div>
                            <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                                Rincian Wilayah Administrasi
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                {{-- RT / RW --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>🏷️</span> RT / RW
                                    </span>
                                    <span class="font-bold text-sm block {{ $rtRwFormatted === '-' ? 'text-gray-400' : 'text-primary-700' }}">
                                        {{ $rtRwFormatted }}
                                    </span>
                                </div>

                                {{-- Kelurahan --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>🏘️</span> Kelurahan / Desa
                                    </span>
                                    <span class="font-bold text-gray-900 text-sm block break-words whitespace-normal leading-snug">
                                        {{ $kelurahan ?? '-' }}
                                    </span>
                                </div>

                                {{-- Kecamatan --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>📍</span> Kecamatan
                                    </span>
                                    <span class="font-bold text-gray-900 text-sm block break-words whitespace-normal leading-snug">
                                        {{ $kecamatan ?? '-' }}
                                    </span>
                                </div>

                                {{-- Kota / Kabupaten --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>🏛️</span> Kota / Kabupaten
                                    </span>
                                    <span class="font-bold text-gray-900 text-sm block break-words whitespace-normal leading-snug">
                                        {{ $cityName ?? '-' }}
                                    </span>
                                </div>

                                {{-- Provinsi --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>🗺️</span> Provinsi
                                    </span>
                                    <span class="font-bold text-gray-900 text-sm block break-words whitespace-normal leading-snug">
                                        {{ $province ?? '-' }}
                                    </span>
                                </div>

                                {{-- Kode Pos --}}
                                <div class="bg-white p-3.5 rounded-xl border border-gray-200/80 shadow-2xs hover:border-primary-200 transition">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <span>📮</span> Kode Pos
                                    </span>
                                    <span class="font-bold text-sm block break-words whitespace-normal leading-snug {{ empty($postalCode) ? 'text-gray-400' : 'text-gray-900' }}">
                                        {{ $postalCode ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Foto KTP & Selfie Berdampingan dengan Preview Rapi -->
                @if(!empty($ktpUrl) || !empty($selfieUrl))
                    <div class="pt-4 border-t border-gray-100 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block">
                                    Dokumen Foto KTP & Selfie
                                </span>
                            </div>
                            <span class="text-[11px] text-gray-400 font-medium hidden sm:inline">Klik gambar untuk memperbesar</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($ktpUrl))
                                <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-2xs space-y-3 hover:border-primary-300 transition duration-200">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                            <span class="w-5 h-5 rounded-md bg-blue-50 text-blue-600 inline-flex items-center justify-center text-xs">🪪</span>
                                            <span>Foto KTP Asli</span>
                                        </span>
                                        <a href="{{ $ktpUrl }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition shadow-2xs">
                                            <span>Buka Penuh</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    <a href="{{ $ktpUrl }}" target="_blank"
                                        class="relative block rounded-xl overflow-hidden border border-gray-200/80 bg-slate-900/5 group shadow-inner">
                                        <div class="h-44 sm:h-52 w-full flex items-center justify-center p-2">
                                            <img src="{{ $ktpUrl }}" alt="Foto KTP" class="max-h-full max-w-full object-contain rounded-lg group-hover:scale-105 transition duration-300" />
                                        </div>
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                            <span class="px-3 py-1.5 bg-white/95 text-gray-900 text-xs font-bold rounded-lg shadow-md flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                Perbesar Gambar
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            @if(!empty($selfieUrl))
                                <div class="bg-white p-4 rounded-2xl border border-gray-200/90 shadow-2xs space-y-3 hover:border-primary-300 transition duration-200">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                            <span class="w-5 h-5 rounded-md bg-purple-50 text-purple-600 inline-flex items-center justify-center text-xs">🤳</span>
                                            <span>Foto Selfie dengan KTP</span>
                                        </span>
                                        <a href="{{ $selfieUrl }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition shadow-2xs">
                                            <span>Buka Penuh</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    <a href="{{ $selfieUrl }}" target="_blank"
                                        class="relative block rounded-xl overflow-hidden border border-gray-200/80 bg-slate-900/5 group shadow-inner">
                                        <div class="h-44 sm:h-52 w-full flex items-center justify-center p-2">
                                            <img src="{{ $selfieUrl }}" alt="Foto Selfie" class="max-h-full max-w-full object-contain rounded-lg group-hover:scale-105 transition duration-300" />
                                        </div>
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                            <span class="px-3 py-1.5 bg-white/95 text-gray-900 text-xs font-bold rounded-lg shadow-md flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                Perbesar Gambar
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="pt-3 border-t border-gray-100">
                        <div class="p-3.5 bg-amber-50/80 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-center gap-2.5">
                            <span class="text-base">⚠️</span>
                            <span>Pengguna belum mengunggah dokumen foto KTP atau foto selfie.</span>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-100 bg-white text-right flex-shrink-0">
            <button type="button" id="modal-close-btn-2"
                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition shadow-2xs cursor-pointer">
                Tutup
            </button>
        </div>
        </div>
    </div>
</div>