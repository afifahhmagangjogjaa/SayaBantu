@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        <!-- Statistik Ringkasan -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
            <!-- 1. Pending -->
            <div class="bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 p-4 rounded-2xl border border-amber-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-amber-900/70 truncate">Pending</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-amber-700 leading-tight">{{ $totalPending }}</div>
                </div>
            </div>

            <!-- 2. In Progress -->
            <div class="bg-gradient-to-br from-sky-50/80 via-white to-sky-50/30 p-4 rounded-2xl border border-sky-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 border border-sky-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-sky-900/70 truncate">In Progress</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-sky-700 leading-tight">{{ $totalInProgress }}</div>
                </div>
            </div>

            <!-- 3. Resolved -->
            <div class="bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 p-4 rounded-2xl border border-emerald-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 border border-emerald-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-emerald-900/70 truncate">Resolved</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-emerald-700 leading-tight">{{ $totalResolved }}</div>
                </div>
            </div>

            <!-- 4. Dismissed -->
            <div class="bg-gradient-to-br from-slate-50/80 via-white to-slate-50/30 p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 border border-slate-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-slate-900/70 truncate">Dismissed</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-slate-700 leading-tight">{{ $totalDismissed }}</div>
                </div>
            </div>

            <!-- 5. Dari Customer -->
            <div class="bg-gradient-to-br from-indigo-50/80 via-white to-indigo-50/30 p-4 rounded-2xl border border-indigo-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 border border-indigo-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-indigo-900/70 truncate">Dari Customer</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-indigo-700 leading-tight">{{ $totalFromCustomer }}</div>
                </div>
            </div>

            <!-- 6. Dari Mitra -->
            <div class="bg-gradient-to-br from-purple-50/80 via-white to-purple-50/30 p-4 rounded-2xl border border-purple-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 border border-purple-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-purple-900/70 truncate">Dari Mitra</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-purple-700 leading-tight">{{ $totalFromMitra }}</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-4 sm:p-5 mb-6">
            <form method="GET" action="{{ route('admin.partners.report') }}" class="space-y-4">
                <!-- Header / Title -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0066FF] flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Filter Laporan Aduan</h3>
                    </div>

                    @if (request()->hasAny(['status', 'category', 'report_type', 'search', 'start_date', 'end_date']) && (request('status') !== 'all' || request('category') !== 'all' || request('report_type') !== 'all' || request('search') || request('start_date') || request('end_date')))
                        <a href="{{ route('admin.partners.report') }}" wire:navigate
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 px-2.5 py-1 rounded-lg transition border border-rose-200/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Sedang Ditangani (In Progress)</option>
                            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                            <option value="dismissed" {{ $status === 'dismissed' ? 'selected' : '' }}>Ditolak (Dismissed)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kategori</label>
                        <select name="category" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                            <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="dari_customer" {{ $category === 'dari_customer' ? 'selected' : '' }}>Dari Customer</option>
                            <option value="dari_mitra" {{ $category === 'dari_mitra' ? 'selected' : '' }}>Dari Mitra</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Laporan</label>
                        <select name="report_type" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                            <option value="all" {{ $reportType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            @foreach ($reportTypes as $key => $label)
                                <option value="{{ $key }}" {{ $reportType === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" onclick="this.showPicker()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" onclick="this.showPicker()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                    </div>
                </div>

                <!-- Search and Action Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-1">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari nama reporter, yang dilaporkan, jenis bantuan, atau kata kunci keluhan..."
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] transition">
                        @if($search)
                            <a href="{{ route('admin.partners.report', array_merge(request()->except('search'), ['search' => ''])) }}" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1" title="Hapus pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button type="submit"
                            class="h-[42px] px-6 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0040A8] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Laporan Aduan</h2>
                    <p class="text-xs text-gray-500 mt-1">Total {{ $reports->total() }} laporan ditemukan.</p>
                </div>
            </div>

            @if ($reports->isEmpty())
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">Belum ada laporan aduan.</p>
                    <p class="text-gray-400 text-xs mt-1">Laporan aduan dari customer dan mitra akan muncul di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pelapor</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Dilaporkan</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($reports as $report)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-2.5 py-2.5 whitespace-nowrap text-xs text-gray-700">
                                        <div class="font-medium">{{ $report->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $report->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        @if ($report->reporter)
                                            <a href="{{ route('admin.users.show', $report->reporter) }}" wire:navigate
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[140px]">
                                                {{ $report->reporter->name }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $report->reporter->email }}</div>
                                        @elseif ($report->user)
                                            <a href="{{ route('admin.users.show', $report->user) }}" wire:navigate
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[140px]">
                                                {{ $report->user->name }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $report->user->email }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        @if ($report->reportedUser)
                                            <a href="{{ route('admin.users.show', $report->reportedUser) }}" wire:navigate
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[140px]">
                                                {{ $report->reportedUser->name }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $report->reportedUser->email }}</div>
                                        @elseif ($report->reported_user_text)
                                            <div class="text-xs font-semibold text-gray-900 truncate max-w-[140px]">{{ $report->reported_user_text }}</div>
                                        @elseif ($report->reportedHelp)
                                            <a href="#" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[140px]">
                                                Bantuan #{{ $report->reportedHelp->id }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ Str::limit($report->reportedHelp->title, 25) }}</div>
                                        @elseif ($report->reported_help_text)
                                            <div class="text-xs font-semibold text-gray-900 truncate max-w-[140px]">{{ $report->reported_help_text }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $report->report_type_label }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap">
                                        <span
                                            class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $report->isFromCustomer() ? 'bg-primary-50 text-primary-700 border border-primary-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $report->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                        @if ($report->status === 'pending')
                                            <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                Pending
                                            </span>
                                        @elseif ($report->status === 'in_progress')
                                            <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                                In Progress
                                            </span>
                                        @elseif ($report->status === 'resolved')
                                            <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                                                Resolved
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                                Dismissed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2.5 py-2.5 whitespace-nowrap text-center text-xs">
                                        <a href="{{ route('admin.partners.reports.show', $report) }}" wire:navigate
                                            class="inline-flex items-center px-2.5 py-1 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 transition shadow-2xs">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($reports->hasPages())
                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-center">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
