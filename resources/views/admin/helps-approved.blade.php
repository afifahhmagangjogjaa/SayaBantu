    <div class="space-y-6">
        @isset($totalHelps)
        <!-- Stats Cards for Index -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
                <p class="text-xs font-medium text-gray-500">Total Bantuan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalHelps) }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Dari kota yang Anda kelola</p>
            </div>
            <div class="bg-amber-50 p-5 rounded-2xl shadow-sm border border-amber-200">
                <p class="text-xs font-medium text-amber-700">Menunggu Mitra</p>
                <p class="text-2xl font-bold text-amber-900 mt-1">{{ number_format($waitingMitraHelps) }}</p>
                <p class="text-[10px] text-amber-500 mt-0.5">Belum ada mitra yang mengambil</p>
            </div>
            <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-200 cursor-pointer hover:border-red-300 transition" wire:click="$set('statusFilter', 'komplain')">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-red-700">Komplain / Mediasi</p>
                    <span class="w-2 h-2 rounded-full {{ ($complaintHelps ?? 0) > 0 ? 'bg-red-500 animate-pulse' : 'bg-gray-300' }}"></span>
                </div>
                <p class="text-2xl font-bold text-red-900 mt-1">{{ number_format($complaintHelps ?? 0) }}</p>
                <p class="text-[10px] text-red-500 mt-0.5">Perlu keputusan / mediasi admin</p>
            </div>
            <div class="bg-green-50 p-5 rounded-2xl shadow-sm border border-green-200">
                <p class="text-xs font-medium text-green-700">Selesai</p>
                <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($completedHelps) }}</p>
                <p class="text-[10px] text-green-600 mt-0.5">Pekerjaan telah diselesaikan</p>
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
                    <select wire:model.live="statusFilter" class="px-3 py-1.5 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
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
                                    <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $help->title }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">ID: {{ $help->order_id ?? '#' . $help->id }}</div>
                                </td>
                                <td class="px-2.5 py-2.5 text-xs text-gray-700 whitespace-nowrap">{{ $help->customer->name ?? '-' }}</td>
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
                                    @elseif($help->status === 'selesai')
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">Selesai</span>
                                    @elseif(in_array($help->status, ['rejected', 'dibatalkan']))
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Ditolak / Batal</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ ucfirst(str_replace('_', ' ', $help->status)) }}</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.helps.show', $help->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-xs font-medium transition" title="Lihat Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">Tidak ada data bantuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($helps->hasPages())
                <div class="mt-4">{{ $helps->links() }}</div>
            @endif
    </div>
