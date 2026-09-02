@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        <!-- Statistik Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">Pending</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $totalPending }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">In Progress</div>
                <div class="text-2xl font-bold text-blue-600">{{ $totalInProgress }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">Resolved</div>
                <div class="text-2xl font-bold text-green-600">{{ $totalResolved }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">Dismissed</div>
                <div class="text-2xl font-bold text-gray-600">{{ $totalDismissed }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">Dari Customer</div>
                <div class="text-2xl font-bold text-primary-600">{{ $totalFromCustomer }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-500 mb-1">Dari Mitra</div>
                <div class="text-2xl font-bold text-amber-600">{{ $totalFromMitra }}</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.partners.report') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>Sedang Ditangani (In Progress)</option>
                            <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                            <option value="dismissed" {{ $status === 'dismissed' ? 'selected' : '' }}>Ditolak (Dismissed)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Kategori</label>
                        <select name="category" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="dari_customer" {{ $category === 'dari_customer' ? 'selected' : '' }}>Dari Customer</option>
                            <option value="dari_mitra" {{ $category === 'dari_mitra' ? 'selected' : '' }}>Dari Mitra</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Jenis Laporan</label>
                        <select name="report_type" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                            <option value="all" {{ $reportType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            @foreach ($reportTypes as $key => $label)
                                <option value="{{ $key }}" {{ $reportType === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" onclick="this.showPicker()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()" onclick="this.showPicker()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Cari</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari nama reporter, yang dilaporkan, jenis bantuan, atau kata kunci keluhan..."
                                class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @if($search)
                                <a href="{{ route('admin.partners.report', array_merge(request()->except('search'), ['search' => ''])) }}" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-end space-x-2 pt-6">
                        <button type="submit"
                            class="px-6 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                            Filter
                        </button>
                        @if (request()->hasAny(['status', 'category', 'report_type', 'search', 'start_date', 'end_date']) && (request('status') !== 'all' || request('category') !== 'all' || request('report_type') !== 'all' || request('search') || request('start_date') || request('end_date')))
                            <a href="{{ route('admin.partners.report') }}"
                                class="px-6 py-2.5 bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-300 transition">
                                Reset
                            </a>
                        @endif
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
                                            <a href="{{ route('admin.users.show', $report->reporter) }}"
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline block truncate max-w-[140px]">
                                                {{ $report->reporter->name }}
                                            </a>
                                            <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $report->reporter->email }}</div>
                                        @elseif ($report->user)
                                            <a href="{{ route('admin.users.show', $report->user) }}"
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
                                            <a href="{{ route('admin.users.show', $report->reportedUser) }}"
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
                                        <a href="{{ route('admin.partners.reports.show', $report) }}"
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
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-center">
                        {{ $reports->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
