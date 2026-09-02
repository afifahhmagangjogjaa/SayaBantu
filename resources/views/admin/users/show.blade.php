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
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Profil Pengguna</h2>
                        <p class="text-xs text-gray-500 mt-1">Informasi dasar akun dan kota operasional.</p>
                    </div>
                    <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'mitra' ? 'bg-green-100 text-green-800' : ($user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700') }}">
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

                @if(in_array($user->role, ['kustomer', 'customer']))
                    <div class="mt-6 border-t border-gray-100 pt-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <span>⭐ Penilaian & Ulasan Mitra</span>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-purple-100 text-purple-800 rounded-full">Internal Admin</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Rating rahasia dari mitra untuk evaluasi customer ini.</p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center gap-1">
                                    <span class="text-lg font-bold text-yellow-600">{{ $user->customer_average_rating }}</span>
                                    <span class="text-xs text-gray-400">/ 5.0</span>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div class="text-[11px] text-gray-500">{{ $user->customer_rating_count }} ulasan</div>
                            </div>
                        </div>

                        @php
                            $customerRatings = $user->customerRatings()->with(['rater', 'help'])->latest()->get();
                        @endphp

                        @if($customerRatings->count() > 0)
                            <div class="space-y-3">
                                @foreach($customerRatings as $cr)
                                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 text-xs">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-semibold text-gray-900">{{ optional($cr->rater)->name ?? 'Mitra' }}</span>
                                            <div class="flex items-center gap-1">
                                                <div class="flex text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3.5 h-3.5 {{ $i <= $cr->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-gray-400 text-[10px] ml-1">{{ $cr->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        @if($cr->help)
                                            <div class="text-[11px] text-primary-600 mb-1 font-medium">Bantuan: {{ $cr->help->title }}</div>
                                        @endif
                                        @if($cr->review)
                                            <div class="text-gray-700 bg-white p-2.5 rounded-lg border border-gray-100 italic">"{{ $cr->review }}"</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 rounded-xl text-center text-xs text-gray-500">
                                Belum ada penilaian atau ulasan dari mitra untuk customer ini.
                            </div>
                        @endif
                    </div>
                @endif
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
                        @if ($user->ktp_path || $user->ktp_photo)
                            <div class="mt-2">
                                <a href="{{ Storage::url($user->ktp_path ?? $user->ktp_photo) }}" target="_blank"
                                    class="inline-flex items-center text-xs text-primary-600 hover:text-primary-700">
                                    Lihat file KTP
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection                                 </div>
                                        </div>
                                        @if($mr->help)
                                            <div class="text-[11px] text-primary-600 mb-1 font-medium">Bantuan: {{ $mr->help->title }}</div>
                                        @endif
                                        @if($mr->review)
                                            <div class="text-gray-700 bg-white p-2.5 rounded-lg border border-gray-100 italic">"{{ $mr->review }}"</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 rounded-xl text-center text-xs text-gray-500">
                                Belum ada ulasan dari customer untuk mitra ini.
                            </div>
                        @endif
                    </div>
                @endif
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
                        @if ($user->ktp_path || $user->ktp_photo)
                            <div class="mt-2">
                                <a href="{{ Storage::url($user->ktp_path ?? $user->ktp_photo) }}" target="_blank"
                                    class="inline-flex items-center text-xs text-primary-600 hover:text-primary-700">
                                    Lihat file KTP
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection