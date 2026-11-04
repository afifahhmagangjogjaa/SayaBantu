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
                    <a href="{{ route('superadmin.pengaturan.bantuan.fee-pendapatan') }}" wire:navigate
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
                    <a href="{{ route('superadmin.pengaturan.bantuan.biaya-topup') }}" wire:navigate
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
                    <a href="{{ route('superadmin.pengaturan.bantuan.tarif-radius') }}" wire:navigate
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
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-indigo-700">Tarif & Radius Jangkauan Mitra</h3>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    Nominal minimal bantuan reguler & prioritas, serta radius maksimal jarak mitra bisa ambil order bantuan.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Fitur Komunikasi: Visibilitas Tombol WhatsApp -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-6">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gradient-to-r from-emerald-50/70 via-white to-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Visibilitas Tombol WhatsApp</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Atur apakah tombol kontak WhatsApp ditampilkan atau disembunyikan pada detail pesanan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $show_whatsapp_button ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }} text-xs font-bold rounded-xl border shadow-2xs">
                        <span class="w-2 h-2 rounded-full {{ $show_whatsapp_button ? 'bg-emerald-600 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ $show_whatsapp_button ? 'Status: Ditampilkan' : 'Status: Disembunyikan (Hide)' }}
                    </span>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                @if (session()->has('message'))
                    <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border {{ $show_whatsapp_button ? 'bg-emerald-50/30 border-emerald-200' : 'bg-gray-50/50 border-gray-200' }} transition">
                    <div class="max-w-xl">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-gray-900">Tombol WhatsApp di Halaman Detail Bantuan</h4>
                            @if(!$show_whatsapp_button)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Disembunyikan</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Aktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Jika <strong>disembunyikan</strong>, tombol WhatsApp pada rincian bantuan (sisi mitra & customer) tidak akan muncul, sehingga seluruh komunikasi terpusat aman melalui <strong>Chat Aplikasi</strong> bawaan. Jika <strong>ditampilkan</strong>, tombol WhatsApp akan muncul kembali seperti biasa.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <button type="button" wire:click="toggleWhatsapp" wire:loading.attr="disabled"
                            class="relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#0066FF] focus:ring-offset-2 {{ $show_whatsapp_button ? 'bg-emerald-600' : 'bg-gray-300' }}"
                            role="switch" aria-checked="{{ $show_whatsapp_button ? 'true' : 'false' }}">
                            <span class="sr-only">Toggle WhatsApp Button</span>
                            <span aria-hidden="true"
                                class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out {{ $show_whatsapp_button ? 'translate-x-7' : 'translate-x-0' }}">
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
