<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2 text-center" x-data="verifyEmailPage()">
        <div>
            <!-- 1. Dynamic Icon (Email Envelope -> Green Checkmark on Verified) -->
            <template x-if="!isVerified">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-blue-50 text-[#0098e7] border-2 border-blue-100 flex items-center justify-center shadow-xs transition-all duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </template>

            <template x-if="isVerified">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-emerald-50 text-emerald-600 border-2 border-emerald-200 flex items-center justify-center shadow-md animate-bounce">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </template>

            <!-- Title & Subtitle -->
            <h2 class="text-2xl font-extrabold text-gray-900 mb-1.5 tracking-tight"
                x-text="isVerified ? 'Email Berhasil Diverifikasi! 🎉' : 'Periksa Inbox Email Anda'">
                Periksa Inbox Email Anda
            </h2>
            
            <p class="text-xs text-gray-500 font-medium px-2 leading-relaxed mb-4"
               x-text="isVerified ? 'Verifikasi sukses. Anda akan dialihkan secara otomatis...' : 'Kami telah mengirimkan tautan aktivasi akun ke:'">
                Kami telah mengirimkan tautan aktivasi akun ke:
            </p>

            <!-- 2. Email Address Display & Pulsing Status -->
            <div class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-4 mb-3 text-center shadow-2xs transition">
                <p class="text-sm sm:text-base font-bold text-gray-900 break-all select-all">
                    {{ auth()->user()->email ?? 'email Anda' }}
                </p>
                
                <!-- Status Normal: Menunggu -->
                <div x-show="!isVerified" class="mt-2.5 inline-flex items-center gap-2 px-3.5 py-1 bg-blue-50/90 text-[#0077cc] text-xs font-semibold rounded-full border border-blue-200/80">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0098e7]"></span>
                    </span>
                    <span>Menunggu verifikasi...</span>
                </div>

                <!-- Status Berhasil: Terverifikasi & Mengalihkan -->
                <div x-show="isVerified" x-cloak class="mt-2.5 inline-flex items-center gap-2 px-3.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200 animate-pulse">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Terverifikasi! Mengalihkan ke Buat Sandi...</span>
                </div>
            </div>

            <!-- 3. Real-Time Automation Note Card -->
            <div x-show="!isVerified" class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3 mb-5 text-left flex items-start gap-2.5 text-xs text-amber-900 shadow-2xs">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <div class="leading-relaxed">
                    <span class="font-bold text-amber-950">Terdeteksi Otomatis:</span> Begitu tautan di email diklik, layar ini akan langsung melanjutkan ke pembuatan kata sandi.
                </div>
            </div>

            @if (session('message'))
                <div class="mb-4 p-3.5 bg-green-50 border border-green-200 text-green-700 text-xs rounded-xl font-semibold shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- 4. Action Group -->
            <div class="space-y-3 pt-1" x-show="!isVerified">
                <!-- Primary Action: Open Email App -->
                @php
                    $userEmail = auth()->user()->email ?? '';
                    $emailDomain = explode('@', $userEmail)[1] ?? '';
                    $mailUrl = match(true) {
                        str_contains($emailDomain, 'gmail') => 'https://mail.google.com/mail/u/0/#inbox',
                        str_contains($emailDomain, 'yahoo') => 'https://mail.yahoo.com',
                        str_contains($emailDomain, 'outlook') || str_contains($emailDomain, 'hotmail') => 'https://outlook.live.com',
                        default => 'mailto:' . $userEmail,
                    };
                @endphp
                <a href="{{ $mailUrl }}" target="_blank"
                    class="w-full inline-flex items-center justify-center gap-2 text-white font-bold py-3.5 px-4 rounded-full shadow-md hover:shadow-lg active:scale-98 transition text-sm tracking-wide cursor-pointer"
                    style="background-color: #0098e7;">
                    <span>Buka Aplikasi Email</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>

                <!-- Secondary Assistance Links -->
                <div class="pt-1.5 space-y-1.5 text-xs text-gray-500">
                    <div>
                        <span>Tidak menerima email?</span>
                        <form method="POST" action="{{ route('verification.send') }}" class="inline-block ml-1">
                            @csrf
                            <template x-if="cooldown > 0">
                                <span class="font-bold text-gray-400">
                                    Kirim Ulang (<span x-text="cooldown"></span>s)
                                </span>
                            </template>
                            <template x-if="cooldown <= 0">
                                <button type="submit" class="font-bold text-[#0098e7] hover:underline cursor-pointer">
                                    Kirim Ulang
                                </button>
                            </template>
                        </form>
                    </div>

                    <div>
                        <span>Salah alamat email?</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline-block ml-1">
                            @csrf
                            <button type="submit" class="font-bold text-slate-700 hover:text-[#0098e7] underline transition cursor-pointer">
                                Ubah Email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Polling Realtime & Smooth 1s Auto-Redirect -->
    <script>
        function verifyEmailPage() {
            return {
                isVerified: false,
                cooldown: 0,
                init() {
                    // Cek jika baru saja kirim ulang
                    if (@json(session('message') ? true : false)) {
                        this.startCooldown(60);
                    }

                    // Polling status verifikasi setiap 2 detik
                    const checkInterval = setInterval(async () => {
                        if (this.isVerified) return;

                        try {
                            const res = await fetch("{{ route('verification.check-status') }}", {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const data = await res.json();
                                if (data.verified && data.redirect) {
                                    clearInterval(checkInterval);
                                    
                                    // 1. Tampilkan animasi sukses
                                    this.isVerified = true;

                                    // 2. Beri jeda 1.2 detik agar user melihat konfirmasi sukses, lalu otomatis pindah
                                    setTimeout(() => {
                                        window.location.href = data.redirect;
                                    }, 1200);
                                }
                            }
                        } catch (err) {
                            console.error('Checking verification...', err);
                        }
                    }, 2000);
                },
                startCooldown(seconds) {
                    this.cooldown = seconds;
                    const timer = setInterval(() => {
                        this.cooldown--;
                        if (this.cooldown <= 0) {
                            clearInterval(timer);
                        }
                    }, 1000);
                }
            }
        }
    </script>
</x-guest-layout>