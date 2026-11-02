    <div class="space-y-6">
        @isset($totalHelps)
        <!-- Stats Cards for Index -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Bantuan -->
            <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/30 p-4 sm:p-5 rounded-2xl border border-blue-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-900/70">Total Bantuan</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalHelps) }}</h3>
                    <p class="text-[11px] text-blue-600/80 mt-0.5">Dari kota yang Anda kelola</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#0066FF] border border-blue-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- 2. Menunggu Mitra -->
            <div class="bg-gradient-to-br from-amber-50/80 via-white to-amber-50/30 p-4 sm:p-5 rounded-2xl border border-amber-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-900/70">Menunggu Mitra</span>
                    <h3 class="text-2xl font-black text-amber-700 mt-1">{{ number_format($waitingMitraHelps) }}</h3>
                    <p class="text-[11px] text-amber-600/80 mt-0.5">Belum ada mitra yang mengambil</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 3. Komplain / Mediasi -->
            <div class="bg-gradient-to-br from-rose-50/80 via-white to-rose-50/30 p-4 sm:p-5 rounded-2xl border border-rose-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between cursor-pointer hover:border-rose-300" wire:click="$set('statusFilter', 'komplain')">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold uppercase tracking-wider text-rose-900/70">Komplain / Mediasi</span>
                        <span class="w-2 h-2 rounded-full {{ ($complaintHelps ?? 0) > 0 ? 'bg-rose-500 animate-pulse' : 'bg-gray-300' }}"></span>
                    </div>
                    <h3 class="text-2xl font-black text-rose-700 mt-1">{{ number_format($complaintHelps ?? 0) }}</h3>
                    <p class="text-[11px] text-rose-600/80 mt-0.5">Perlu keputusan / mediasi admin</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- 4. Selesai -->
            <div class="bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 p-4 sm:p-5 rounded-2xl border border-emerald-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-900/70">Selesai</span>
                    <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($completedHelps) }}</h3>
                    <p class="text-[11px] text-emerald-600/80 mt-0.5">Pekerjaan telah diselesaikan</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 border border-emerald-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        @endisset

        <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ isset($totalHelps) ? 'Daftar Permintaan Bantuan' : 'Bantuan - Disetujui' }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ isset($totalHelps) ? 'Kelola dan moderasi permintaan bantuan layanan.' : 'Menampilkan bantuan dengan status disetujui (hanya lihat untuk Admin).' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @isset($totalHelps)
                    <select wire:model.live="statusFilter" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white cursor-pointer font-medium text-gray-700">
                        <option value="">Semua Status</option>
                        <option value="komplain">⚠️ Komplain / Mediasi</option>
                        <option value="menunggu_mitra">Menunggu Mitra</option>
                        <option value="waiting_customer_confirmation">Menunggu Konfirmasi Customer</option>
                        <option value="partner_on_the_way">Mitra OTW</option>
                        <option value="taken">Diambil Mitra</option>
                        <option value="selesai">Selesai</option>
                        <option value="rejected">Ditolak</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    @endisset
                    <input wire:model.live.debounce.500ms="search" type="text" placeholder="Cari judul atau deskripsi..."
                        class="px-3 py-1.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kota</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($helps as $help)
                            <tr class="hover:bg-gray-50/80 transition-colors {{ in_array($help->status, ['komplain', 'disputed']) ? 'bg-red-50/40' : '' }}">
                                <td class="px-2.5 py-2.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">{{ $helps->firstItem() + $loop->index }}</td>
                                <td class="px-2.5 py-2.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $help->title }}</div>
                                        @if($help->isUrgent())
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">ID: {{ $help->order_id ?? '#' . $help->id }}</div>
                                </td>
                                <td class="px-2.5 py-2.5 text-xs text-gray-700 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $help->customer->name ?? '-' }}</span>
                                        @if(!empty($help->customer->is_shadow_banned))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200" title="Akun ini sedang dalam status Shadow Ban">👻 Shadow</span>
                                        @endif
                                        @if(($help->customer->status ?? '') === 'blocked')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 border border-red-200" title="Akun ini sedang diblokir">⛔ Blokir</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2.5 py-2.5 text-xs text-gray-600 whitespace-nowrap">{{ $help->city->name ?? '-' }}</td>
                                <td class="px-2.5 py-2.5 text-xs text-gray-600 whitespace-nowrap">{{ $help->category->name ?? '-' }}</td>
                                <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                    @if(in_array($help->status, ['komplain', 'disputed']))
                                        <span class="px-2.5 py-0.5 inline-flex items-center gap-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-300 animate-pulse">
                                            ⚠️ Komplain / Mediasi
                                        </span>
                                    @elseif($help->status === 'waiting_customer_confirmation')
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-orange-50 text-orange-700 border border-orange-200">Menunggu Konfirmasi Customer</span>
                                    @elseif($help->status === 'menunggu_mitra')
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Menunggu Mitra</span>
                                    @elseif(in_array($help->status, ['partner_on_the_way', 'taken', 'in_progress', 'sedang_diproses']))
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">Aktif</span>
                                    @elseif($help->isSelesai())
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">Selesai</span>
                                    @elseif($help->status === 'rejected')
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Ditolak</span>
                                    @elseif($help->isDibatalkan())
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Dibatalkan</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ ucfirst(str_replace('_', ' ', $help->status)) }}</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.helps.show', $help->id) }}" wire:navigate
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-xs font-medium transition" title="Lihat Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6">
                                    <x-empty-state 
                                        icon="clipboard" 
                                        title="Tidak Ada Data Bantuan" 
                                        description="Tidak ditemukan data bantuan pada filter atau pencarian saat ini." 
                                        compact 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($helps->hasPages())
                <div class="mt-4">{{ $helps->links() }}</div>
            @endif
    </div>
