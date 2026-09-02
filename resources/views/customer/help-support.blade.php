<x-app-layout>
    <x-slot name="title">Bantuan & Dukungan</x-slot>

    <div class="min-h-screen bg-gray-50 pb-24">
        <!-- BRImo Header -->
        <div class="relative bg-gradient-to-br from-[#0098e7] via-[#0077cc] to-[#0060b0] pb-24 overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative max-w-md mx-auto px-6 pt-4 pb-6">
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('profile') }}" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="w-10"></div>
                </div>
                
                <h1 class="text-2xl font-bold text-white mb-2">Bantuan & Dukungan</h1>
                <p class="text-sm text-white/90">Kami siap membantu Anda</p>
            </div>

            <!-- Curved Separator -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#F9FAFB"/>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-md mx-auto px-6 -mt-16 relative z-10 space-y-4">
            <!-- FAQ Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-purple-600">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Frequently Asked Questions</h2>
                </div>
                <div class="space-y-2">
                    <details class="group">
                        <summary class="cursor-pointer list-none flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="font-semibold text-gray-900 text-sm">Bagaimana cara membuat permintaan bantuan?</span>
                            <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-2 px-3 py-2 text-xs text-gray-600 leading-relaxed">
                            Klik tombol "Buat Bantuan" di halaman dashboard, isi detail bantuan yang Anda butuhkan seperti kategori, lokasi, dan deskripsi, lalu kirim permintaan Anda.
                        </div>
                    </details>

                    <details class="group">
                        <summary class="cursor-pointer list-none flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="font-semibold text-gray-900 text-sm">Bagaimana cara melakukan top up saldo?</span>
                            <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-2 px-3 py-2 text-xs text-gray-600 leading-relaxed">
                            Masuk ke menu Top Up, pilih nominal yang diinginkan, dan selesaikan pembayaran melalui metode yang tersedia. Saldo akan otomatis bertambah setelah pembayaran berhasil.
                        </div>
                    </details>

                    <details class="group">
                        <summary class="cursor-pointer list-none flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="font-semibold text-gray-900 text-sm">Bagaimana cara memberikan rating kepada mitra?</span>
                            <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-2 px-3 py-2 text-xs text-gray-600 leading-relaxed">
                            Setelah bantuan selesai, Anda akan diminta untuk memberikan rating dan ulasan untuk mitra yang telah membantu Anda. Rating ini membantu mitra lain di platform kami.
                        </div>
                    </details>

                    <details class="group">
                        <summary class="cursor-pointer list-none flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="font-semibold text-gray-900 text-sm">Bagaimana jika bantuan tidak sesuai?</span>
                            <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <div class="mt-2 px-3 py-2 text-xs text-gray-600 leading-relaxed">
                            Anda dapat melaporkan masalah atau memberikan rating yang sesuai. Tim customer support kami akan membantu menyelesaikan masalah Anda dengan segera.
                        </div>
                    </details>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Hubungi Kami</h2>
                <div class="space-y-3">
                    <a href="mailto:support@sayabantu.com" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-sm">Email</p>
                            <p class="text-xs text-gray-600 truncate">support@sayabantu.com</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-br from-green-500 to-green-600 flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-sm">WhatsApp</p>
                            <p class="text-xs text-gray-600">+62 812-3456-7890</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="tel:+6281234567890" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #0098e7 0%, #0060b0 100%);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 text-sm">Telepon</p>
                            <p class="text-xs text-gray-600">+62 812-3456-7890</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- App Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Aplikasi</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Versi Aplikasi</span>
                        <span class="text-sm font-bold text-gray-900">1.0.0</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Platform</span>
                        <span class="text-sm font-bold text-gray-900">Web & Mobile</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Last Update</span>
                        <span class="text-sm font-bold text-gray-900">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>