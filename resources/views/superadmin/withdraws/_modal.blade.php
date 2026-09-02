<div id="superadmin-withdraw-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <div id="superadmin-withdraw-modal-overlay" class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-md mx-auto bg-white rounded-xl shadow-2xl z-10 max-h-[90vh] overflow-y-auto no-scrollbar">
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Withdraw Request #{{ $withdraw->id }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Detail permintaan penarikan saldo</p>
                </div>
                <button id="close-superadmin-withdraw-modal"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Status Badge -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($withdraw->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">{{ $withdraw->user?->name ?? '-' }}</h4>
                        <p class="text-sm text-gray-500">ID Mitra: {{ $withdraw->user_id }}</p>
                    </div>
                </div>
                <div>
                    @if($withdraw->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Pending
                        </span>
                    @elseif($withdraw->status === 'processing')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing
                        </span>
                    @elseif($withdraw->status === 'success')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Success
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Failed
                        </span>
                    @endif
                </div>
            </div>

            <!-- Amount Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-900 mb-1">Jumlah Penarikan</p>
                        <p class="text-3xl font-bold text-blue-900">
                            Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-blue-700 mb-1">Bank</p>
                        <p class="text-sm font-semibold text-blue-900">{{ $withdraw->bank_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 mb-1">No. Rekening</p>
                        <p class="text-sm font-semibold text-blue-900">{{ $withdraw->account_number }}</p>
                    </div>
                </div>
                @if($withdraw->description)
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <p class="text-xs text-blue-700 mb-1">Keterangan</p>
                    <p class="text-sm text-blue-900">{{ $withdraw->description }}</p>
                </div>
                @endif
            </div>

            @if($withdraw->status === 'pending')
                <!-- Approval Form -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h5 class="text-sm font-semibold text-gray-900 mb-4">Form Approval</h5>
                    <form action="{{ route('superadmin.withdraws.approve', $withdraw) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="text" name="note" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                                    placeholder="Tambahkan catatan jika diperlukan" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Transfer <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="text" name="transfer_reference"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow"
                                    placeholder="Masukkan referensi transfer" />
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <button type="button" id="open-reject-local"
                                class="px-5 py-2.5 bg-white border-2 border-red-500 text-red-600 font-medium rounded-lg hover:bg-red-50 transition-colors duration-200">
                                <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak
                            </button>
                            <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 shadow-lg shadow-green-500/30 transition-all duration-200">
                                <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve & Potong Saldo
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Processed Info -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h5 class="text-sm font-semibold text-gray-900 mb-4">Informasi Pemrosesan</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($withdraw->status) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Diproses pada</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ $withdraw->processed_at ? $withdraw->processed_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                        @if($withdraw->external_id)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Referensi</span>
                            <span class="text-sm font-mono font-semibold text-gray-900">{{ $withdraw->external_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Fallback Reject Modal (used when modal is injected dynamically) -->
    <div id="withdraw-reject-modal-fallback" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div id="withdraw-reject-fallback-overlay" class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 z-10">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Tolak Permintaan Withdraw</h3>
                <button id="close-reject-fallback" class="text-gray-500 hover:text-gray-700">&times;</button>
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
                        <button type="button" id="cancel-reject-fallback"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Konfirmasi Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>