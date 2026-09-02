@extends('layouts.admin')

@section('content')

    <div class="space-y-6">

        @php
            $collection = ($activities instanceof \Illuminate\Pagination\AbstractPaginator) ? collect($activities->items()) : collect($activities);
            $roleCounts = [
                'mitra' => $collection->filter(fn($a) => optional($a->user)?->isMitra())->count(),
                'customer' => $collection->filter(fn($a) => optional($a->user)?->isCustomer())->count(),
                'other' => $collection->filter(fn($a) => $a->user && !$a->user->isMitra() && !$a->user->isCustomer())->count(),
            ];
            $totalOnPage = $collection->count();
            $topTypes = $collection->groupBy('activity_type')->map(fn($items) => $items->count())->sortDesc()->take(4);
            $roleMeta = [
                'mitra' => ['label' => 'Mitra', 'badge' => 'bg-primary-50 text-primary-700'],
                'customer' => ['label' => 'Customer', 'badge' => 'bg-amber-100 text-amber-800'],
                'other' => ['label' => 'Internal', 'badge' => 'bg-gray-100 text-gray-700'],
            ];

            // Activity meta and helpers (defined early so views can use them)
            $activityMeta = [
                // Login / Logout
                'login' => ['label' => 'Login Berhasil', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => '🔐'],
                'login_failed' => ['label' => 'Login Gagal', 'badge' => 'bg-red-100 text-red-700', 'icon' => '❌'],
                'logout' => ['label' => 'Logout', 'badge' => 'bg-blue-50 text-blue-700', 'icon' => '📤'],

                // Bantuan
                'take_help' => ['label' => 'Ambil Bantuan', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => '📥'],
                'help_started' => ['label' => 'Mulai Kerjakan Bantuan', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => '▶️'],
                'help_completed' => ['label' => 'Selesaikan Bantuan', 'badge' => 'bg-emerald-200 text-emerald-800', 'icon' => '✔️'],
                'help_cancelled' => ['label' => 'Batalkan Bantuan', 'badge' => 'bg-yellow-100 text-yellow-800', 'icon' => '⏹️'],
                'help_created' => ['label' => 'Customer Membuat Bantuan', 'badge' => 'bg-sky-100 text-sky-700', 'icon' => '🆘'],
                'help_reviewed' => ['label' => 'Customer Menilai Bantuan', 'badge' => 'bg-indigo-100 text-indigo-700', 'icon' => '⭐'],

                // Profil (ungu)
                'profile_updated' => ['label' => 'Update Data Diri', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => '📝'],
                'ktp_reuploaded' => ['label' => 'Upload Ulang KTP', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => '📤'],
                'phone_changed' => ['label' => 'Mengubah Nomor Telepon', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => '📱'],
                'password_changed' => ['label' => 'Mengubah Password', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => '🔑'],

                // Finansial
                'balance_topup' => ['label' => 'Top Up Saldo', 'badge' => 'bg-orange-100 text-orange-700', 'icon' => '💳'],
                'balance_withdraw' => ['label' => 'Tarik Saldo', 'badge' => 'bg-orange-100 text-orange-700', 'icon' => '🏧'],
                'balance_deducted' => ['label' => 'Pengurangan Saldo', 'badge' => 'bg-orange-50 text-orange-700', 'icon' => '➖'],

                // Berbahaya
                'security_bruteforce' => ['label' => 'Banyak Login Gagal', 'badge' => 'bg-red-100 text-red-700', 'icon' => '⚠️'],
                'security_location_anomaly' => ['label' => 'Lokasi Mencurigakan', 'badge' => 'bg-red-100 text-red-700', 'icon' => '📍'],
                'security_outdated_app' => ['label' => 'Aplikasi Versi Lama', 'badge' => 'bg-red-100 text-red-700', 'icon' => '⬇️'],

                // KTP (hijau & merah)
                'ktp_verified' => ['label' => 'KTP Diverifikasi', 'badge' => 'bg-green-100 text-green-700', 'icon' => '🛂'],
                'ktp_rejected' => ['label' => 'KTP Ditolak', 'badge' => 'bg-red-100 text-red-700', 'icon' => '✖️'],
            ];

            $formatActivity = function ($type) use ($activityMeta) {
                if (isset($activityMeta[$type])) {
                    return $activityMeta[$type]['label'];
                }

                return ucwords(str_replace('_', ' ', $type));
            };

            $detectDevice = function ($userAgent) {
                if (!$userAgent) {
                    return ['label' => 'Unknown Device', 'badge' => 'bg-gray-100 text-gray-600'];
                }

                $ua = strtolower($userAgent);

                if (str_contains($ua, 'android') || str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) {
                    return ['label' => 'Mobile Browser', 'badge' => 'bg-green-100 text-green-700'];
                }

                if (str_contains($ua, 'windows')) {
                    return ['label' => 'Chrome Windows', 'badge' => 'bg-blue-100 text-blue-700'];
                }

                if (str_contains($ua, 'mac os') || str_contains($ua, 'macintosh')) {
                    return ['label' => 'MacOS Browser', 'badge' => 'bg-blue-50 text-blue-700'];
                }

                if (str_contains($ua, 'linux')) {
                    return ['label' => 'Linux Browser', 'badge' => 'bg-blue-50 text-blue-700'];
                }

                return ['label' => 'Unknown Device', 'badge' => 'bg-gray-100 text-gray-600'];
            };
        @endphp

        <!-- Top stat cards and live feed removed as requested -->



        <!-- Filter Bar -->
        <div class="mb-6">
            <form method="GET" action="{{ route('admin.partners.activity') }}"
                class="bg-white rounded-2xl shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-700">Filter Aktivitas</p>
                    @if (request()->hasAny(['search', 'type', 'start_date', 'end_date']))
                        <a href="{{ route('admin.partners.activity') }}"
                            class="text-[11px] text-gray-400 hover:text-gray-600 underline">Reset filter</a>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Aktivitas</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama/email mitra, deskripsi, atau IP"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Aktivitas</label>
                        <select name="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">Semua Aktivitas</option>
                            @foreach ($activityTypes as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Periode</label>
                        <div class="flex space-x-2">
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-1/2 px-3 py-2 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-1/2 px-3 py-2 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <p class="text-[11px] text-gray-400">Gunakan kombinasi pencarian, jenis aktivitas, dan periode tanggal
                        untuk mempersempit log aktivitas.</p>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Aktivitas</h2>
                    <p class="text-xs text-gray-500 mt-1">Menampilkan aktivitas terbaru mitra, customer, serta aksi penting lainnya.</p>
                </div>
                <div class="flex items-center space-x-3 self-end sm:self-auto">
                    @if (!$activities->isEmpty())
                        <p class="text-xs text-gray-500 mr-1 hidden md:block">Total {{ $activities->total() }} aktivitas pada halaman ini.</p>
                    @endif
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.partners.activity.export.excel', request()->query()) }}"
                            class="inline-flex items-center px-3.5 py-1.5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-xs">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/>
                            </svg>
                            Excel
                        </a>
                        <a href="{{ route('admin.partners.activity.export.print', request()->query()) }}" target="_blank"
                            class="inline-flex items-center px-3.5 py-1.5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-xs">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                            </svg>
                            PDF / Print
                        </a>
                    </div>
                </div>
            </div>

            @if ($activities->isEmpty())
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 8H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 10.414V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">
                        @if(request()->has('search') || request()->has('type') || request()->has('start_date'))
                            Tidak ada aktivitas yang sesuai dengan filter/pencarian Anda.
                        @else
                            Belum ada aktivitas mitra yang tercatat di sistem.
                        @endif
                    </p>
                    @if(request()->has('search') || request()->has('type') || request()->has('start_date'))
                        <p class="text-gray-400 text-xs mt-1">
                            Coba klik <a href="{{ route('admin.partners.activity') }}" class="underline text-primary-600">Reset filter</a> untuk melihat seluruh aktivitas.
                        </p>
                    @endif
                </div>
            @else
                <div class="px-6 py-3 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-wrap gap-2 items-center text-xs">
                        <span class="text-gray-400 mr-2 text-[11px] font-semibold tracking-wider uppercase">FILTER ROLE CEPAT:</span>
                        <button type="button" data-role-filter-btn data-role-filter="all"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-900 text-white border border-gray-900 shadow-xs transition-all">
                            Semua ({{ $totalOnPage }})
                        </button>
                        <button type="button" data-role-filter-btn data-role-filter="mitra"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 transition-all">
                            Mitra ({{ $roleCounts['mitra'] }})
                        </button>
                        <button type="button" data-role-filter-btn data-role-filter="customer"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 transition-all">
                            Customer ({{ $roleCounts['customer'] }})
                        </button>
                        <button type="button" data-role-filter-btn data-role-filter="other"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 transition-all">
                            Lainnya ({{ $roleCounts['other'] }})
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aktivitas</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider font-mono">IP</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Device</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-20">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($activities as $a)
                                @php
                                    $roleKey = optional($a->user)?->isMitra() ? 'mitra' : (optional($a->user)?->isCustomer() ? 'customer' : 'other');
                                    $roleInfo = $roleMeta[$roleKey] ?? $roleMeta['other'];
                                    $device = $detectDevice($a->user_agent);
                                    $meta = $activityMeta[$a->activity_type] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors" data-role-row data-role="{{ $roleKey }}"
                                    data-activity-type="{{ $a->activity_type }}">
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        @if($a->user)
                                            <a href="{{ route('admin.users.show', $a->user) }}"
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[150px]">
                                                {{ $a->user->name }}
                                            </a>
                                            <p class="text-[10px] text-gray-400 font-normal truncate max-w-[150px]">{{ $a->user->email }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic">User Terhapus</span>
                                        @endif
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $roleInfo['badge'] }}">
                                            {{ $roleInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $meta['badge'] ?? 'bg-gray-100 text-gray-700' }}">
                                            @if (!empty($meta['icon']))
                                                <span class="mr-1">{{ $meta['icon'] }}</span>
                                            @endif
                                            {{ $formatActivity($a->activity_type) }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2.5 text-gray-600 text-xs max-w-xs">
                                        <span class="line-clamp-2">{{ $a->description ?? '-' }}</span>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap text-gray-600 text-xs font-mono">
                                        {{ $a->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-2.5 py-2.5 text-gray-500 text-xs">
                                        <div class="flex flex-col space-y-0.5">
                                            <div>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $device['badge'] }}">
                                                    {{ $device['label'] }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-gray-400 truncate max-w-[140px]" title="{{ $a->user_agent }}">
                                                {{ $a->user_agent ? (str_contains(strtolower($a->user_agent), 'symfony') ? 'Symfony' : \Illuminate\Support\Str::limit($a->user_agent, 25)) : '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap text-xs">
                                        <div class="text-gray-700 font-medium">{{ $a->created_at->diffForHumans() }}</div>
                                        <div class="text-gray-400 text-[10px]">({{ $a->created_at->format('Y-m-d H:i') }})</div>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap text-center text-xs">
                                        <a href="{{ route('admin.partners.activity', array_merge(request()->query(), ['activity_id' => $a->id])) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors shadow-2xs">
                                            <span>Detail</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($activities->hasPages())
                    @php
                        $p = $activities;
                        $current = $p->currentPage();
                        $last = $p->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-center">
                        <nav class="inline-flex items-center space-x-2" role="navigation" aria-label="Pagination">
                            {{-- Previous --}}
                            @if($current > 1)
                                <a href="{{ $p->url($current - 1) }}" class="inline-flex items-center px-3 py-1.5 bg-white border rounded-md text-sm text-gray-700 hover:bg-gray-50">
                                    &larr; Prev
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border rounded-md text-sm text-gray-400">&larr; Prev</span>
                            @endif

                            {{-- First + ellipsis --}}
                            @if($start > 1)
                                <a href="{{ $p->url(1) }}" class="inline-flex items-center px-3 py-1.5 bg-white border rounded-md text-sm text-gray-700 hover:bg-gray-50">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-sm text-gray-500">…</span>
                                @endif
                            @endif

                            {{-- Page numbers --}}
                            @for($i = $start; $i <= $end; $i++)
                                @if($i == $current)
                                    <span aria-current="page" class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white border rounded-md text-sm">{{ $i }}</span>
                                @else
                                    <a href="{{ $p->url($i) }}" class="inline-flex items-center px-3 py-1.5 bg-white border rounded-md text-sm text-gray-700 hover:bg-gray-50">{{ $i }}</a>
                                @endif
                            @endfor

                            {{-- Ellipsis + last --}}
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="px-2 text-sm text-gray-500">…</span>
                                @endif
                                <a href="{{ $p->url($last) }}" class="inline-flex items-center px-3 py-1.5 bg-white border rounded-md text-sm text-gray-700 hover:bg-gray-50">{{ $last }}</a>
                            @endif

                            {{-- Next --}}
                            @if($current < $last)
                                <a href="{{ $p->url($current + 1) }}" class="inline-flex items-center px-3 py-1.5 bg-white border rounded-md text-sm text-gray-700 hover:bg-gray-50">Next &rarr;</a>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 border rounded-md text-sm text-gray-400">Next &rarr;</span>
                            @endif
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if ($selectedActivity)
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-3 z-50"
             onclick="if(event.target === this) window.location.href='{{ route('admin.partners.activity', request()->except('activity_id')) }}'">
            <div class="bg-white w-full max-w-md rounded-xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] text-xs animate-in fade-in zoom-in-95 duration-100">
                <!-- Modal Header -->
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/60">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-bold text-gray-900">Detail Aktivitas</span>
                        <span class="text-[10px] text-gray-400 font-mono">#{{ $selectedActivity->id }}</span>
                    </div>
                    <a href="{{ route('admin.partners.activity', request()->except('activity_id')) }}"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-md hover:bg-gray-200/60 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>

                <!-- Modal Body -->
                <div class="p-4 overflow-y-auto space-y-3">
                    @if ($selectedActivity && $suspicious['flag'])
                        <div class="border border-amber-200 bg-amber-50/80 rounded-lg p-2.5 flex items-start space-x-2">
                            <span class="text-xs mt-0.5">⚠️</span>
                            <div class="flex-1 text-[11px]">
                                <p class="font-semibold text-amber-900">Aktivitas mencurigakan:</p>
                                <ul class="text-[10px] text-amber-800 list-disc ml-3.5 space-y-0.5">
                                    @foreach ($suspicious['reasons'] as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <form method="POST" action="{{ route('admin.partners.toggle', $selectedActivity->user_id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-600 text-white hover:bg-red-700">
                                            Blokir
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('admin.partners.activity.reset_sessions', $selectedActivity->user_id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-800 text-white hover:bg-gray-900">
                                            Reset Sesi
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('admin.partners.activity.reset_password', $selectedActivity->user_id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-600 text-white hover:bg-blue-700">
                                            Reset Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-gray-50/60 border border-gray-100 rounded-lg p-3 space-y-2.5">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold">User</span>
                                <div class="font-bold text-gray-900 text-xs">
                                    {{ $selectedActivity->user->name ?? 'User Terhapus' }}
                                </div>
                                <div class="text-[11px] text-gray-500">{{ $selectedActivity->user->email ?? '-' }}</div>
                            </div>
                            @php
                                $roleKey = optional($selectedActivity->user)?->isMitra() ? 'mitra' : (optional($selectedActivity->user)?->isCustomer() ? 'customer' : 'other');
                                $roleInfo = $roleMeta[$roleKey] ?? $roleMeta['other'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $roleInfo['badge'] }}">
                                {{ $roleInfo['label'] }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
                            <span class="text-[11px] text-gray-500 font-medium">Aktivitas</span>
                            @php
                                $meta = $activityMeta[$selectedActivity->activity_type] ?? null;
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $meta['badge'] ?? 'bg-gray-100 text-gray-700' }}">
                                @if (!empty($meta['icon']))
                                    <span class="mr-1">{{ $meta['icon'] }}</span>
                                @endif
                                {{ $formatActivity($selectedActivity->activity_type) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 pt-2">
                            <span class="text-[11px] text-gray-500 font-medium">Deskripsi</span>
                            <div class="mt-1 text-[11px] text-gray-800 bg-white p-2 rounded border border-gray-200/60 whitespace-pre-line leading-relaxed">
                                {{ $selectedActivity->description ?? '-' }}
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-2 grid grid-cols-2 gap-2 text-[11px]">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold">IP Address</span>
                                <div class="font-mono text-gray-800 font-medium">
                                    {{ $selectedActivity->ip_address ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold">Waktu</span>
                                <div class="text-gray-800 font-medium">
                                    {{ $selectedActivity->created_at->format('Y-m-d H:i:s') }} WIB
                                </div>
                                <div class="text-[10px] text-gray-400">({{ $selectedActivity->created_at->diffForHumans() }})</div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-2">
                            <span class="text-[10px] text-gray-400 uppercase font-semibold">Perangkat & User Agent</span>
                            @php
                                $device = $detectDevice($selectedActivity->user_agent ?? null);
                            @endphp
                            <div class="mt-1 flex flex-col space-y-1">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-semibold {{ $device['badge'] }}">
                                        {{ $device['label'] }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-gray-600 bg-white p-1.5 rounded border border-gray-200/60 break-all font-mono">
                                    {{ $selectedActivity->user_agent ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!$recentActivities->isEmpty())
                        <div>
                            <h3 class="text-[11px] font-bold text-gray-700 mb-1.5">Aktivitas 24 Jam Terakhir</h3>
                            <ul class="space-y-1 max-h-32 overflow-y-auto pr-1">
                                @foreach ($recentActivities as $ra)
                                    @php
                                        $meta = $activityMeta[$ra->activity_type] ?? null;
                                    @endphp
                                    <li class="bg-gray-50 p-1.5 rounded border border-gray-100 flex items-center justify-between text-[10px]">
                                        <div class="truncate mr-2">
                                            <span class="font-medium text-gray-800">
                                                @if (!empty($meta['icon']))
                                                    <span class="mr-0.5">{{ $meta['icon'] }}</span>
                                                @endif
                                                {{ $formatActivity($ra->activity_type) }}
                                            </span>
                                            <span class="text-gray-400"> · {{ \Illuminate\Support\Str::limit($ra->description ?? '-', 25) }}</span>
                                        </div>
                                        <span class="text-gray-400 whitespace-nowrap">
                                            {{ $ra->created_at->format('H:i:s') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50/60 flex justify-end">
                    <a href="{{ route('admin.partners.activity', request()->except('activity_id')) }}"
                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-colors">
                        Tutup
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-role-filter-btn]');
            const rows = document.querySelectorAll('[data-role-row]');

            if (!buttons.length || !rows.length) {
                return;
            }

            const setActiveButton = (current) => {
                buttons.forEach((btn) => {
                    btn.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
                    btn.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200');
                });

                current.classList.add('bg-gray-900', 'text-white', 'border-gray-900');
                current.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-200');
            };

            const applyRoleFilter = (role) => {
                rows.forEach((row) => {
                    const shouldShow = role === 'all' || row.dataset.role === role;
                    row.classList.toggle('hidden', !shouldShow);
                });
            };

            if (buttons[0]) {
                setActiveButton(buttons[0]);
                applyRoleFilter(buttons[0].dataset.roleFilter || 'all');
            }

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const role = btn.dataset.roleFilter || 'all';
                    setActiveButton(btn);
                    applyRoleFilter(role);
                });
            });

            // Close modal on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const closeLink = document.querySelector('a[href*="activity"][href*="activity_id"]');
                    const modal = document.querySelector('.fixed.inset-0.z-50');
                    if (modal) {
                        const url = new URL(window.location.href);
                        url.searchParams.delete('activity_id');
                        window.location.href = url.toString();
                    }
                }
            });
        });
    </script>
@endpush