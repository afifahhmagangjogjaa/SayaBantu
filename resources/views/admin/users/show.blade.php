@extends('layouts.admin')

@section('content')
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-8 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pengguna</h1>
                <p class="text-sm text-gray-600 mt-1">Tinjau informasi akun, status KTP, dan status keamanan pengguna.</p>
            </div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.users.index') }}"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                &larr; Kembali ke daftar
            </a>
        </div>
    </div>

    <div class="p-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-200 p-6 space-y-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Profil Pengguna</h2>
                        <p class="text-xs text-gray-500 mt-1">Informasi dasar akun dan kota operasional.</p>
                    </div>
                    <span
                        class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->isMitra() ? 'bg-green-100 text-green-800' : ($user->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                @php
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
                    if (empty($rt) && !empty($address) && preg_match('/RT[\.\s]*0*(\d+)/i', $address, $m)) {
                        $rt = sprintf('%02d', $m[1]);
                    }
                    if (empty($rw) && !empty($address) && preg_match('/RW[\.\s]*0*(\d+)/i', $address, $m)) {
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

                    // Auto parse kelurahan, kecamatan, city, province if empty
                    $kelurahan = $user->kelurahan ?: optional($user->registration)->kelurahan;
                    if (empty($kelurahan) && !empty($address) && preg_match('/\b(?:Kelurahan|Kel)\.?\s*([^,]+)/i', $address, $m)) {
                        $kelurahan = trim($m[1]);
                    }

                    $kecamatan = $user->kecamatan ?: optional($user->registration)->kecamatan;
                    if (empty($kecamatan) && !empty($address) && preg_match('/\b(?:Kecamatan|Kec)\.?\s*([^,]+)/i', $address, $m)) {
                        $kecamatan = trim($m[1]);
                    }

                    $cityName = $user->city_name ?: ($user->city?->name ?: ($user->city ?: optional($user->registration)->city));
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

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 text-xs">Nama Lengkap</dt>
                        <dd class="font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">No. HP / WhatsApp</dt>
                        <dd class="font-medium text-gray-900 font-mono">{{ $user->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Kota Operasional</dt>
                        <dd class="font-medium text-gray-900">{{ $cityName ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">NIK</dt>
                        <dd class="font-medium text-gray-900 font-mono">{{ $nik ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Jenis Kelamin</dt>
                        <dd class="font-medium text-gray-900">{{ $gender ? ucfirst($gender) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Tempat, Tgl Lahir</dt>
                        <dd class="font-medium text-gray-900">{{ $pob ?? '-' }}{{ $dob ? ', ' . optional(\Carbon\Carbon::parse($dob))->format('d M Y') : '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Agama</dt>
                        <dd class="font-medium text-gray-900">{{ $religion ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Status Perkawinan</dt>
                        <dd class="font-medium text-gray-900">{{ $maritalStatus ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs">Pekerjaan</dt>
                        <dd class="font-medium text-gray-900">{{ $occupation ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-gray-500 text-xs mb-1">Alamat Domisili KTP</dt>
                        <dd class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-200/80 space-y-3">
                            <div class="bg-white p-3 rounded-lg border border-gray-200/80 text-gray-900 text-xs sm:text-sm font-medium leading-relaxed shadow-2xs flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>{{ $address ?? '-' }}</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">🏷️ RT / RW</span>
                                    <span class="font-bold text-sm block {{ $rtRwFormatted === '-' ? 'text-gray-400' : 'text-primary-700' }}">{{ $rtRwFormatted }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">🏘️ Kelurahan / Desa</span>
                                    <span class="font-bold text-gray-900 text-sm block break-words leading-snug">{{ $kelurahan ?? '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">📍 Kecamatan</span>
                                    <span class="font-bold text-gray-900 text-sm block break-words leading-snug">{{ $kecamatan ?? '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">🏛️ Kota / Kabupaten</span>
                                    <span class="font-bold text-gray-900 text-sm block break-words leading-snug">{{ $cityName ?? '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">🗺️ Provinsi</span>
                                    <span class="font-bold text-gray-900 text-sm block break-words leading-snug">{{ $province ?? '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl border border-gray-200/80 shadow-2xs">
                                    <span class="block text-gray-400 text-[10px] font-semibold uppercase tracking-wider mb-1">📮 Kode Pos</span>
                                    <span class="font-bold text-sm block break-words leading-snug {{ empty($postalCode) ? 'text-gray-400' : 'text-gray-900' }}">{{ $postalCode ?? '-' }}</span>
                                </div>
                            </div>
                        </dd>
                    </div>
                </dl>

                @php
                    $avgScore = $user->average_rating ?? 0;
                    $ratingTotal = $user->ratings_count ?? 0;
                @endphp

                <!-- Rating Summary Box -->
                <div class="border-t border-gray-100 pt-5">
                    <div class="bg-gradient-to-r from-amber-50 via-orange-50/50 to-amber-50 rounded-xl p-4 border border-amber-200/80 flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    {{ $user->isMitra() ? 'Rating Mitra' : 'Rating Customer' }}
                                </span>
                                @if(!$user->isMitra())
                                    <span class="px-1.5 py-0.5 text-[9px] font-semibold bg-purple-100 text-purple-800 rounded">Internal</span>
                                @endif
                                @if($ratingTotal > 0 && isset($user->rating_badge['text']))
                                    <span class="px-2.5 py-0.5 text-[10px] font-semibold rounded-full bg-white border border-amber-200 text-amber-800 shadow-2xs">
                                        {{ $user->rating_badge['emoji'] ?? '⭐' }} {{ $user->rating_badge['text'] }}
                                    </span>
                                @endif
                            </div>
                            @if($ratingTotal > 0 && $avgScore > 0)
                                <div class="flex items-center gap-2">
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
                                <span class="text-xs text-gray-400 italic">Belum ada ulasan</span>
                            @endif
                        </div>

                        <a href="{{ route('admin.ratings.index', ['search' => $user->email ?? $user->name], false) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-black text-xs font-semibold rounded-xl shadow-2xs transition flex-shrink-0 cursor-pointer"
                            title="Buka ulasan lengkap di menu Rating & Ulasan">
                            <span>Lihat Ulasan di Menu Rating</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Status Akun</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Status</span>
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'blocked' ? 'bg-red-100 text-red-800' : ($user->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Blokir</span>
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_blocked ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->is_blocked ? 'Diblokir' : 'Tidak diblokir' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Shadow Ban</span>
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->isShadowBanned() ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->isShadowBanned() ? '👻 Shadow Banned' : 'Normal' }}
                            </span>
                        </div>
                        <div class="pt-3 border-t border-gray-100 space-y-2">
                            <form action="{{ route('admin.partners.toggle-shadow-ban', $user->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->isShadowBanned() ? 'melepaskan' : 'menerapkan' }} Shadow Ban pada pengguna {{ $user->name }}?');">
                                @csrf
                                <button type="submit"
                                    style="background-color: {{ $user->isShadowBanned() ? '#faf5ff' : '#7e22ce' }}; color: {{ $user->isShadowBanned() ? '#6b21a8' : '#ffffff' }}; border: 1.5px solid {{ $user->isShadowBanned() ? '#c084fc' : '#6b21a8' }};"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 hover:opacity-90 cursor-pointer shadow-2xs">
                                    <span>👻</span>
                                    <span>{{ $user->isShadowBanned() ? 'Lepas Shadow Ban' : 'Aktifkan Shadow Ban' }}</span>
                                </button>
                            </form>
                            @if ($user->status !== 'blocked')
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->status === 'active' ? 'menonaktifkan' : 'mengaktifkan kembali' }} akun {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer shadow-2xs {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }}">
                                        {{ $user->status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.partners.toggle', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold {{ $user->is_blocked ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                                    {{ $user->is_blocked ? 'Buka Blokir Pengguna' : 'Blokir Pengguna' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if($user->isMitra())
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800">Riwayat Pembatalan</h3>
                            @php
                                $cancels = $user->cancellations_count ?? 0;
                                $badgeColor = $cancels === 0 ? 'bg-emerald-100 text-emerald-800' : ($cancels <= 2 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800');
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $badgeColor }}">
                                {{ $cancels }}x Batal
                            </span>
                        </div>
                        @if(($user->cancellations_count ?? 0) > 0)
                            <div class="space-y-2 mt-2">
                                <p class="text-xs text-gray-500 font-medium">Pembatalan Terakhir:</p>
                                @foreach(($user->recent_cancellations ?? collect()) as $act)
                                    <div class="p-2.5 bg-gray-50 rounded-lg text-xs border border-gray-100">
                                        <p class="font-medium text-gray-800">{{ $act->description }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($act->created_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">Mitra ini belum pernah membatalkan pesanan (performa bagus).</p>
                        @endif
                    </div>
                @endif

                {{-- ===== Riwayat Surat Peringatan (SP) ===== --}}
                @php
                    $currentSp = (int) ($user->warning_level ?? 0);
                    $isBanned  = (bool) ($user->is_banned ?? false) || $user->status === 'blocked';

                    // Kumpulkan riwayat SP dari notifikasi database
                    $spHistory = collect();
                    foreach ($sanctionNotifs as $notif) {
                        $data = $notif->data ?? [];
                        $lvl  = (int) ($data['warning_level'] ?? 0);
                        $spHistory->push([
                            'level'  => $lvl,
                            'reason' => $data['reason'] ?? null,
                            'date'   => \Carbon\Carbon::parse($notif->created_at)->translatedFormat('d M Y, H:i'),
                            'type'   => $lvl === 0 ? 'reset' : 'sanction',
                        ]);
                    }

                    // Juga tambahkan dari spLogs (fallback dari admin_notes) jika notifikasi kosong
                    if ($spHistory->isEmpty() && $spLogs->isNotEmpty()) {
                        foreach ($spLogs as $log) {
                            $spHistory->push([
                                'level'  => null,
                                'reason' => null,
                                'log'    => $log['log'],
                                'type'   => 'log',
                            ]);
                        }
                    }
                @endphp

                <div class="bg-white rounded-2xl shadow-md border {{ $currentSp > 0 ? 'border-red-200' : 'border-gray-200' }} p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 {{ $currentSp > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-500' }} rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            <h3 class="text-sm font-semibold text-gray-800">Riwayat SP & Sanksi</h3>
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

                    {{-- Progress bar SP --}}
                    <div class="mb-3">
                        <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                            <span>Level SP Aktif</span>
                            <span>{{ $currentSp }} / 3</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden flex gap-0.5">
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="flex-1 h-full rounded-full transition-all {{ $i <= $currentSp ? ($currentSp >= 3 ? 'bg-red-500' : ($currentSp === 2 ? 'bg-orange-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></div>
                            @endfor
                        </div>
                    </div>

                    {{-- Riwayat SP dari notifikasi / log --}}
                    @if ($spHistory->isNotEmpty())
                        <div class="space-y-1.5 mt-3 max-h-52 overflow-y-auto pr-1">
                            @foreach ($spHistory as $item)
                                @php
                                    $isReset = ($item['type'] ?? '') === 'reset' || ($item['level'] ?? -1) === 0;
                                    $isLog   = ($item['type'] ?? '') === 'log';
                                    $lvl     = $item['level'] ?? null;
                                @endphp
                                <div class="flex items-start gap-2 p-2 rounded-lg text-xs
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
                                                @else Surat Peringatan {{ $lvl > 0 ? 'SP ' . $lvl : '' }}
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
                        <p class="text-xs text-gray-400 italic mt-2">Belum ada riwayat sanksi/SP untuk pengguna ini.</p>
                    @endif

                    {{-- Tautan ke laporan yang ada --}}
                    @if ($spLogs->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-[10px] text-gray-400 mb-1.5">Sumber laporan terkait:</p>
                            @foreach ($spLogs->unique('report_id') as $log)
                                <a href="{{ route('admin.partners.reports.show', $log['report_id']) }}"
                                   class="inline-flex items-center gap-1 text-[10px] text-primary-600 hover:text-primary-700 font-medium bg-primary-50 hover:bg-primary-100 px-2 py-1 rounded-lg transition mr-1 mb-1">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Laporan #{{ $log['report_id'] }}{{ $log['report_title'] ? ': ' . \Illuminate\Support\Str::limit($log['report_title'], 25) : '' }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800">Status & Verifikasi KTP</h3>
                        <span
                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $user->verified ? '✓ Terverifikasi' : 'Menunggu / Belum' }}
                        </span>
                    </div>

                    <div class="space-y-3 text-sm">
                        @if(!$user->verified)
                            <form action="{{ route('admin.users.verify-ktp', $user->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menyetujui (ACC) dan memverifikasi KTP pengguna {{ $user->name }}?');">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>ACC / Setujui KTP</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.reject-ktp', $user->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan status verifikasi KTP pengguna {{ $user->name }}?');">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2 border border-gray-200 text-gray-600 hover:text-red-700 hover:bg-red-50 rounded-xl text-xs font-medium transition cursor-pointer">
                                    Batalkan Verifikasi KTP
                                </button>
                            </form>
                        @endif

                        @php
                            $ktpFile = null;
                            $candidates = array_filter([
                                $user->ktp_photo,
                                $user->ktp_path,
                                optional($user->registration)->ktp_photo_path,
                            ]);
                            foreach ($candidates as $cand) {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cand) || file_exists(public_path('storage/' . $cand))) {
                                    $ktpFile = $cand;
                                    break;
                                }
                            }
                            if (!$ktpFile && !empty($candidates)) {
                                $ktpFile = reset($candidates);
                            }
                            $ktpLink = $ktpFile ? asset('storage/' . $ktpFile) : null;

                            $selfieFile = null;
                            $selfieCandidates = array_filter([
                                $user->selfie_photo,
                                optional($user->registration)->selfie_photo_path,
                            ]);
                            foreach ($selfieCandidates as $scand) {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($scand) || file_exists(public_path('storage/' . $scand))) {
                                    $selfieFile = $scand;
                                    break;
                                }
                            }
                            if (!$selfieFile && !empty($selfieCandidates)) {
                                $selfieFile = reset($selfieCandidates);
                            }
                            $selfieLink = $selfieFile ? asset('storage/' . $selfieFile) : null;
                        @endphp

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <span class="text-gray-500">File Dokumen:</span>
                            <span class="font-medium text-gray-900">
                                {{ ($ktpLink || $selfieLink) ? 'Terunggah' : 'Belum ada' }}
                            </span>
                        </div>

                        @if ($ktpLink || $selfieLink)
                            <div class="pt-2 border-t border-gray-100 space-y-3">
                                @if ($ktpLink)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-700">Foto KTP</span>
                                            <a href="{{ $ktpLink }}" target="_blank"
                                                class="text-primary-600 hover:text-primary-700 font-semibold text-xs hover:underline">
                                                Buka KTP ↗
                                            </a>
                                        </div>
                                        <a href="{{ $ktpLink }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-white group shadow-2xs">
                                            <img src="{{ $ktpLink }}" alt="Foto KTP" class="w-full h-32 object-contain p-1 group-hover:scale-105 transition duration-200" />
                                        </a>
                                    </div>
                                @endif

                                @if ($selfieLink)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-700">Foto Selfie</span>
                                            <a href="{{ $selfieLink }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-700 font-semibold text-xs hover:underline">
                                                Buka Selfie ↗
                                            </a>
                                        </div>
                                        <a href="{{ $selfieLink }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-white group shadow-2xs">
                                            <img src="{{ $selfieLink }}" alt="Foto Selfie" class="w-full h-32 object-contain p-1 group-hover:scale-105 transition duration-200" />
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-amber-600 italic">User belum mengunggah foto KTP atau foto selfie.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection