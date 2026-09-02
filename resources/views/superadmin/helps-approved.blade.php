
<div>
    <div class="space-y-6">
        <div class="flex justify-end mb-4">
            <input wire:model.debounce.500ms="search" type="text" placeholder="Cari bantuan..." class="px-3.5 py-2 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-64 shadow-sm" />
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Judul</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Kota</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jumlah</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($helps as $help)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-3 text-center text-xs text-gray-500 font-medium whitespace-nowrap">{{ $helps->firstItem() + $loop->index }}</td>
                                <td class="px-2.5 py-3">
                                    <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $help->title }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">ID: {{ $help->order_id ?? '#' . $help->id }}</div>
                                </td>
                                <td class="px-2.5 py-3 text-xs text-gray-700">{{ $help->customer->name ?? '-' }}</td>
                                <td class="px-2.5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                        {{ $help->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ in_array($help->status, ['komplain', 'disputed']) ? 'bg-red-100 text-red-800 border border-red-300 animate-pulse' :
                                          ($help->status === 'menunggu_mitra' ? 'bg-amber-100 text-amber-800' : 
                                          ($help->status === 'partner_on_the_way' || $help->status === 'waiting_customer_confirmation' ? 'bg-blue-100 text-blue-800' : 
                                          ($help->status === 'selesai' || $help->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 
                                          ($help->status === 'rejected' || $help->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))) }}">
                                        {{ in_array($help->status, ['komplain', 'disputed']) ? '⚠️ Mediasi Komplain' : str_replace('_', ' ', $help->status) }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 text-xs text-gray-600 whitespace-nowrap">{{ $help->city->name ?? '-' }}</td>
                                <td class="px-2.5 py-3 text-xs font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($help->amount ?? 0,0,',','.') }}</td>
                                <td class="px-2.5 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $help->created_at?->format('d M Y') }}</td>
                                <td class="px-2.5 py-2 text-left whitespace-nowrap">
                                    <button
                                        wire:click="openDetailModal({{ $help->id }})"
                                        class="px-3 py-1 {{ in_array($help->status, ['komplain', 'disputed']) ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} rounded-lg text-xs font-semibold transition shadow-2xs cursor-pointer">
                                        {{ in_array($help->status, ['komplain', 'disputed']) ? 'Mediasi' : 'Proses' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm font-medium">Tidak ada bantuan disetujui</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($helps, 'hasPages') && $helps->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $helps->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($showRejectModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeRejectModal"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tolak Permintaan Bantuan</h3>
                        <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[220px]">{{ $rejectingHelpTitle }}</p>
                    </div>
                </div>
                <button wire:click="closeRejectModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea
                        wire:model="rejectionReason"
                        rows="3"
                        placeholder="Contoh: Permintaan melanggar pedoman komunitas atau konten tidak pantas..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
                    @error('rejectionReason')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button
                        wire:click="closeRejectModal"
                        type="button"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button
                        wire:click="confirmReject"
                        type="button"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                        Ya, Tolak Bantuan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showDetailModal && $detailHelp)
    <div class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeDetailModal"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/80">
                <div>
                    <h3 class="text-base font-bold text-gray-900">{{ $detailHelp->title }}</h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">ID: {{ $detailHelp->order_id ?? '#' . $detailHelp->id }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full uppercase tracking-wider
                        {{ in_array($detailHelp->status, ['komplain', 'disputed']) ? 'bg-red-100 text-red-800 border border-red-300 animate-pulse' :
                          ($detailHelp->status === 'menunggu_mitra' ? 'bg-amber-100 text-amber-800' :
                          ($detailHelp->status === 'partner_on_the_way' || $detailHelp->status === 'waiting_customer_confirmation' ? 'bg-blue-100 text-blue-800' :
                          ($detailHelp->status === 'selesai' || $detailHelp->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                          ($detailHelp->status === 'rejected' || $detailHelp->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))) }}">
                        {{ in_array($detailHelp->status, ['komplain', 'disputed']) ? '⚠️ Mediasi Komplain' : str_replace('_', ' ', $detailHelp->status) }}
                    </span>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto space-y-5">
                @if(in_array($detailHelp->status, ['komplain', 'disputed']))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-900 space-y-2">
                        <div class="flex items-center justify-between font-bold">
                            <span class="flex items-center gap-1">⚠️ Sengketa Komplain Customer</span>
                            <span class="text-[10px] text-red-600 font-normal">{{ $detailHelp->complaint_submitted_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-red-700 leading-relaxed">
                            Customer mengajukan komplain hasil kerja. Anda dapat menyetujui refund atau menolak komplain langsung di bawah ini.
                        </p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Customer / Pemohon</h4>
                        @if($detailHelp->customer)
                            <p class="text-sm font-semibold text-gray-900">{{ $detailHelp->customer->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $detailHelp->customer->email }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $detailHelp->customer->phone ?? '-' }}</p>
                        @else
                            <p class="text-xs text-gray-400 italic">User terhapus</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mitra / Relawan</h4>
                        @if($detailHelp->mitra)
                            <p class="text-sm font-semibold text-gray-900">{{ $detailHelp->mitra->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $detailHelp->mitra->email }}</p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $detailHelp->mitra->phone ?? '-' }}</p>
                        @else
                            <p class="text-xs text-gray-400 italic">Belum ada mitra</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200/80 space-y-3">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rincian Bantuan</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <span class="text-gray-400 block">Kota</span>
                            <span class="font-semibold text-gray-800">{{ $detailHelp->city->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Kategori</span>
                            <span class="font-semibold text-gray-800">{{ $detailHelp->category->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Nominal Imbalan</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($detailHelp->amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Biaya Admin</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($detailHelp->admin_fee ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Total Transaksi</span>
                            <span class="font-bold text-primary-600">Rp {{ number_format($detailHelp->total_amount ?? ($detailHelp->amount + $detailHelp->admin_fee), 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Dibuat Pada</span>
                            <span class="font-semibold text-gray-800">{{ $detailHelp->created_at?->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                @if($detailHelp->completion_photo || $detailHelp->complaint_photo)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 text-xs">
                            <h5 class="font-bold text-gray-700 mb-2 flex items-center gap-1">
                                <span class="text-green-600">✓</span> Bukti Selesai Mitra
                            </h5>
                            @if($detailHelp->completion_photo)
                                <a href="{{ asset('storage/' . $detailHelp->completion_photo) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 mb-2">
                                    <img src="{{ asset('storage/' . $detailHelp->completion_photo) }}" alt="Bukti Mitra" class="w-full h-32 object-cover">
                                </a>
                                @if($detailHelp->completion_notes)
                                    <p class="text-[11px] text-gray-600 italic">"{{ $detailHelp->completion_notes }}"</p>
                                @endif
                            @else
                                <p class="text-gray-400 italic">Tidak ada foto</p>
                            @endif
                        </div>

                        <div class="bg-red-50/50 rounded-xl p-3 border border-red-200 text-xs">
                            <h5 class="font-bold text-red-700 mb-2 flex items-center gap-1">
                                <span>⚠️</span> Bukti Komplain Customer
                            </h5>
                            @if($detailHelp->complaint_photo)
                                <a href="{{ asset('storage/' . $detailHelp->complaint_photo) }}" target="_blank" class="block rounded-lg overflow-hidden border border-red-200 mb-2">
                                    <img src="{{ asset('storage/' . $detailHelp->complaint_photo) }}" alt="Bukti Customer" class="w-full h-32 object-cover">
                                </a>
                            @endif
                            @if($detailHelp->complaint_reason)
                                <p class="text-[11px] text-red-900 italic">"{{ $detailHelp->complaint_reason }}"</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi Bantuan</h4>
                    <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">{{ $detailHelp->description ?? '-' }}</p>
                </div>

                @if($detailHelp->location || $detailHelp->full_address)
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200/80">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Lokasi & Alamat Lengkap</h4>
                        <p class="text-xs font-semibold text-gray-900">{{ $detailHelp->location ?? '-' }}</p>
                        @if($detailHelp->full_address)
                            <p class="text-xs text-gray-600 mt-1">{{ $detailHelp->full_address }}</p>
                        @endif
                    </div>
                @endif

                @if($detailHelp->admin_notes)
                    <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                        <h4 class="text-xs font-bold text-red-700 uppercase tracking-wider mb-1">Catatan Admin / Alasan Penolakan</h4>
                        <p class="text-xs text-red-800">{{ $detailHelp->admin_notes }}</p>
                    </div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between flex-wrap gap-2">
                <button wire:click="closeDetailModal"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Tutup
                </button>

                <div class="flex items-center gap-2">
                    @if(in_array($detailHelp->status, ['komplain', 'disputed']))
                        <button wire:click="approveRefund({{ $detailHelp->id }})"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                            Setujui Refund Customer
                        </button>
                        <button wire:click="rejectComplaint({{ $detailHelp->id }})"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                            Tolak Komplain & Selesaikan
                        </button>
                    @elseif(!in_array($detailHelp->status, ['selesai', 'completed', 'dibatalkan', 'cancelled', 'rejected']))
                        <button wire:click="openRejectModalFromDetail({{ $detailHelp->id }})"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                            Tolak Bantuan Ini
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
