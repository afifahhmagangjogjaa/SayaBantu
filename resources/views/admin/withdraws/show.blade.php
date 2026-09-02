@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Withdraw</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi permintaan tarik saldo dari mitra (hanya tampilan, proses dilakukan oleh Super Admin).</p>
            </div>
            <div>
                <a href="{{ route('admin.withdraws.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900">Withdraw #{{ $withdraw->id }}</h2>
                    <p class="text-xs text-gray-500 mt-1">Permintaan dari mitra berikut informasinya.</p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Status</div>
                    @if($withdraw->status === 'pending')
                        <div class="inline-flex items-center px-3 py-1 rounded bg-yellow-100 text-yellow-800">Pending</div>
                    @elseif($withdraw->status === 'processing')
                        <div class="inline-flex items-center px-3 py-1 rounded bg-blue-100 text-blue-800">Processing</div>
                    @elseif($withdraw->status === 'success')
                        <div class="inline-flex items-center px-3 py-1 rounded bg-green-100 text-green-800">Success</div>
                    @else
                        <div class="inline-flex items-center px-3 py-1 rounded bg-red-100 text-red-800">Failed</div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-xs text-gray-500">Mitra</div>
                    <div class="font-semibold">{{ $withdraw->user?->name ?? '-' }} <span class="text-xs text-gray-400">(ID:
                            {{ $withdraw->user_id }})</span></div>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-xs text-gray-500">Saldo Saat Ini</div>
                    <div class="font-semibold">Rp {{ number_format($withdraw->user?->balance ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <div class="text-xs text-gray-500">Jumlah Permintaan</div>
                    <div class="font-semibold">Rp {{ number_format($withdraw->amount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded p-4 mb-4">
                <div class="text-sm text-gray-500">Bank / Rekening</div>
                <div class="font-medium">{{ $withdraw->bank_code }} / {{ $withdraw->account_number }}</div>
                <div class="text-sm text-gray-500 mt-3">Keterangan</div>
                <div>{{ $withdraw->description ?? '-' }}</div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800">
                        @if($withdraw->status === 'pending')
                            <span class="font-semibold">Menunggu Persetujuan Super Admin.</span> Permintaan ini belum diproses.
                        @elseif($withdraw->status === 'processing')
                            <span class="font-semibold">Sedang Diproses.</span> Super Admin sedang memproses transfer.
                        @elseif($withdraw->status === 'success')
                            <span class="font-semibold">Berhasil.</span>
                            Diproses pada: {{ $withdraw->processed_at ? $withdraw->processed_at->format('d M Y, H:i') : '-' }}
                            @if($withdraw->external_id) | Referensi: {{ $withdraw->external_id }} @endif
                        @else
                            <span class="font-semibold">Ditolak / Gagal.</span>
                            {{ $withdraw->note ?? 'Tanpa keterangan.' }}
                            Diproses pada: {{ $withdraw->processed_at ? $withdraw->processed_at->format('d M Y, H:i') : '-' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user() && auth()->user()->isSuperAdmin())
    <!-- Reject Confirmation Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div id="rejectModalOverlay" class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 z-10">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Tolak Permintaan Withdraw</h3>
                <button id="rejectModalClose" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Masukkan catatan penolakan (opsional) dan konfirmasi penolakan.</p>
                <form action="{{ route('superadmin.withdraws.reject', $withdraw) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan Penolakan</label>
                        <input type="text" name="note" class="mt-1 block w-full rounded border-gray-300"
                            placeholder="Contoh: Saldo tidak mencukupi" />
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" id="rejectModalCancel"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Konfirmasi Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var openBtn = document.getElementById('open-reject-modal');
            var modal = document.getElementById('rejectModal');
            var overlay = document.getElementById('rejectModalOverlay');
            var closeBtn = document.getElementById('rejectModalClose');
            var cancelBtn = document.getElementById('rejectModalCancel');

            function showModal() {
                modal.classList.remove('hidden');
            }
            function hideModal() {
                modal.classList.add('hidden');
            }

            if (openBtn) openBtn.addEventListener('click', showModal);
            if (overlay) overlay.addEventListener('click', hideModal);
            if (closeBtn) closeBtn.addEventListener('click', hideModal);
            if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
        })();
    </script>
    @endif
@endsection