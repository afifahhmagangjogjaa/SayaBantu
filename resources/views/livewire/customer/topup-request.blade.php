<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white pb-24">
    <!-- Header -->
    <div class="bg-gradient-to-br from-primary-500 to-primary-600 px-5 pt-6 pb-8 rounded-b-3xl shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.dashboard') }}" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-white">Top-Up Saldo</h1>
            </div>
            <a href="{{ route('customer.topup.history') }}" class="flex items-center gap-1 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Riwayat</span>
            </a>
        </div>
        
        <!-- Progress Indicator -->
        <div class="flex items-center justify-between max-w-md mx-auto mt-6">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full {{ $currentStep >= 1 ? 'bg-white text-primary-600' : 'bg-white/30 text-white' }} flex items-center justify-center font-bold">
                    1
                </div>
                <span class="text-xs text-white ml-2">Data</span>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep >= 2 ? 'bg-white' : 'bg-white/30' }}"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full {{ $currentStep >= 2 ? 'bg-white text-primary-600' : 'bg-white/30 text-white' }} flex items-center justify-center font-bold">
                    2
                </div>
                <span class="text-xs text-white ml-2">Detail</span>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep >= 3 ? 'bg-white' : 'bg-white/30' }}"></div>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full {{ $currentStep >= 3 ? 'bg-white text-primary-600' : 'bg-white/30 text-white' }} flex items-center justify-center font-bold">
                    3
                </div>
                <span class="text-xs text-white ml-2">Bayar</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="px-5 py-6">
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Info jika data tersimpan -->
        @if(session('topup_form_data') && $currentStep > 1)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-2">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold">Data Anda Tersimpan</p>
                    <p class="text-xs mt-0.5">Data form tidak akan hilang meskipun halaman di-refresh</p>
                </div>
            </div>
        @endif

        <!-- Step 1: Form Pengisian Data -->
        @if ($currentStep === 1)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Langkah 1: Isi Data Pengisian</h2>
                
                <form wire:submit.prevent="nextStep">
                    <!-- Nominal -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal Top-Up *</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-gray-500 font-semibold text-sm select-none pointer-events-none">Rp</span>
                            <input type="number" wire:model.live="amount" wire:change="calculateFees"
                                oninput="if(parseInt(this.value) > 10000000) this.value = 10000000; else if(parseInt(this.value) < 0) this.value = 0;"
                                class="w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium text-gray-900"
                                placeholder="0" min="10000" max="10000000">
                        </div>
                        @error('amount') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Min: Rp 10.000 - Max: Rp 10.000.000</p>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Cepat:</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button type="button" wire:click="setQuickAmount(20000)"
                                class="px-3 py-2 text-sm font-semibold rounded-lg border-2 {{ $amount == 20000 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-700 hover:border-primary-300' }}">
                                20K
                            </button>
                            <button type="button" wire:click="setQuickAmount(50000)"
                                class="px-3 py-2 text-sm font-semibold rounded-lg border-2 {{ $amount == 50000 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-700 hover:border-primary-300' }}">
                                50K
                            </button>
                            <button type="button" wire:click="setQuickAmount(100000)"
                                class="px-3 py-2 text-sm font-semibold rounded-lg border-2 {{ $amount == 100000 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-700 hover:border-primary-300' }}">
                                100K
                            </button>
                            <button type="button" wire:click="setQuickAmount(200000)"
                                class="px-3 py-2 text-sm font-semibold rounded-lg border-2 {{ $amount == 200000 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-700 hover:border-primary-300' }}">
                                200K
                            </button>
                            <button type="button" wire:click="setQuickAmount(500000)"
                                class="px-3 py-2 text-sm font-semibold rounded-lg border-2 {{ $amount == 500000 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-700 hover:border-primary-300' }}">
                                500K
                            </button>
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="customerName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-50"
                            readonly>
                        @error('customerName') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon *</label>
                        <input type="tel" wire:model="customerPhone" maxlength="13" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="08123456789">
                        @error('customerPhone') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email (opsional)</label>
                        <input type="email" wire:model="customerEmail"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="email@example.com">
                        @error('customerEmail') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                        <textarea wire:model="customerNotes" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        @error('customerNotes') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="space-y-2">
                        <div class="flex gap-3">
                            <a href="{{ route('customer.dashboard') }}"
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center hover:bg-gray-200">
                                Batal
                            </a>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700">
                                Lanjutkan →
                            </button>
                        </div>
                        @if(session('topup_form_data'))
                            <button type="button" wire:click="resetFormData"
                                onclick="return confirm('Yakin ingin reset semua data form?')"
                                class="w-full px-4 py-2 text-xs text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition border border-gray-200">
                                Reset Data
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @endif

        <!-- Step 2: Detail Pembayaran -->
        @if ($currentStep === 2)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Langkah 2: Detail Pembayaran</h2>
                
                <!-- Rincian Pembayaran -->
                <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6 mb-6">
                    <h3 class="text-sm font-bold text-primary-900 mb-4">RINCIAN PEMBAYARAN</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">Nominal Top-Up</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">Biaya Admin</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-primary-200 my-2"></div>
                        <div class="flex justify-between text-lg">
                            <span class="font-bold text-primary-900">Total Bayar</span>
                            <span class="font-bold text-primary-600">Rp {{ number_format($totalPayment, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penting -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold mb-1">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Transfer sesuai nominal total yang tertera</li>
                                <li>Saldo masuk setelah admin approve</li>
                                <li>Maksimal 1x24 jam untuk verifikasi</li>
                                <li>Bukti transfer wajib diupload</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Data Pengirim -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Data Pengirim:</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama:</span>
                            <span class="font-medium text-gray-900">{{ $customerName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telepon:</span>
                            <span class="font-medium text-gray-900">{{ $customerPhone }}</span>
                        </div>
                        @if($customerEmail)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium text-gray-900">{{ $customerEmail }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Buttons -->
                <div class="space-y-2">
                    <div class="flex gap-3">
                        <button type="button" wire:click="previousStep"
                            class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">
                            ← Kembali
                        </button>
                        <button type="button" wire:click="nextStep"
                            class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700">
                            Lanjutkan →
                        </button>
                    </div>
                    @if(session('topup_form_data'))
                        <button type="button" wire:click="resetFormData"
                            onclick="return confirm('Yakin ingin reset semua data form?')"
                            class="w-full px-4 py-2 text-xs text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition border border-gray-200">
                            Reset Data
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Step 3: Metode Pembayaran & Upload Bukti -->
        @if ($currentStep === 3)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Langkah 3: Metode Pembayaran</h2>
                
                <!-- Total yang harus dibayar -->
                <div class="bg-primary-600 text-white rounded-xl p-4 mb-6 text-center">
                    <p class="text-sm mb-1">Total yang harus dibayar:</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
                </div>

                <form wire:submit.prevent="submitRequest">
                    <!-- QRIS -->
                    @if($qrisEnabled)
                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <span>📱</span> E-WALLET
                            </h3>
                            <div wire:click="selectPaymentMethod('qris')"
                                class="border-2 {{ $paymentMethod === 'qris' ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }} rounded-xl p-4 cursor-pointer hover:border-primary-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">💳</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">QRIS - Semua E-Wallet</p>
                                        <p class="text-xs text-gray-600">Scan & Bayar</p>
                                    </div>
                                    @if($paymentMethod === 'qris')
                                        <svg class="w-6 h-6 text-primary-600 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                        </svg>
                                    @endif
                                </div>
                                
                                <!-- QRIS QR Code Display -->
                                @if($paymentMethod === 'qris')
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="bg-white rounded-xl p-4 text-center">
                                            <p class="text-sm font-semibold text-gray-900 mb-3">Scan QR Code untuk Bayar</p>
                                            @if(file_exists(public_path('images/payment/qris.png')))
                                                <img src="{{ asset('images/payment/qris.png') }}" 
                                                    alt="QRIS QR Code" 
                                                    class="w-64 h-64 mx-auto border-2 border-gray-200 rounded-xl shadow-lg">
                                            @else
                                                <div class="w-64 h-64 mx-auto border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center">
                                                    <div class="text-center">
                                                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                        </svg>
                                                        <p class="text-sm text-gray-500">QR Code belum tersedia</p>
                                                        <p class="text-xs text-gray-400 mt-1">Upload file: public/images/payment/qris.png</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="mt-3 text-xs text-gray-500 space-y-1">
                                                <p>✓ Bisa pakai GoPay, OVO, Dana, LinkAja, ShopeePay, dll</p>
                                                <p>✓ Transfer sesuai nominal: <strong class="text-primary-600">Rp {{ number_format($totalPayment, 0, ',', '.') }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Transfer Bank -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span>🏦</span> TRANSFER BANK
                        </h3>
                        <div class="space-y-3">
                            @foreach($availableBanks as $bank)
                                <div wire:click="selectPaymentMethod('bank_{{ strtolower($bank['code']) }}')"
                                    class="border-2 {{ $paymentMethod === 'bank_'.strtolower($bank['code']) ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }} rounded-xl p-4 cursor-pointer hover:border-primary-300 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                                            <span class="text-xs font-bold text-gray-700">{{ strtoupper($bank['code']) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $bank['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $bank['account_number'] }}</p>
                                            <p class="text-xs text-gray-500">a.n. {{ $bank['account_name'] }}</p>
                                        </div>
                                        <button type="button" 
                                            onclick="navigator.clipboard.writeText('{{ $bank['account_number'] }}'); alert('Nomor rekening disalin!')"
                                            class="px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                            📋 Salin
                                        </button>
                                        @if($paymentMethod === 'bank_'.strtolower($bank['code']))
                                            <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('paymentMethod') <span class="text-xs text-red-600 mb-4 block">{{ $message }}</span> @enderror

                    <!-- Upload Bukti Transfer -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span>📤</span> Upload Bukti Transfer *
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                            <input type="file" wire:model="proofOfPayment" accept="image/*" class="hidden" id="proofUpload">
                            <label for="proofUpload" class="cursor-pointer">
                                <div class="text-gray-600 mb-2">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700">Klik untuk pilih file</p>
                                <p class="text-xs text-gray-500 mt-1">atau Drag & Drop di sini</p>
                                <p class="text-xs text-gray-400 mt-1">Max: 2MB (JPG, PNG)</p>
                            </label>
                        </div>
                        @error('proofOfPayment') <span class="text-xs text-red-600 mt-2 block">{{ $message }}</span> @enderror
                        
                        @if ($proofOfPayment)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                                <img src="{{ $proofOfPayment->temporaryUrl() }}" class="max-w-xs rounded-xl shadow-lg">
                            </div>
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="space-y-2">
                        <div class="flex gap-3">
                            <button type="button" wire:click="previousStep"
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">
                                ← Kembali
                            </button>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Kirim Request</span>
                                <span wire:loading>Mengirim...</span>
                            </button>
                        </div>
                        @if(session('topup_form_data'))
                            <button type="button" wire:click="resetFormData"
                                onclick="return confirm('Yakin ingin reset semua data form?')"
                                class="w-full px-4 py-2 text-xs text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition border border-gray-200">
                                Reset Data
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
