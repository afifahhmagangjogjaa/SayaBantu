@php
    $totalMb = $stats['total_mb'] ?? 0;
    $totalFiles = $stats['total_files'] ?? 0;
    $orphanCount = $orphanStats['count'] ?? 0;
    $orphanMb = $orphanStats['size_mb'] ?? 0;
    $tmpCount = $stats['tmp_files'] ?? 0;
    $tmpMb = $stats['tmp_mb'] ?? 0;
@endphp

<div class="space-y-6">
    <!-- Breadcrumb & Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('superadmin.dashboard') }}" wire:navigate class="hover:text-[#0066FF] transition">Super Admin</a>
                <span>/</span>
                <span class="text-slate-400">Pengaturan</span>
                <span>/</span>
                <span class="text-slate-700 font-medium">Pembersihan Storage</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Manajemen & Pembersihan Storage</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau kapasitas penyimpanan disk dan bersihkan berkas tumpukan order lama secara aman.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" wire:click="loadStorageStats" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/80 rounded-xl text-xs font-bold transition shadow-2xs hover:shadow-xs active:scale-95 cursor-pointer">
                <svg wire:loading.class="animate-spin" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Pindai Ulang Storage</span>
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-xs flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Top Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Kapasitas Digunakan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ruang Terpakai</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0066FF] border border-blue-100 flex items-center justify-center text-lg shadow-2xs">
                    💾
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-gray-900 tracking-tight">
                    @if($totalMb >= 1024)
                        {{ number_format($totalMb / 1024, 2) }} <span class="text-sm font-bold text-slate-500">GB</span>
                    @else
                        {{ number_format($totalMb, 2) }} <span class="text-sm font-bold text-slate-500">MB</span>
                    @endif
                </span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Kapasitas Sangat Sehat (Ringan)</span>
            </div>
        </div>

        <!-- 2. Total Berkas File -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Berkas</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-lg shadow-2xs">
                    📁
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($totalFiles) }}</span>
                <span class="text-sm font-bold text-slate-500">file</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Total semua unggahan dalam storage</p>
        </div>

        <!-- 3. Berkas Sampah (Orphan Files) -->
        <div class="bg-white rounded-2xl border {{ $orphanCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80' }} p-5 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">File Sampah (Orphan)</span>
                <div class="w-10 h-10 rounded-xl {{ $orphanCount > 0 ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center text-lg shadow-2xs">
                    🧹
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black {{ $orphanCount > 0 ? 'text-amber-800' : 'text-gray-900' }} tracking-tight">{{ number_format($orphanCount) }}</span>
                <span class="text-sm font-bold text-slate-500">file</span>
                @if($orphanMb > 0)
                    <span class="text-xs text-amber-700 font-bold ml-1">({{ $orphanMb }} MB)</span>
                @endif
            </div>
            <p class="mt-2 text-[11px] {{ $orphanCount > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">
                {{ $orphanCount > 0 ? 'Siap dibersihkan tanpa risiko' : 'Tidak ada file yatim terdeteksi' }}
            </p>
        </div>

        <!-- 4. Cache Temporary Upload -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cache Sementara</span>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center text-lg shadow-2xs">
                    ⚡
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($tmpCount) }}</span>
                <span class="text-sm font-bold text-slate-500">file</span>
                @if($tmpMb > 0)
                    <span class="text-xs text-teal-700 font-bold ml-1">({{ $tmpMb }} MB)</span>
                @endif
            </div>
            <p class="mt-2 text-[11px] text-slate-400">File sementara Livewire upload</p>
        </div>
    </div>

    <!-- Quick Cleanup Actions Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/70 to-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#0066FF] text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 leading-tight">Pusat Aksi Pembersihan File</h2>
                    <p class="text-xs text-slate-500">Pilih opsi pembersihan berkas untuk mengosongkan ruang disk server</p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Action 1: Bersihkan File Sampah / Orphan -->
            <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-xl">🧹</span>
                        <h3 class="font-bold text-sm text-gray-900">Bersihkan File Sampah (Orphan Files)</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Hapus berkas foto yang ada di folder storage tetapi catatannya sudah tidak ada di database (sisa testing atau order yang telah terhapus).
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100/70 text-amber-900 text-[11px] font-bold">
                        <span>Status:</span>
                        <span>{{ $orphanCount }} berkas terdeteksi ({{ $orphanMb }} MB)</span>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="button" wire:click="promptCleanOrphans" {{ $orphanCount === 0 ? 'disabled' : '' }}
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 {{ $orphanCount > 0 ? 'bg-amber-600 hover:bg-amber-700 active:scale-98 text-white cursor-pointer shadow-amber-600/20' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>{{ $orphanCount > 0 ? 'Bersihkan File Sampah Sekarang' : 'Tidak Ada File Sampah' }}</span>
                    </button>
                </div>
            </div>

            <!-- Action 2: Bersihkan Cache Upload Temporary -->
            <div class="rounded-2xl border border-teal-200/80 bg-gradient-to-br from-teal-50/50 via-white to-teal-50/20 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-xl">⚡</span>
                        <h3 class="font-bold text-sm text-gray-900">Bersihkan Cache Unggahan Sementara</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Hapus berkas temporary sisa unggahan form yang dibatalkan atau terputus pada direktori temporary Livewire yang berumur lebih dari 30 menit.
                    </p>
                    <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-100/70 text-teal-900 text-[11px] font-bold">
                        <span>Status:</span>
                        <span>{{ $tmpCount }} berkas sementara ({{ $tmpMb }} MB)</span>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="button" wire:click="promptCleanTmp"
                            class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-xs shadow-teal-600/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Kosongkan Cache Upload</span>
                    </button>
                </div>
            </div>

            <!-- Action 3: Bersihkan Foto Order Selesai -->
            <div class="rounded-2xl border border-blue-200/80 bg-gradient-to-br from-blue-50/50 via-white to-blue-50/20 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-xl">📦</span>
                        <h3 class="font-bold text-sm text-gray-900">Bersihkan Foto Pesanan Selesai / Batal</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Hapus berkas foto bantuan, foto awal pengerjaan, dan bukti selesai dari order yang sudah tuntas. Catatan riwayat order & nominal tetap utuh.
                    </p>
                    <div class="mt-3">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Batas Waktu Order:</label>
                        <select wire:model.live="helpsPeriod" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] outline-none">
                            <option value="30">Selesai lebih dari 30 hari yang lalu</option>
                            <option value="60">Selesai lebih dari 60 hari yang lalu</option>
                            <option value="90">Selesai lebih dari 90 hari yang lalu</option>
                            <option value="all">Semua order yang sudah selesai & dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="button" wire:click="promptCleanCompletedHelps"
                            class="w-full py-2.5 px-4 bg-[#0066FF] hover:bg-[#0052cc] active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-xs shadow-blue-500/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Bersihkan Foto Order Selesai</span>
                    </button>
                </div>
            </div>

            <!-- Action 4: Bersihkan Bukti Transfer Top Up -->
            <div class="rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50/50 via-white to-emerald-50/20 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-xl">💳</span>
                        <h3 class="font-bold text-sm text-gray-900">Bersihkan Bukti Transfer Top Up Selesai</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Hapus berkas struk pembayaran dari permintaan top up yang sudah diapprove atau ditolak. Riwayat mutasi saldo pengguna tetap aman.
                    </p>
                    <div class="mt-3">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Batas Waktu Transaksi:</label>
                        <select wire:model.live="topupsPeriod" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] outline-none">
                            <option value="30">Transaksi lebih dari 30 hari yang lalu</option>
                            <option value="60">Transaksi lebih dari 60 hari yang lalu</option>
                            <option value="all">Semua transaksi yang sudah diproses</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="button" wire:click="promptCleanTopupProofs"
                            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-xs shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Bersihkan Struk Top Up Selesai</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Directory Breakdown Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-gray-900">Rincian Penggunaan Direktori Storage</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar folder penyimpanan berkas di public disk server</p>
            </div>
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/60">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span>Total Direktori: {{ count($stats['folders'] ?? []) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-5">Kategori & Folder</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah File</th>
                        <th class="py-3 px-4 text-right">Kapasitas (MB)</th>
                        <th class="py-3 px-5 text-center">Status Proteksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($stats['folders'] ?? [] as $folder)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base shrink-0 shadow-2xs">
                                        {{ $folder['icon'] ?? '📁' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 truncate">{{ $folder['label'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">public/{{ $folder['dir'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate">
                                {{ $folder['description'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-gray-900">
                                {{ number_format($folder['files_count']) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-gray-900 font-mono">
                                {{ number_format($folder['size_mb'], 2) }} MB
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @if(!empty($folder['protected']))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span>🛡️ Terproteksi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>🟢 Siap Dikelola</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada direktori berkas yang terdeteksi di storage.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal Dialog -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4" x-data>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0 shadow-2xs">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="font-black text-base text-gray-900 leading-snug">{{ $modalTitle }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Konfirmasi tindakan pembersihan berkas</p>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-3.5 text-xs">
                    <p class="text-slate-700 leading-relaxed font-medium">
                        {{ $modalDescription }}
                    </p>

                    <div class="p-3.5 bg-amber-50 border border-amber-200/90 rounded-2xl text-amber-900 leading-relaxed text-[11px]">
                        <p class="font-bold mb-1 flex items-center gap-1.5">
                            <span>🛡️ Catatan Keamanan:</span>
                        </p>
                        <p>{{ $modalWarning }}</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 bg-slate-50/80 border-t border-slate-100 flex items-center gap-3">
                    <button type="button" wire:click="cancelConfirm" wire:loading.attr="disabled"
                            class="flex-1 py-3 px-4 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl text-xs transition border border-slate-200 shadow-2xs cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" wire:click="executeAction" wire:loading.attr="disabled"
                            class="flex-1 py-3 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold rounded-xl text-xs transition shadow-md shadow-rose-600/25 cursor-pointer flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="executeAction">Lanjutkan Hapus</span>
                        <span wire:loading wire:target="executeAction" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

