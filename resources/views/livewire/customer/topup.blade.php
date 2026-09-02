@php
    $user = auth()->user();
@endphp

<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-md mx-auto px-4 py-6">
        <div class="bg-gradient-to-br from-primary-400 via-primary-500 to-primary-600 rounded-2xl p-4 mb-4 shadow-lg">
            <div class="flex items-center gap-3">
                <button onclick="history.back()" class="p-2 rounded-md text-white/90 bg-white/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-extrabold text-white">Top Up Saldo</h1>
                    <p class="text-sm text-white/90">Tambah saldo untuk melakukan pembayaran layanan</p>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-100 text-green-700">{{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700">{{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="submit" class="bg-white rounded-2xl shadow p-5">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih nominal</label>
                <div class="flex gap-2">
                    <button type="button" wire:click.prevent="$set('amount', 25000)"
                        class="flex-1 px-3 py-2 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100 transition">Rp
                        25.000</button>
                    <button type="button" wire:click.prevent="$set('amount', 50000)"
                        class="flex-1 px-3 py-2 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100 transition">Rp
                        50.000</button>
                    <button type="button" wire:click.prevent="$set('amount', 100000)"
                        class="flex-1 px-3 py-2 rounded-lg bg-primary-50 text-primary-700 font-semibold hover:bg-primary-100 transition">Rp
                        100.000</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal lain</label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-500 font-semibold text-sm select-none pointer-events-none">Rp</span>
                    <input id="amount" wire:model.defer="amount" name="amount" type="number" min="10000" max="10000000" step="1000"
                        oninput="if(parseInt(this.value) > 10000000) this.value = 10000000; else if(parseInt(this.value) < 0) this.value = 0;"
                        placeholder="50.000"
                        class="w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium text-gray-900">
                </div>
                @error('amount') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
                <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                    <span>Min: Rp 10.000 — Max: Rp 10.000.000</span>
                    <div>Preview: <span class="font-semibold text-gray-800">Rp {{ number_format($amount ?? 0, 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Metode Pembayaran</label>
                <div class="grid grid-cols-1 gap-2">
                    <label
                        class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                        :class="{ 'border-primary-500 bg-primary-50': $wire.method === 'bank' }">
                        <input type="radio" wire:model="method" name="method" value="bank" checked>
                        <div>
                            <div class="font-semibold">Transfer Bank</div>
                            <div class="text-xs text-gray-500">BCA | BRI | Mandiri | BNI | Permata</div>
                        </div>
                    </label>
                    <label
                        class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition"
                        :class="{ 'border-primary-500 bg-primary-50': $wire.method === 'ewallet' }">
                        <input type="radio" wire:model="method" name="method" value="ewallet">
                        <div>
                            <div class="font-semibold">E-Wallet & QRIS</div>
                            <div class="text-xs text-gray-500">GoPay | ShopeePay | QRIS</div>
                        </div>
                    </label>
                </div>
                @error('method') <div class="text-xs text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-primary-600 text-white rounded-lg px-4 py-3 font-bold hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Top Up Sekarang</span>
                    <span wire:loading>
                        <svg class="animate-spin inline-block w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>

            <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-xs text-blue-700">
                        <p class="font-semibold mb-1">Informasi Pembayaran:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pembayaran diproses secara real-time melalui Midtrans</li>
                            <li>Minimal top up adalah Rp 10.000</li>
                            <li>Saldo akan otomatis bertambah setelah pembayaran berhasil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    {{-- Midtrans Snap JS dimuat di luar komponen Livewire agar tidak di-reset saat re-render --}}
    <script
        src="https://{{ config('services.midtrans.is_production') ? 'app.midtrans.com' : 'app.sandbox.midtrans.com' }}/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <script>
        // Daftarkan listener setiap kali Livewire selesai update (bukan hanya saat init)
        document.addEventListener('livewire:init', function () {
            Livewire.on('openMidtransSnap', function (event) {
                // Livewire v3 membungkus payload dalam array
                let snapToken = null;
                if (Array.isArray(event) && event.length > 0) {
                    snapToken = event[0].snapToken || event[0];
                } else if (event && event.snapToken) {
                    snapToken = event.snapToken;
                }

                console.log('openMidtransSnap received, token:', snapToken);

                if (!snapToken) {
                    console.error('Snap token kosong!', event);
                    alert('Gagal mendapatkan token pembayaran. Coba lagi.');
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    console.error('snap.js belum termuat!');
                    alert('Sistem pembayaran gagal dimuat. Pastikan internet aktif dan nonaktifkan ad-blocker.');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        console.log('Pembayaran sukses:', result);
                        try {
                            fetch('{{ route('topup.client-callback') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: result.order_id, payment_status: 'success' })
                            }).finally(function () {
                                window.location.href = '{{ route("customer.topup") }}?status=success';
                            });
                        } catch (e) {
                            window.location.href = '{{ route("customer.topup") }}?status=success';
                        }
                    },
                    onPending: function (result) {
                        console.log('onPending fired (VA):', result);
                        // Untuk VA payment: kirim ke server agar bisa update saldo di development
                        // Di production, update saldo akan dilakukan via Midtrans webhook
                        try {
                            fetch('{{ route('topup.client-callback') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    order_id: result.order_id,
                                    payment_status: 'pending_va',
                                    va_number: result.va_numbers ? result.va_numbers[0]?.va_number : null
                                })
                            }).finally(function() {
                                window.location.href = '{{ route("customer.topup") }}?status=pending';
                            });
                        } catch(e) {
                            window.location.href = '{{ route("customer.topup") }}?status=pending';
                        }
                    },
                    onError: function (result) {
                        console.error('Pembayaran error:', result);
                        window.location.href = '{{ route("customer.topup") }}?status=error';
                    },
                    onClose: function () {
                        console.log('Popup ditutup tanpa pembayaran');
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Custom styles for Alpine.js transitions if needed */
    </style>
@endpush