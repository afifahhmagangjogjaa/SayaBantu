@php
    $title = 'Pengaturan Bantuan';
    $breadcrumb = 'Super Admin / Pengaturan / Bantuan';
@endphp

<div>
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gray-50/80">
                <h2 class="text-lg sm:text-xl font-medium text-gray-900">Pengaturan Bantuan</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pilih sub-menu pengaturan di bawah ini</p>
            </div>

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                    <!-- 1. Fee & Pendapatan -->
                    <a href="{{ route('superadmin.pengaturan.bantuan.fee-pendapatan') }}"
                       class="group block bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-emerald-700">Fee & Pendapatan Platform</h3>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Grafik pendapatan, breakdown sumber fee, dan pengaturan biaya layanan customer & mitra.
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- 2. Biaya Top-Up & Bank -->
                    <a href="{{ route('superadmin.pengaturan.bantuan.biaya-topup') }}"
                       class="group block bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md hover:border-blue-300 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Biaya Admin Top-Up & Bank</h3>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Atur biaya admin top-up (3 tier) dan daftar rekening bank untuk transfer customer.
                                </p>
                            </div>
                        </div>
                    </a>

                    <!-- 3. Tarif & Radius -->
                    <a href="{{ route('superadmin.pengaturan.bantuan.tarif-radius') }}"
                       class="group block bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md hover:border-indigo-300 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-indigo-700">Tarif & Radius Bantuan</h3>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Nominal minimal bantuan standar & urgent, serta radius maksimal jangkauan mitra.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>