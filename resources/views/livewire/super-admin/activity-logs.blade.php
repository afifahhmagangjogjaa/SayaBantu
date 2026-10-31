@php
    $title = 'Activity Logs';
    $breadcrumb = 'Super Admin / Activity Logs';
@endphp

<div>
    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-emerald-800 font-semibold text-xs">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- 1. Total Aktivitas -->
        <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/30 rounded-2xl border border-blue-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-900/70">Total Aktivitas</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['total_logs']) }}</p>
            </div>
            <div class="w-11 h-11 bg-blue-100 text-[#0066FF] border border-blue-200/80 rounded-xl flex items-center justify-center flex-shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- 2. Hari Ini -->
        <div class="bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 rounded-2xl border border-emerald-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-900/70">Hari Ini</p>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($stats['today_logs']) }}</p>
            </div>
            <div class="w-11 h-11 bg-emerald-100 text-emerald-600 border border-emerald-200/80 rounded-xl flex items-center justify-center flex-shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- 3. Admin -->
        <div class="bg-gradient-to-br from-purple-50/80 via-white to-purple-50/30 rounded-2xl border border-purple-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-purple-900/70">Admin</p>
                <p class="text-2xl font-black text-purple-700 mt-1">{{ number_format($stats['admin_logs']) }}</p>
            </div>
            <div class="w-11 h-11 bg-purple-100 text-purple-600 border border-purple-200/80 rounded-xl flex items-center justify-center flex-shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- 4. Customer -->
        <div class="bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 rounded-2xl border border-amber-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-900/70">Customer</p>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['customer_logs']) }}</p>
            </div>
            <div class="w-11 h-11 bg-amber-100 text-amber-600 border border-amber-200/80 rounded-xl flex items-center justify-center flex-shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        <!-- 5. Mitra -->
        <div class="bg-gradient-to-br from-sky-50/80 via-white to-sky-50/30 rounded-2xl border border-sky-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-900/70">Mitra</p>
                <p class="text-2xl font-black text-sky-700 mt-1">{{ number_format($stats['mitra_logs']) }}</p>
            </div>
            <div class="w-11 h-11 bg-sky-100 text-sky-600 border border-sky-200/80 rounded-xl flex items-center justify-center flex-shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
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
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Daftar Log Aktivitas</h2>
                <p class="text-xs text-gray-500 mt-0.5">Riwayat aktivitas pengguna dalam sistem</p>
            </div>
            <div class="flex items-center gap-2">
                @if(($stats['total_logs'] ?? 0) > 0)
                    <button 
                        wire:click="deleteAllLogs"
                        wire:confirm="Apakah Anda yakin ingin menghapus SEMUA log aktivitas? Tindakan ini tidak dapat dibatalkan."
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus Semua Log</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200">
                    <tr>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Role</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">IP Address</th>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-2.5 py-3 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                {{ $logs->firstItem() + $loop->index }}
                            </td>
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
                            <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs">
                                <button 
                                    wire:click="deleteLog({{ $log->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus log aktivitas ini?"
                                    class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                    title="Hapus Log">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
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

