@php
    $title = 'Activity Logs';
    $breadcrumb = 'Super Admin / Activity Logs';
@endphp

<div>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Aktivitas</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_logs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['today_logs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Admin</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['admin_logs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['customer_logs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Mitra</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['mitra_logs']) }}</p>
                </div>
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari user, aksi, deskripsi..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select wire:model.live="roleFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="all">Semua Role</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="kustomer">Customer</option>
                    <option value="mitra">Mitra</option>
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Aksi</label>
                <select wire:model.live="actionFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="all">Semua Aksi</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button 
                    wire:click="clearFilters"
                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <input 
                    type="date" min="2023-01-01" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                    wire:model.live="dateFrom"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                <input 
                    type="date" min="2023-01-01" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                    wire:model.live="dateTo"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200">
                    <tr>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Role</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-500 font-mono">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-2.5 py-3">
                                <div class="flex items-center gap-3" style="gap: 12px;">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px;">
                                        {{ strtoupper(substr($log->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0" style="margin-left: 4px;">
                                        <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[160px]">{{ $log->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-[160px]">{{ $log->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2.5 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full border 
                                    {{ $log->user->role === 'super_admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : '' }}
                                    {{ $log->user->role === 'admin' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                    {{ in_array($log->user->role, ['customer', 'kustomer']) ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                    {{ $log->user->role === 'mitra' ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}">
                                    {{ in_array($log->user->role, ['customer', 'kustomer']) ? 'Customer' : ucfirst(str_replace('_', ' ', $log->user->role ?? 'unknown')) }}
                                </span>
                            </td>
                            <td class="px-2.5 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ ucfirst(str_replace('_', ' ', $log->activity_type)) }}
                                </span>
                            </td>
                            <td class="px-2.5 py-3 text-xs text-gray-900">
                                <div class="max-w-md line-clamp-2">{{ $log->description }}</div>
                            </td>
                            <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-500 font-mono">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-gray-500 font-medium">Tidak ada activity log ditemukan</p>
                                <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau lakukan pencarian lain</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

