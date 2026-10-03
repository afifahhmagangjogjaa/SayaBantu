<div class="space-y-6">
    {{-- Flash Message --}}
    @if(session('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.helps') }}" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Manajemen Bantuan
        </a>
    </div>

    {{-- Banner Mediasi Komplain (Jika status komplain) --}}
    {{-- Banner Mediasi Komplain (Jika status komplain) --}}
    @if(in_array($help->status, ['komplain', 'disputed']))
        <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-2xl p-5 text-white shadow-md flex items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="font-bold text-base">Pesanan Memerlukan Mediasi Komplain</h3>
                    <p class="text-xs text-red-100 mt-0.5 leading-relaxed">Customer mengajukan komplain penolakan hasil kerja pada {{ $help->complaint_submitted_at?->format('d M Y, H:i') ?? '-' }}. Mohon periksa bukti foto kedua belah pihak di bawah, lalu ambil keputusan pada panel mediasi di sebelah kanan.</p>
                </div>
            </div>
        </div>
    @elseif($help->complaint_resolution)
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 text-xs text-purple-900 flex items-center justify-between shadow-2xs">
            <div>
                <span class="font-bold text-purple-950 block text-sm flex items-center gap-1.5">⚖️ Riwayat Mediasi Komplain</span>
                <p class="mt-1 text-purple-900">Keputusan: <strong>{{ $help->complaint_resolution === 'refunded' ? 'Dana Dikembalikan ke Customer (Refund)' : 'Komplain Ditolak & Dana Dicairkan ke Mitra' }}</strong> pada {{ $help->complaint_resolved_at?->format('d M Y H:i') }}</p>
                @if($help->complaint_admin_notes)
                    <p class="text-gray-700 italic mt-1.5 bg-white p-2.5 rounded-xl border border-purple-100">Catatan Admin: "{{ $help->complaint_admin_notes }}"</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Banner Pembatalan (Hanya jika pembatalan manual, bukan refund mediasi) --}}
    @if(((in_array($help->status, ['dibatalkan', 'cancelled']) && empty($help->complaint_resolution)) || $help->customer_cancel_reason || $help->partner_cancel_reason))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 flex items-start gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold shrink-0">
                ⚠️
            </div>
            <div class="flex-1 space-y-1">
                <span class="font-bold text-sm text-amber-900 block">Riwayat Pembatalan Pesanan</span>
                @if($help->customer_cancel_reason)
                    <div class="bg-white p-2.5 rounded-xl border border-amber-100 mt-1">
                        <span class="font-semibold text-gray-800">Dibatalkan oleh Customer:</span>
                        <p class="italic text-gray-700 mt-0.5">"{{ $help->customer_cancel_reason }}"</p>
                        @php
                            $custCancelTime = $help->cancelled_at ?? $help->updated_at;
                        @endphp
                        <p class="text-[10px] text-gray-500 mt-1 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Waktu: <strong class="text-gray-700">{{ $custCancelTime ? $custCancelTime->format('d M Y, H:i') . ' WIB' : '-' }}</strong></span>
                        </p>
                    </div>
                @endif
                @if($help->partner_cancel_reason)
                    <div class="bg-white p-2.5 rounded-xl border border-amber-100 mt-1">
                        <div class="flex items-center justify-between flex-wrap gap-1.5">
                            <span class="font-semibold text-gray-800">
                                @if(str_contains(strtolower($help->partner_cancel_reason), 'rekan jasa lain') || str_contains(strtolower($help->partner_cancel_reason), 'keberangkatan'))
                                    Dibatalkan & Ganti Rekan Jasa:
                                @else
                                    Dibatalkan oleh Rekan Jasa (Mitra):
                                @endif
                            </span>
                            @if($help->lastCancelledMitra)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-900 bg-amber-100/90 px-2 py-0.5 rounded-md border border-amber-200">
                                    <span>Mitra yang Dibatalkan:</span>
                                    <span class="text-gray-900 font-bold">{{ $help->lastCancelledMitra->name }}</span>
                                    @if($help->lastCancelledMitra->phone)
                                        <span class="text-gray-500 font-mono text-[10px]">({{ $help->lastCancelledMitra->phone }})</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                        <p class="italic text-gray-700 mt-1">"{{ $help->partner_cancel_reason }}"</p>
                        @php
                            $partnerCancelTime = $help->partner_cancel_requested_at ?? $help->cancelled_at ?? $help->updated_at;
                        @endphp
                        <p class="text-[10px] text-gray-500 mt-1 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Waktu Pembatalan: <strong class="text-gray-700">{{ $partnerCancelTime ? $partnerCancelTime->format('d M Y, H:i') . ' WIB' : '-' }}</strong></span>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-xl font-bold text-gray-900">{{ $help->title }}</h2>
                            @if($help->isUrgent())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider">⚡ Urgent</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 font-mono mt-1">ID: {{ $help->order_id ?? '#' . $help->id }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        @if(in_array($help->status, ['komplain', 'disputed']))
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-300 animate-pulse">⚠️ Komplain / Mediasi</span>
                        @elseif($help->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-orange-50 text-orange-700 border border-orange-200">Menunggu Persetujuan</span>
                            <button type="button" wire:click="openDecisionModal('reject_help')"
                                class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Bantuan
                            </button>
                        @elseif($help->status === 'waiting_customer_confirmation')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-orange-50 text-orange-700 border border-orange-200">Menunggu Konfirmasi Customer</span>
                        @elseif($help->status === 'menunggu_mitra')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Menunggu Mitra</span>
                            <button type="button" wire:click="openDecisionModal('reject_help')"
                                class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak Bantuan
                            </button>
                        @elseif(in_array($help->status, ['partner_on_the_way', 'taken', 'in_progress', 'sedang_diproses']))
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">Aktif / Berlangsung</span>
                        @elseif($help->status === 'selesai')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">Selesai</span>
                        @elseif($help->status === 'rejected')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Ditolak</span>
                        @elseif($help->status === 'dibatalkan' || $help->status === 'cancelled')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Dibatalkan</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">{{ ucfirst(str_replace('_', ' ', $help->status)) }}</span>
                        @endif
                    </div>
                </div>

                <div class="prose prose-sm max-w-none text-gray-700">
                    <p>{{ $help->description ?? '-' }}</p>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Informasi Detail</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Lokasi</p>
                        <p class="font-medium text-gray-800">{{ $help->location ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Kota</p>
                        <p class="font-medium text-gray-800">{{ $help->city->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Kategori</p>
                        <p class="font-medium text-gray-800">{{ $help->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Nominal Jasa</p>
                        <p class="font-medium text-gray-800">{{ $help->amount ? 'Rp ' . number_format($help->amount, 0, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Biaya Admin</p>
                        <p class="font-medium text-gray-800">{{ $help->admin_fee ? 'Rp ' . number_format($help->admin_fee, 0, ',', '.') : 'Rp 0' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Total Dibayar</p>
                        <p class="font-bold text-gray-900">{{ 'Rp ' . number_format($help->total_amount ?? ($help->amount + ($help->admin_fee ?? 0)), 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Dibuat</p>
                        <p class="font-medium text-gray-800">{{ $help->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Tipe Layanan</p>
                        <p class="font-bold {{ $help->isUrgent() ? 'text-red-600' : 'text-blue-600' }}">
                            {{ $help->isUrgent() ? '⚡ Mendesak (Urgent)' : '📅 Terjadwal' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Dijadwalkan</p>
                        <p class="font-medium {{ $help->isUrgent() ? 'text-red-600 font-semibold' : 'text-gray-800' }}">{{ $help->scheduled_at ? \Carbon\Carbon::parse($help->scheduled_at)->format('d M Y, H:i') : 'Segera' }}</p>
                    </div>
                </div>
            </div>

            {{-- Perbandingan Bukti: Bukti Selesai Mitra VS Bukti Komplain Customer --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Bukti Pekerjaan Selesai (Mitra) --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        1. Bukti Selesai dari Mitra
                    </h3>
                    @if($help->completion_photo)
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                            <a href="{{ asset('storage/' . $help->completion_photo) }}" target="_blank" class="block group relative">
                                <img src="{{ asset('storage/' . $help->completion_photo) }}" alt="Bukti Selesai Mitra" class="w-full h-48 object-cover group-hover:opacity-95 transition">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    Lihat Foto Asli
                                </div>
                            </a>
                            @if($help->completion_notes)
                                <div class="p-3 bg-gray-50 text-xs text-gray-700 border-t border-gray-100">
                                    <span class="font-semibold text-gray-900 block mb-0.5">Catatan Mitra:</span>
                                    <p class="italic">"{{ $help->completion_notes }}"</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            Belum ada bukti unggahan dari mitra
                        </div>
                    @endif
                </div>

                {{-- Bukti Komplain (Customer) --}}
                <div class="bg-white border {{ $help->complaint_photo ? 'border-red-200 ring-1 ring-red-100' : 'border-gray-200' }} rounded-2xl p-5 shadow-sm">
                    <h3 class="text-xs font-bold text-red-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        2. Bukti Komplain dari Customer
                    </h3>
                    @if($help->complaint_photo || $help->complaint_reason)
                        <div class="rounded-xl overflow-hidden border border-red-100 bg-red-50/20">
                            @if($help->complaint_photo)
                                <a href="{{ asset('storage/' . $help->complaint_photo) }}" target="_blank" class="block group relative">
                                    <img src="{{ asset('storage/' . $help->complaint_photo) }}" alt="Bukti Komplain Customer" class="w-full h-48 object-cover group-hover:opacity-95 transition">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        Lihat Foto Asli
                                    </div>
                                </a>
                            @endif
                            @if($help->complaint_reason)
                                <div class="p-3 bg-red-50/60 text-xs text-red-900 border-t border-red-100">
                                    <span class="font-semibold block mb-0.5">Alasan Komplain Customer:</span>
                                    <p class="italic">"{{ $help->complaint_reason }}"</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            Tidak ada komplain dari customer
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar: Customer, Mitra, Actions --}}
        <div class="space-y-5">
            {{-- Panel Mediasi Khusus (Jika status komplain) --}}
            @if(in_array($help->status, ['komplain', 'disputed']))
                <div class="bg-white border-2 border-red-300 rounded-2xl p-5 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold text-red-700 uppercase tracking-wider flex items-center gap-1.5">
                        ⚖️ Keputusan Mediasi Admin
                    </h3>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Tentukan putusan sengketa berdasarkan kebenaran bukti foto mitra vs customer di atas:
                    </p>
                    <div class="space-y-2 pt-1">
                        <button type="button" wire:click="openDecisionModal('refund')"
                            class="w-full py-2.5 px-3 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                            <span>Setujui Refund Customer</span>
                        </button>
                        <button type="button" wire:click="openDecisionModal('reject_complaint')"
                            class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                            <span>Tolak Komplain & Cairkan ke Mitra</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Customer --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Customer / Pemohon</h3>
                    @if($help->customer)
                        @if($help->customer->status === 'blocked')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">⛔ Diblokir</span>
                        @elseif($help->customer->is_shadow_banned)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">👻 Shadow Ban</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        @endif
                    @endif
                </div>
                @if($help->customer)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($help->customer->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $help->customer->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $help->customer->email }}</p>
                            <p class="text-xs text-gray-400">{{ $help->customer->phone ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Quick Action Moderasi --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                        <button type="button" wire:click="toggleShadowBan({{ $help->customer->id }})"
                            wire:confirm="{{ $help->customer->is_shadow_banned ? 'Apakah Anda yakin ingin membebaskan ' . $help->customer->name . ' dari Shadow Ban?' : 'Apakah Anda yakin ingin menerapkan Shadow Ban pada ' . $help->customer->name . '? Permintaan bantuan berikutnya akan disembunyikan secara senyap dari mitra.' }}"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer {{ $help->customer->is_shadow_banned ? 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            <span>👻</span>
                            <span>{{ $help->customer->is_shadow_banned ? 'Lepas Shadow' : 'Shadow Ban' }}</span>
                        </button>
                        <button type="button" wire:click="toggleBlockUser({{ $help->customer->id }})"
                            wire:confirm="{{ $help->customer->status === 'blocked' ? 'Apakah Anda yakin ingin membuka blokir akun ' . $help->customer->name . '?' : 'Apakah Anda yakin ingin memblokir akun ' . $help->customer->name . '? Pengguna tidak dapat login lagi.' }}"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer {{ $help->customer->status === 'blocked' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                            <span>⛔</span>
                            <span>{{ $help->customer->status === 'blocked' ? 'Buka Blokir' : 'Blokir Akun' }}</span>
                        </button>
                    </div>
                @else
                    <p class="text-sm text-gray-400">-</p>
                @endif
            </div>

            {{-- Mitra --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mitra / Pelaksana</h3>
                    @if($help->mitra)
                        @if($help->mitra->status === 'blocked')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">⛔ Diblokir</span>
                        @elseif($help->mitra->is_shadow_banned)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">👻 Shadow Ban</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        @endif
                    @endif
                </div>
                @if($help->mitra)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($help->mitra->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $help->mitra->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $help->mitra->email }}</p>
                            <p class="text-xs text-gray-400">{{ $help->mitra->phone ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Quick Action Moderasi Mitra --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center gap-2">
                        <button type="button" wire:click="toggleShadowBan({{ $help->mitra->id }})"
                            wire:confirm="{{ $help->mitra->is_shadow_banned ? 'Apakah Anda yakin ingin membebaskan ' . $help->mitra->name . ' dari Shadow Ban?' : 'Apakah Anda yakin ingin menerapkan Shadow Ban pada mitra ' . $help->mitra->name . '? Mitra tidak akan menerima pesanan baru.' }}"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer {{ $help->mitra->is_shadow_banned ? 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            <span>👻</span>
                            <span>{{ $help->mitra->is_shadow_banned ? 'Lepas Shadow' : 'Shadow Ban' }}</span>
                        </button>
                        <button type="button" wire:click="toggleBlockUser({{ $help->mitra->id }})"
                            wire:confirm="{{ $help->mitra->status === 'blocked' ? 'Apakah Anda yakin ingin membuka blokir mitra ' . $help->mitra->name . '?' : 'Apakah Anda yakin ingin memblokir akun mitra ' . $help->mitra->name . '?' }}"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl border text-xs font-semibold transition cursor-pointer {{ $help->mitra->status === 'blocked' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                            <span>⛔</span>
                            <span>{{ $help->mitra->status === 'blocked' ? 'Buka Blokir' : 'Blokir Akun' }}</span>
                        </button>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Belum ada mitra</p>
                @endif
            </div>

            {{-- Rating & Review (if available) --}}
            @php
                $helpRating = $help->ratings()->where('type', 'customer_to_mitra')->first();
            @endphp
            @if($helpRating)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rating Customer</h3>
                        @if($helpRating->is_anonymous)
                            <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200 font-semibold">Anonim di Mitra</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 mb-2">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $helpRating->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-1 text-xs font-bold text-gray-900">{{ $helpRating->rating }}/5</span>
                    </div>
                    @if($helpRating->review)
                        <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl italic mb-2">"{{ $helpRating->review }}"</p>
                    @endif
                    <div class="text-[11px] text-gray-400 border-t border-gray-100 pt-2">
                        Pemberi Rating Asli: <strong class="text-gray-700">{{ $helpRating->rater->name ?? 'User' }}</strong> ({{ $helpRating->rater->email ?? '-' }})
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Keputusan Admin --}}
    @if($showDecisionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col">
                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between {{ $decisionType === 'refund' || $decisionType === 'reject_help' ? 'bg-red-50' : 'bg-emerald-50' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full {{ $decisionType === 'refund' || $decisionType === 'reject_help' ? 'bg-red-500 text-white' : 'bg-emerald-600 text-white' }} flex items-center justify-center font-bold text-sm">
                            {{ $decisionType === 'refund' ? '💸' : ($decisionType === 'reject_help' ? '✕' : '✅') }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">
                                @if($decisionType === 'refund')
                                    Konfirmasi Refund ke Customer
                                @elseif($decisionType === 'reject_help')
                                    Konfirmasi Tolak Permintaan Bantuan
                                @else
                                    Konfirmasi Tolak Komplain & Cairkan ke Mitra
                                @endif
                            </h3>
                            <p class="text-[11px] text-gray-500">Putusan moderasi oleh Admin</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDecisionModal" class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-700 transition text-xs">✕</button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="processDecision" class="p-5 space-y-4">
                    <div class="p-3 {{ $decisionType === 'refund' || $decisionType === 'reject_help' ? 'bg-red-50 text-red-800 border-red-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }} border rounded-xl text-xs leading-relaxed">
                        @if($decisionType === 'refund')
                            <strong>Perhatian:</strong> Saldo sebesar <strong>Rp {{ number_format($help->amount + ($help->admin_fee ?? 0), 0, ',', '.') }}</strong> akan dikembalikan ke dompet customer. Status pesanan akan diubah menjadi <em>Dibatalkan</em>.
                        @elseif($decisionType === 'reject_help')
                            <strong>Perhatian:</strong> Permintaan bantuan <strong>"{{ $help->title }}"</strong> akan ditolak. Saldo sebesar <strong>Rp {{ number_format($help->amount + ($help->admin_fee ?? 0), 0, ',', '.') }}</strong> akan otomatis dikembalikan 100% ke dompet customer.
                        @else
                            <strong>Perhatian:</strong> Dana sebesar <strong>Rp {{ number_format($help->amount, 0, ',', '.') }}</strong> akan langsung dicairkan ke dompet saldo mitra. Status pesanan akan diubah menjadi <em>Selesai</em>.
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1">
                            {{ $decisionType === 'reject_help' ? 'Alasan Penolakan (Akan Dilihat Customer)' : 'Catatan Keputusan Mediasi Admin' }} <span class="text-red-500 font-bold">*</span>
                        </label>
                        <textarea wire:model="admin_notes" rows="3" placeholder="{{ $decisionType === 'reject_help' ? 'Contoh: Permintaan tidak sesuai ketentuan layanan atau informasi lokasi tidak jelas...' : 'Wajib masukkan alasan keputusan atau catatan mediasi untuk arsip...' }}"
                            class="w-full px-3 py-2 text-xs border @error('admin_notes') border-red-400 bg-red-50/50 focus:border-red-500 focus:ring-red-200 @else border-gray-300 bg-gray-50 focus:bg-white focus:ring-primary-500 @enderror rounded-xl focus:ring-2 transition"></textarea>
                        @error('admin_notes')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" wire:click="closeDecisionModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 {{ $decisionType === 'refund' || $decisionType === 'reject_help' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer flex items-center gap-1.5">
                            <span wire:loading.remove wire:target="processDecision">{{ $decisionType === 'reject_help' ? 'Tolak Bantuan' : 'Eksekusi Keputusan' }}</span>
                            <span wire:loading wire:target="processDecision" class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
