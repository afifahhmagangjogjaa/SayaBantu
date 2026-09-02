<div class="space-y-4" x-data="{
    playNotif() { window.playNotifChime(); },
    playRing() { window.playRingChime(); },
    vibrate() { window.triggerVibrate(); }
}"
@play-preview.window="
    if ($event.detail.setting === 'sound') playNotif();
    if ($event.detail.setting === 'soundCall') playRing();
    if ($event.detail.setting === 'vibrate') vibrate();
">
    @if(session()->has('message'))
        <div class="p-3.5 bg-green-50 border border-green-200 rounded-xl text-xs font-semibold text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Notifikasi Umum -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Notifikasi Umum</span>
            <span class="text-xs text-gray-400">Pemberitahuan bantuan baru dan info penting</span>
        </div>
        <button type="button" wire:click="updateSetting('generalNotification')"
            onclick="if('Notification' in window && Notification.permission !== 'granted') Notification.requestPermission();"
            style="background-color: {{ $generalNotification ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $generalNotification ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Suara Notifikasi -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-sm text-gray-900 block">Suara Notifikasi</span>
                <button type="button" @click="playNotif()" class="px-2 py-0.5 text-[11px] bg-blue-50 text-[#0098e7] rounded-md font-medium hover:bg-blue-100 transition flex items-center gap-1">
                    🔊 Uji Suara
                </button>
            </div>
            <span class="text-xs text-gray-400">Bunyikan nada saat ada notifikasi masuk</span>
        </div>
        <button type="button" wire:click="updateSetting('sound')"
            @click="playNotif()"
            style="background-color: {{ $sound ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $sound ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Nada Panggilan -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-sm text-gray-900 block">Nada Panggilan</span>
                <button type="button" @click="playRing()" class="px-2 py-0.5 text-[11px] bg-blue-50 text-[#0098e7] rounded-md font-medium hover:bg-blue-100 transition flex items-center gap-1">
                    📞 Uji Dering
                </button>
            </div>
            <span class="text-xs text-gray-400">Bunyi dering panggilan bantuan darurat</span>
        </div>
        <button type="button" wire:click="updateSetting('soundCall')"
            @click="playRing()"
            style="background-color: {{ $soundCall ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $soundCall ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Getar (Vibrate) -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Getar (Vibrate)</span>
            <span class="text-xs text-gray-400">Getarkan perangkat saat ada pesan atau bantuan baru</span>
        </div>
        <button type="button" wire:click="updateSetting('vibrate')"
            @click="vibrate()"
            style="background-color: {{ $vibrate ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $vibrate ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Update Transaksi & Saldo -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Update Transaksi & Saldo</span>
            <span class="text-xs text-gray-400">Notifikasi top up, penarikan saldo, & pencairan</span>
        </div>
        <button type="button" wire:click="updateSetting('transactionUpdate')"
            style="background-color: {{ $transactionUpdate ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $transactionUpdate ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Pengingat Bantuan -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Pengingat Bantuan</span>
            <span class="text-xs text-gray-400">Pengingat jadwal dan status bantuan yang sedang berjalan</span>
        </div>
        <button type="button" wire:click="updateSetting('expenseReminder')"
            style="background-color: {{ $expenseReminder ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $expenseReminder ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Notifikasi Promosi & Info -->
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Notifikasi Promosi & Info</span>
            <span class="text-xs text-gray-400">Info promo, update sistem, dan fitur terbaru</span>
        </div>
        <button type="button" wire:click="updateSetting('budgetNotifications')"
            style="background-color: {{ $budgetNotifications ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $budgetNotifications ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Peringatan Saldo Rendah -->
    <div class="flex items-center justify-between py-3">
        <div class="pr-4">
            <span class="font-semibold text-sm text-gray-900 block">Peringatan Saldo Rendah</span>
            <span class="text-xs text-gray-400">Peringatan saat saldo dompet hampir habis</span>
        </div>
        <button type="button" wire:click="updateSetting('lowBalanceAlerts')"
            style="background-color: {{ $lowBalanceAlerts ? '#0098e7' : '#cbd5e1' }}; width: 44px; height: 24px;"
            class="relative inline-flex flex-shrink-0 items-center rounded-full transition-colors duration-200 cursor-pointer focus:outline-none shadow-inner">
            <span style="transform: translateX({{ $lowBalanceAlerts ? '22px' : '2px' }});"
                class="inline-block h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
        </button>
    </div>

    <!-- Interactive Audio & Feedback Script -->
    <script>
        window.playNotifChime = function () {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;
                // Bright sweet 3-tone chime (C5 -> E5 -> G5 -> C6)
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (i * 0.08));
                    gain.gain.setValueAtTime(0.3, now + (i * 0.08));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.08) + 0.3);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.08));
                    osc.stop(now + (i * 0.08) + 0.3);
                });
            } catch(e) {
                console.warn('Audio play error:', e);
            }
        };

        window.playRingChime = function () {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;
                // Melodic ringtone (D5 -> A5 -> D5 -> A5)
                const notes = [587.33, 880, 587.33, 880];
                notes.forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (i * 0.12));
                    gain.gain.setValueAtTime(0.25, now + (i * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.12) + 0.22);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + (i * 0.12));
                    osc.stop(now + (i * 0.12) + 0.22);
                });
            } catch(e) {
                console.warn('Ring play error:', e);
            }
        };

        window.triggerVibrate = function () {
            if ('vibrate' in navigator) {
                try { navigator.vibrate([150, 80, 150]); } catch(e){}
            }
        };
    </script>
</div>