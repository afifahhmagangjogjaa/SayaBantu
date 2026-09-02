@php
    $title = 'Approval Top-Up Saldo';
    $breadcrumb = 'Super Admin / Approval Top-Up';
@endphp

<div x-data="approvalModal()" @confirm-approve.window="openFromEvent($event)" class="space-y-6">
    <div wire:poll.4s>
        <!-- Alerts -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 flex items-start gap-3 shadow-2xs">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm font-semibold shadow-2xs">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Pending</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1.5">{{ $pendingRequests->total() }}</h3>
                        <p class="text-amber-600 text-xs font-semibold mt-1">Request butuh verifikasi</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Approved Hari Ini</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1.5">{{ $approvedToday ?? 0 }}</h3>
                        <p class="text-green-600 text-xs font-semibold mt-1">Transaksi telah disetujui</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-green-50 border border-green-100 flex items-center justify-center text-green-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Nominal Pending</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1.5">Rp {{ number_format($totalPendingAmount ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-gray-400 text-xs mt-1">Rp {{ number_format($totalApprovedAmountToday ?? 0, 0, ',', '.') }} (Approved Hari Ini)</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 1: REQUEST MENUNGGU APPROVAL -->
        <!-- ========================================== -->
        <div class="mb-8">
            <!-- Section Header & Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 px-1 sm:px-2">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                        <span>Request Menunggu Approval</span>
                        <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $totalPendingCount ?? 0 }} Data
                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-1.5">Antrean request top-up dari customer wilayah Anda yang menunggu approval Super Admin</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center h-9 w-72 sm:w-80 bg-white border border-gray-300 rounded-xl px-3 shadow-2xs transition focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="searchPending"
                            placeholder="Cari customer, kode, no HP..."
                            class="w-full h-full bg-transparent border-0 py-0 px-0 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 leading-normal" />
                        @if($searchPending)
                            <button type="button" wire:click="$set('searchPending', '')" class="ml-1.5 text-gray-400 hover:text-gray-600 text-xs flex-shrink-0" title="Hapus pencarian">
                                ✕
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Kode Request</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nominal</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Total Bayar</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Metode</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu Masuk</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($pendingRequests as $transaction)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500">
                                        {{ $pendingRequests->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-2.5 py-3">
                                        <div class="flex items-center gap-3" style="gap: 12px;">
                                            <div class="w-8 h-8 flex-shrink-0 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-xs" style="width: 32px; height: 32px; min-width: 32px;">
                                                {{ strtoupper(substr($transaction->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0" style="margin-left: 4px;">
                                                <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[150px]" title="{{ $transaction->user->name ?? '-' }}">{{ $transaction->user->name ?? '-' }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]" title="{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}">{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}</div>
                                                @if(!empty($transaction->user->city->name))
                                                    <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                                        <span>📍 {{ $transaction->user->city->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-mono font-bold text-gray-900 px-2 py-0.5 bg-gray-100 border border-gray-200 rounded inline-block">
                                            {{ $transaction->request_code ?? '#' . $transaction->id }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5 font-mono">
                                            {{ $transaction->customer_phone ?? ($transaction->user->phone ?? '-') }}
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-900">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            +Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }} fee
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-primary-700 px-2 py-0.5 bg-primary-50 border border-primary-100 rounded inline-block">
                                            Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700 inline-block border border-gray-200">
                                            {{ $transaction->payment_method ?? 'QRIS' }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-semibold text-gray-900">{{ $transaction->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $transaction->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium">
                                        <div class="inline-flex items-center justify-center gap-1" style="gap: 4px;">
                                            <!-- Detail Button -->
                                            <button type="button" wire:click="viewDetail({{ $transaction->id }})"
                                                wire:loading.attr="disabled"
                                                class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded text-xs font-semibold transition"
                                                title="Lihat Bukti">
                                                Detail
                                            </button>

                                            <!-- Tolak Button -->
                                            <button type="button" wire:click="openRejectModal({{ $transaction->id }})"
                                                wire:loading.attr="disabled"
                                                class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded text-xs font-semibold transition"
                                                title="Tolak Request">
                                                Tolak
                                            </button>

                                            <!-- Approve Button -->
                                            <button type="button" wire:loading.attr="disabled"
                                                data-id="{{ $transaction->id }}"
                                                data-name="{{ $transaction->user->name ?? 'User' }}"
                                                data-amount="{{ 'Rp ' . number_format($transaction->amount, 0, ',', '.') }}"
                                                @click.prevent="openFromEl($event)"
                                                class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold transition shadow-xs"
                                                title="Approve Request">
                                                Approve
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-800">Tidak Ada Antrean Approval</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Semua request top-up telah diproses atau belum ada request baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pendingRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $pendingRequests->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSI 2: RIWAYAT APPROVAL & TRANSAKSI -->
        <!-- ========================================== -->
        <div class="border-t border-gray-200 pt-6 mt-6">
            <!-- Section Header & Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 px-1 sm:px-2">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                        <span>Riwayat Approval & Transaksi</span>
                        <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            {{ $totalHistoryCount ?? 0 }} Data
                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-1.5">Histori seluruh transaksi top-up yang telah disetujui maupun ditolak</p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Status Filter Tabs -->
                    <div class="inline-flex h-9 items-center rounded-xl border border-gray-200 p-1 bg-gray-50 shadow-2xs">
                        <button type="button" wire:click="filterHistoryStatus('')"
                            class="h-7 px-3 flex items-center justify-center rounded-lg text-xs font-semibold transition cursor-pointer {{ $historyStatusFilter === '' || $historyStatusFilter === 'all' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Semua ({{ $totalHistoryCount ?? 0 }})
                        </button>
                        <button type="button" wire:click="filterHistoryStatus('completed')"
                            class="h-7 px-3 flex items-center justify-center rounded-lg text-xs font-semibold transition cursor-pointer {{ $historyStatusFilter === 'completed' ? 'bg-green-600 text-white font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Disetujui ({{ $totalCompletedCount ?? 0 }})
                        </button>
                        <button type="button" wire:click="filterHistoryStatus('rejected')"
                            class="h-7 px-3 flex items-center justify-center rounded-lg text-xs font-semibold transition cursor-pointer {{ $historyStatusFilter === 'rejected' ? 'bg-red-600 text-white font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Ditolak ({{ $totalRejectedCount ?? 0 }})
                        </button>
                    </div>

                    <!-- Search Input History -->
                    <div class="flex items-center h-9 w-64 sm:w-72 bg-white border border-gray-300 rounded-xl px-3 shadow-2xs transition focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="searchHistory"
                            placeholder="Cari riwayat transaksi..."
                            class="w-full h-full bg-transparent border-0 py-0 px-0 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 leading-normal" />
                        @if($searchHistory)
                            <button type="button" wire:click="$set('searchHistory', '')" class="ml-1.5 text-gray-400 hover:text-gray-600 text-xs flex-shrink-0" title="Hapus pencarian">
                                ✕
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Kode Request</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Nominal</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Total Bayar</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Metode</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Waktu Diproses</th>
                                <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($historyRequests as $transaction)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500">
                                        {{ $historyRequests->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-2.5 py-3">
                                        <div class="flex items-center gap-3" style="gap: 12px;">
                                            <div class="w-8 h-8 flex-shrink-0 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs" style="width: 32px; height: 32px; min-width: 32px;">
                                                {{ strtoupper(substr($transaction->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0" style="margin-left: 4px;">
                                                <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[150px]" title="{{ $transaction->user->name ?? '-' }}">{{ $transaction->user->name ?? '-' }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]" title="{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}">{{ $transaction->customer_email ?? ($transaction->user->email ?? '-') }}</div>
                                                @if(!empty($transaction->user->city->name))
                                                    <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                                        <span>📍 {{ $transaction->user->city->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-mono font-bold text-gray-900 px-2 py-0.5 bg-gray-100 border border-gray-200 rounded inline-block">
                                            {{ $transaction->request_code ?? '#' . $transaction->id }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5 font-mono">
                                            {{ $transaction->customer_phone ?? ($transaction->user->phone ?? '-') }}
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-900">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            +Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }} fee
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-900">
                                            Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700 inline-block border border-gray-200">
                                            {{ $transaction->payment_method ?? 'QRIS' }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                        @if($transaction->status === 'completed' || $transaction->status === 'approved')
                                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                                                Disetujui
                                            </span>
                                        @elseif($transaction->status === 'rejected')
                                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap">
                                        <div class="text-xs font-semibold text-gray-900">{{ $transaction->approved_at ? $transaction->approved_at->format('d M Y') : $transaction->updated_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $transaction->approved_at ? $transaction->approved_at->format('H:i') : $transaction->updated_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium">
                                        <button type="button" wire:click="viewDetail({{ $transaction->id }})"
                                            wire:loading.attr="disabled"
                                            class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-semibold transition border border-gray-200"
                                            title="Lihat Detail Transaksi">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-800">Belum Ada Riwayat</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Belum ada transaksi yang diproses pada filter ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($historyRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $historyRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL DETAIL REQUEST TOP-UP -->
    <!-- ========================================== -->
    @if ($showDetailModal && $selectedTransaction)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click="closeModal">
            <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Detail Request Top-Up</h2>
                        <p class="text-xs text-gray-500 mt-1">Status: 
                            @if($selectedTransaction->status === 'completed' || $selectedTransaction->status === 'approved')
                                <span class="font-bold text-green-600">Disetujui</span>
                            @elseif($selectedTransaction->status === 'rejected')
                                <span class="font-bold text-red-600">Ditolak</span>
                            @else
                                <span class="font-bold text-yellow-600">Menunggu Approval</span>
                            @endif
                        </p>
                    </div>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    @if($selectedTransaction->status === 'completed' || $selectedTransaction->status === 'approved')
                        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-xs text-green-800 flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>Transaksi ini telah <strong>Disetujui</strong> pada {{ $selectedTransaction->approved_at ? $selectedTransaction->approved_at->format('d M Y, H:i WIB') : '-' }}. Saldo customer telah ditambahkan.</div>
                        </div>
                    @elseif($selectedTransaction->status === 'rejected')
                        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-800">
                            <div class="flex items-center gap-2 font-bold mb-1">
                                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Transaksi ini telah Ditolak pada {{ $selectedTransaction->approved_at ? $selectedTransaction->approved_at->format('d M Y, H:i WIB') : '-' }}
                            </div>
                            <div class="mt-2 pl-6 text-red-700 bg-white/70 p-3 rounded-lg border border-red-200/60">
                                <span class="font-semibold text-gray-700">Alasan:</span> {{ $selectedTransaction->rejection_reason ?? 'Tidak ada alasan yang dicantumkan.' }}
                            </div>
                        </div>
                    @endif

                    <!-- Transaction Info -->
                    <div class="bg-gray-50 rounded-xl p-5 mb-5 border border-gray-200/70">
                        <div class="grid grid-cols-2 gap-5 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Customer:</p>
                                <p class="font-semibold text-gray-900">{{ $selectedTransaction->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Kode Request:</p>
                                <p class="font-semibold text-gray-900 font-mono">{{ $selectedTransaction->request_code ?? '#' . $selectedTransaction->id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Nominal:</p>
                                <p class="font-semibold text-gray-900">Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Total Bayar:</p>
                                <p class="font-semibold text-primary-600">Rp {{ number_format($selectedTransaction->total_payment, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Proof Image -->
                    @if ($selectedTransaction->proof_of_payment)
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-semibold text-gray-700">Bukti Transfer:</p>
                                <a href="{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-800 bg-primary-50 hover:bg-primary-100 px-2.5 py-1 rounded-lg border border-primary-200/80 transition"
                                    title="Buka gambar di tab baru">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>Buka Ukuran Penuh ↗</span>
                                </a>
                            </div>
                            <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-gray-50 shadow-2xs">
                                <a href="{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}" target="_blank" rel="noopener noreferrer" class="block">
                                    <img src="{{ asset('storage/' . $selectedTransaction->proof_of_payment) }}" 
                                        class="w-full max-h-[380px] object-contain transition duration-200 group-hover:scale-[1.01]"
                                        alt="Bukti Transfer"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect width=%22400%22 height=%22300%22 fill=%22%23f3f4f6%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%239ca3af%22%3EGambar tidak dapat dimuat%3C/text%3E%3C/svg%3E';">
                                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                        <span class="px-3 py-1.5 bg-black/75 text-white rounded-lg text-xs font-semibold shadow flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                            </svg>
                                            Klik untuk membuka gambar penuh
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8 bg-gray-50 rounded-xl text-xs mb-5 border border-dashed border-gray-200">Bukti transfer tidak tersedia</p>
                    @endif

                    <!-- Actions in modal -->
                    <div class="flex gap-3 mt-6">
                        @if($selectedTransaction->status === 'waiting_approval')
                            <button type="button" wire:loading.attr="disabled"
                                data-id="{{ $selectedTransaction->id }}"
                                data-name="{{ $selectedTransaction->user->name ?? 'User' }}"
                                data-amount="{{ 'Rp ' . number_format($selectedTransaction->amount, 0, ',', '.') }}"
                                @click.prevent="openFromEl($event)"
                                class="flex-1 px-6 py-2.5 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 disabled:opacity-50 text-sm transition">
                                <span wire:loading.remove wire:target="approve({{ $selectedTransaction->id }})">Approve Request</span>
                                <span wire:loading wire:target="approve({{ $selectedTransaction->id }})">Processing...</span>
                            </button>
                            <button type="button" wire:click="openRejectModal({{ $selectedTransaction->id }})"
                                wire:loading.attr="disabled"
                                class="flex-1 px-6 py-2.5 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 disabled:opacity-50 text-sm transition">
                                <span wire:loading.remove wire:target="openRejectModal({{ $selectedTransaction->id }})">Tolak Request</span>
                                <span wire:loading wire:target="openRejectModal({{ $selectedTransaction->id }})">Loading...</span>
                            </button>
                        @else
                            <button type="button" wire:click="closeModal"
                                class="w-full px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 text-sm transition">
                                Tutup
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL TOLAK REQUEST -->
    <!-- ========================================== -->
    @if ($showRejectModal && $selectedTransaction)
        <div class="fixed inset-0 bg-black/50 z-[110] flex items-center justify-center p-4" wire:click="closeModal">
            <div class="bg-white rounded-2xl w-full max-w-md" wire:click.stop>
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Tolak Request Top-Up</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Anda akan menolak request dari <strong>{{ $selectedTransaction->user->name ?? '-' }}</strong>
                        dengan nominal <strong>Rp {{ number_format($selectedTransaction->amount, 0, ',', '.') }}</strong>
                    </p>

                    <form wire:submit.prevent="reject">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan *</label>
                            <textarea wire:model="rejectionReason" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Jelaskan alasan penolakan..."></textarea>
                            @error('rejectionReason')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="button" wire:click="closeModal"
                                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 text-sm transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 text-sm transition">
                                Tolak Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- APPROVAL CONFIRMATION MODAL (ALPINE) -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-cloak x-show="show" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="display: none;">
            <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden" @click.away="close()">
                <div class="p-6">
                    <button @click="close()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <h3 class="text-base font-bold text-gray-900">Setujui Request Top-Up?</h3>
                        <p class="text-xs text-gray-500 mt-1">Saldo customer akan otomatis bertambah:</p>

                        <div class="mt-3 w-full bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                            <div class="text-sm font-semibold text-gray-900" x-text="name"></div>
                            <div class="text-sm font-bold text-green-600 mt-0.5" x-text="amount"></div>
                        </div>

                        <div class="mt-5 flex w-full gap-3">
                            <button @click="close()" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                                Batal
                            </button>
                            <button @click="$wire.approve(id); close()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-semibold transition">
                                Ya, Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        function approvalModal() {
            return {
                show: false,
                id: null,
                name: '',
                amount: '',
                openFromEvent(e) {
                    const d = e.detail || {};
                    this.id = d.id ?? null;
                    this.name = d.name ?? '';
                    this.amount = d.amount ?? '';
                    this.show = true;
                },
                openFromEl(e) {
                    const el = e.currentTarget || e.target;
                    const id = el.dataset?.id ?? null;
                    const name = el.dataset?.name ?? '';
                    const amount = el.dataset?.amount ?? '';
                    this.id = id;
                    this.name = name;
                    this.amount = amount;
                    this.show = true;
                },
                close() {
                    this.show = false;
                    this.id = null;
                }
            }
        }
    </script>
</div>
