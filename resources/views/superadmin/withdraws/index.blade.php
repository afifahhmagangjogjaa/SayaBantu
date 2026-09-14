@extends('layouts.superadmin')

@section('content')

    <div class="space-y-6" x-data="{ showDeleteModal: false, deleteMode: 'monthly', deleteYear: '{{ $availableYears[0] ?? date('Y') }}', deleteMonth: '{{ (int) date('n') }}' }">
        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Withdraw</h2>
                    <p class="text-xs text-gray-500 mt-1">Menampilkan permintaan tarik saldo terbaru dari mitra.</p>
                </div>
                <div>
                    <button type="button" @click="showDeleteModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-2xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus Riwayat</span>
                    </button>
                </div>
            </div>

            <!-- Summary Cards (Clickable Quick Filters) -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    <a href="{{ route('superadmin.withdraws.index') }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ !request('status') ? 'bg-primary-50 border-primary-300 ring-2 ring-primary-200' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                        <div class="text-xs font-medium text-gray-500">Semua Request</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">{{ $counts['all'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('superadmin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'pending' ? 'bg-yellow-50 border-yellow-300 ring-2 ring-yellow-200' : 'bg-white border-gray-200 hover:border-yellow-200' }}">
                        <div class="text-xs font-medium text-yellow-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                            Pending
                        </div>
                        <div class="text-xl font-bold text-yellow-800 mt-1">{{ $counts['pending'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('superadmin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'processing' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : 'bg-white border-gray-200 hover:border-blue-200' }}">
                        <div class="text-xs font-medium text-blue-700">Diproses</div>
                        <div class="text-xl font-bold text-blue-800 mt-1">{{ $counts['processing'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('superadmin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'success'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'success' ? 'bg-green-50 border-green-300 ring-2 ring-green-200' : 'bg-white border-gray-200 hover:border-green-200' }}">
                        <div class="text-xs font-medium text-green-700">Berhasil</div>
                        <div class="text-xl font-bold text-green-800 mt-1">{{ $counts['success'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('superadmin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'failed'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'failed' ? 'bg-red-50 border-red-300 ring-2 ring-red-200' : 'bg-white border-gray-200 hover:border-red-200' }}">
                        <div class="text-xs font-medium text-red-700">Ditolak/Gagal</div>
                        <div class="text-xl font-bold text-red-800 mt-1">{{ $counts['failed'] ?? 0 }}</div>
                    </a>
                </div>

                <!-- Unified Filter Form -->
                <form method="GET" action="{{ route('superadmin.withdraws.index') }}" class="mt-4 pt-4 border-t border-gray-200/70">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Mitra / Rekening / ID / Nominal</label>
                            <div class="relative w-full flex items-center">
                                <!-- Search Icon strictly inside Input -->
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-gray-400" style="padding-left: 12px;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') ?? request('user') }}"
                                    style="padding-left: 38px; padding-right: 34px; padding-top: 8px; padding-bottom: 8px; font-size: 14px;"
                                    class="w-full bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                    placeholder="Nama mitra, no. rek, email, ID (#12), nominal..." />
                                @if(request('search') || request('user'))
                                    <a href="{{ route('superadmin.withdraws.index', request()->except('search', 'user', 'page')) }}" 
                                       class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer" 
                                       style="padding-right: 12px;"
                                       title="Hapus pencarian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil (Success)</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Ditolak / Gagal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bank</label>
                            <select name="bank_code" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                                <option value="">Semua Bank</option>
                                @foreach(($banks ?? []) as $b)
                                    <option value="{{ $b }}" {{ request('bank_code') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer" />
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            @if(request()->hasAny(['search', 'user', 'status', 'bank_code', 'date_from', 'date_to']))
                                <span class="inline-flex items-center text-primary-700 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-600 mr-1.5"></span>
                                    Filter sedang aktif ({{ $items->total() }} data ditemukan)
                                </span>
                            @else
                                Menampilkan total {{ $items->total() }} data
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if(request()->hasAny(['search', 'user', 'status', 'bank_code', 'date_from', 'date_to']))
                                <a href="{{ route('superadmin.withdraws.index') }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-xs font-semibold">
                                    Reset Filter
                                </a>
                            @endif
                            <button type="submit" 
                                class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition text-xs font-semibold shadow-sm flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Cari / Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mitra</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jumlah</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Rekening Tujuan</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-3 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                    {{ $items->firstItem() + $loop->index }}
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-900">
                                        {{ $item->created_at->isToday() ? 'Hari ini' : ($item->created_at->isYesterday() ? 'Kemarin' : $item->created_at->translatedFormat('d M Y')) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-2.5 py-3">
                                    @if($item->user)
                                        <div class="flex items-center gap-3" style="gap: 12px;">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shadow-2xs flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px;">
                                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0" style="margin-left: 4px;">
                                                <span class="text-sm font-semibold text-gray-900 truncate block max-w-[160px]">{{ $item->user->name }}</span>
                                                <div class="text-[10px] text-gray-500 mt-0.5">WD: #{{ $item->id }} • ID Mitra: {{ $item->user_id }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">User terhapus</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">Rp {{ number_format($item->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-800">{{ $item->bank_code }}</div>
                                    <div class="text-[10px] font-mono text-gray-500 mt-0.5">{{ $item->account_number }}</div>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1 animate-pulse"></span> Pending
                                        </span>
                                    @elseif($item->status === 'processing')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span> Diproses
                                        </span>
                                    @elseif($item->status === 'success')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span> Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span> Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button data-id="{{ $item->id }}"
                                            class="open-withdraw-modal inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition shadow-xs text-xs font-semibold cursor-pointer">
                                            Proses
                                        </button>
                                        @if($item->status !== 'pending')
                                            <form action="{{ route('superadmin.withdraws.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data riwayat withdraw ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Riwayat Ini"
                                                    class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg border border-rose-200 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada request withdraw</h3>
                                    <p class="mt-1 text-sm text-gray-500">Permintaan penarikan saldo dari mitra akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-center">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Hapus Riwayat Withdraw Berdasarkan Periode -->
        <div x-show="showDeleteModal" style="display: none;" 
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" @click="showDeleteModal = false"></div>
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 space-y-5 border border-gray-100" @click.away="showDeleteModal = false">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Hapus Riwayat Withdraw</h3>
                                <p class="text-xs text-gray-500">Pilih rentang bulan & tahun yang ingin dibersihkan.</p>
                            </div>
                        </div>
                        <button type="button" @click="showDeleteModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                <!-- Pilihan Tipe Hapus (Tabs) -->
                <div class="flex rounded-xl bg-gray-100 p-1 gap-1">
                    <button type="button" @click="deleteMode = 'monthly'"
                        :class="deleteMode === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer">
                        Bulanan
                    </button>
                    <button type="button" @click="deleteMode = 'yearly'"
                        :class="deleteMode === 'yearly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer">
                        Tahunan
                    </button>
                    <button type="button" @click="deleteMode = 'all'"
                        :class="deleteMode === 'all' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-500 hover:text-rose-600'"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer">
                        Hapus Semua
                    </button>
                </div>

                <!-- Konten Mode Bulanan -->
                <div x-show="deleteMode === 'monthly'" class="space-y-4">
                    <form action="{{ route('superadmin.withdraws.destroy_period') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data riwayat withdraw pada bulan terpilih? Tindakan ini tidak dapat dibatalkan.');" class="space-y-4">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                            <select name="year" x-model="deleteYear" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Bulan</label>
                            <select name="month" x-model="deleteMonth" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                <option value="1">01 - Januari</option>
                                <option value="2">02 - Februari</option>
                                <option value="3">03 - Maret</option>
                                <option value="4">04 - April</option>
                                <option value="5">05 - Mei</option>
                                <option value="6">06 - Juni</option>
                                <option value="7">07 - Juli</option>
                                <option value="8">08 - Agustus</option>
                                <option value="9">09 - September</option>
                                <option value="10">10 - Oktober</option>
                                <option value="11">11 - November</option>
                                <option value="12">12 - Desember</option>
                            </select>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Hanya riwayat withdraw yang telah <strong>selesai atau gagal</strong> pada bulan terpilih yang akan dihapus. Permintaan pending tetap aman.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Riwayat Bulan Terpilih</span>
                        </button>
                    </form>
                </div>

                <!-- Konten Mode Tahunan -->
                <div x-show="deleteMode === 'yearly'" style="display: none;" class="space-y-4">
                    <form action="{{ route('superadmin.withdraws.destroy_period') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SELURUH riwayat withdraw pada tahun terpilih? Tindakan ini tidak dapat dibatalkan.');" class="space-y-4">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="month" value="all">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                            <select name="year" x-model="deleteYear" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Seluruh riwayat withdraw yang telah selesai atau gagal sepanjang tahun terpilih akan dihapus. Permintaan pending tetap aman.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Seluruh Tahun Terpilih</span>
                        </button>
                    </form>
                </div>

                <!-- Konten Mode Hapus Semua -->
                <div x-show="deleteMode === 'all'" style="display: none;" class="space-y-4">
                    <form action="{{ route('superadmin.withdraws.destroy_all') }}" method="POST" onsubmit="return confirm('PERINGATAN EKSTREM: Apakah Anda yakin ingin menghapus SEMUA riwayat withdraw selesai/gagal tanpa terkecuali?');" class="space-y-4">
                        @csrf
                        @method('DELETE')

                        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex gap-3 items-start">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-rose-900 uppercase tracking-wider mb-1">Peringatan Ekstrem</h4>
                                <p class="text-xs text-rose-800 leading-relaxed">
                                    Tindakan ini akan menghapus <strong>SEMUA</strong> riwayat withdraw selesai atau gagal tanpa batasan waktu. Permintaan withdraw yang masih pending tetap aman.
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Semua Riwayat Sekarang</span>
                        </button>
                    </form>
                </div>

                <!-- Footer Cancel -->
                <div class="border-t border-gray-100 pt-3 flex items-center justify-end">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs text-gray-600 hover:text-gray-800 font-medium hover:bg-gray-100 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal container -->
    <div id="superadmin-withdraw-modal-container"></div>

    <script>
        (function () {
            function openModal(id) {
                var container = document.getElementById('superadmin-withdraw-modal-container');
                // Fetch modal HTML
                fetch('/superadmin/withdraws/' + id + '/modal')
                    .then(function (res) { return res.text(); })
                    .then(function (html) {
                        container.innerHTML = html;
                        // Ensure modal scripts/behavior are initialized after injection
                        initSuperadminWithdrawModal(container);
                        window.scrollTo(0, 0);
                    })
                    .catch(function (err) { console.error('Failed to load modal:', err); });
            }

            document.querySelectorAll('.open-withdraw-modal').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = this.getAttribute('data-id');
                    openModal(id);
                });
            });
        })();

        function initSuperadminWithdrawModal(container) {
            var modal = container.querySelector('#superadmin-withdraw-modal');
            if (!modal) return;
            var overlay = modal.querySelector('#superadmin-withdraw-modal-overlay');
            var closeBtn = modal.querySelector('#close-superadmin-withdraw-modal');
            function removeModal() { container.innerHTML = ''; }
            // Initialize Alpine for dynamically injected modal (if Alpine is present)
            try {
                if (window.Alpine && typeof Alpine.initTree === 'function') {
                    Alpine.initTree(modal);
                }
            } catch (e) {
                console.warn('Alpine initTree failed for injected modal', e);
            }

            // Attach fallback handlers for reject modal inside injected modal
            try {
                var openRejectBtn = modal.querySelector('#open-reject-local');
                var fallback = modal.querySelector('#withdraw-reject-modal-fallback');
                var closeFallback = modal.querySelector('#close-reject-fallback');
                var cancelFallback = modal.querySelector('#cancel-reject-fallback');
                var overlayFallback = modal.querySelector('#withdraw-reject-fallback-overlay');

                console.log('initSuperadminWithdrawModal: openRejectBtn=', !!openRejectBtn, 'fallback=', !!fallback, 'overlay=', !!overlayFallback);

                function showFallback() {
                    if (!fallback) return;
                    fallback.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                function hideFallback() {
                    if (!fallback) return;
                    fallback.classList.add('hidden');
                    document.body.style.overflow = '';
                }

                if (openRejectBtn) {
                    openRejectBtn.addEventListener('click', function (e) {
                        // always show fallback as a guarantee when button clicked
                        try { console.log('open-reject-local clicked'); } catch (err) {}
                        showFallback();
                        try { window.dispatchEvent(new CustomEvent('open-reject')); } catch (err) {}
                    });
                } else {
                    // if button not found inside modal, listen globally just in case
                    window.addEventListener('click', function (ev) {
                        var target = ev.target || ev.srcElement;
                        if (target && target.id === 'open-reject-local') {
                            showFallback();
                            try { window.dispatchEvent(new CustomEvent('open-reject')); } catch (err) {}
                        }
                    });
                }

                if (closeFallback) closeFallback.addEventListener('click', hideFallback);
                if (cancelFallback) cancelFallback.addEventListener('click', hideFallback);
                if (overlayFallback) overlayFallback.addEventListener('click', hideFallback);
            } catch (e) {
                console.warn('Failed to attach reject fallback handlers', e);
            }

            if (closeBtn) closeBtn.addEventListener('click', removeModal);
            if (overlay) overlay.addEventListener('click', removeModal);
            document.body.style.overflow = 'hidden';
            var originalRemove = removeModal;
            removeModal = function () { document.body.style.overflow = ''; originalRemove(); };
        }

        // Auto-refresh daftar withdraw setiap 8 detik jika tidak sedang membuka modal
        setInterval(function() {
            var modalContainer = document.getElementById('superadmin-withdraw-modal-container');
            var isModalOpen = modalContainer && modalContainer.children.length > 0;
            var isInputFocused = document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA');
            if (!isModalOpen && !isInputFocused) {
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(res) { return res.text(); })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newTable = doc.querySelector('.bg-white.rounded-2xl.shadow-lg');
                        var currentTable = document.querySelector('.bg-white.rounded-2xl.shadow-lg');
                        if (newTable && currentTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                            // Re-bind modal triggers
                            document.querySelectorAll('.open-withdraw-modal').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    var id = this.getAttribute('data-id');
                                    var container = document.getElementById('superadmin-withdraw-modal-container');
                                    fetch('/superadmin/withdraws/' + id + '/modal')
                                        .then(function (res) { return res.text(); })
                                        .then(function (mHtml) {
                                            container.innerHTML = mHtml;
                                            initSuperadminWithdrawModal(container);
                                            window.scrollTo(0, 0);
                                        });
                                });
                            });
                        }
                    })
                    .catch(function(e) { /* ignore */ });
            }
        }, 8000);
    </script>
@endsection
