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

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">No. HP / WhatsApp</dt>
                        <dd class="font-medium text-gray-900">{{ $user->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kota</dt>
                        <dd class="font-medium text-gray-900">{{ $user->city?->name ?? $user->city_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">NIK</dt>
                        <dd class="font-medium text-gray-900">{{ $user->nik ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jenis Kelamin</dt>
                        <dd class="font-medium text-gray-900">{{ $user->gender ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tempat, Tgl Lahir</dt>
                        <dd class="font-medium text-gray-900">{{ $user->place_of_birth ?? '-' }}, {{ optional($user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null)->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Agama</dt>
                        <dd class="font-medium text-gray-900">{{ $user->religion ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Pekerjaan</dt>
                        <dd class="font-medium text-gray-900">{{ $user->occupation ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-gray-500">Alamat</dt>
                        <dd class="font-medium text-gray-900">{{ $user->address ?? '-' }}</dd>
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
                        <div class="pt-3 border-t border-gray-100">
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

                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Status KTP</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Verifikasi</span>
                            <span
                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $user->verified ? 'Terverifikasi' : 'Belum Verifikasi' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">File KTP</span>
                            <span class="font-medium text-gray-900">
                                {{ ($user->ktp_path || $user->ktp_photo) ? 'Terunggah' : 'Belum ada' }}
                            </span>
                        </div>
                        @php
                            $ktpLink = null;
                            if (!empty($user->ktp_path)) $ktpLink = \Illuminate\Support\Facades\Storage::url($user->ktp_path);
                            elseif (!empty($user->ktp_photo)) $ktpLink = \Illuminate\Support\Facades\Storage::url($user->ktp_photo);
                        @endphp
                        @if ($ktpLink)
                            <div class="mt-2">
                                <a href="{{ $ktpLink }}" target="_blank"
                                    class="inline-flex items-center text-xs text-primary-600 hover:text-primary-700 font-medium">
                                    Lihat file KTP ↗
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection