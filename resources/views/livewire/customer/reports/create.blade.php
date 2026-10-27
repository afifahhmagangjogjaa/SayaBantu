<div class="min-h-screen bg-slate-50 pb-20">
    <div class="max-w-md mx-auto">
        <!-- Header - Brand Blue with Wave Separator -->
        <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <a href="{{ route('customer.dashboard') }}" wire:navigate aria-label="Kembali" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="text-center flex-1 px-2">
                        <h1 class="text-lg font-black text-white tracking-tight leading-tight">Buat Laporan Aduan</h1>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Laporkan masalah atau kendala yang Anda alami</p>
                    </div>

                    <div class="w-9 h-9 shrink-0"></div>
                </div>
            </div>

            <!-- Wave Separator (SVG) bergelombang -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <div class="px-5 space-y-4">
            @if (session('message'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl shadow-2xs flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-bold text-xs">{{ session('message') }}</span>
                </div>
            @endif

            <!-- Form Card -->
            <form wire:submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 space-y-4">
                <!-- Judul -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Judul Laporan
                    </label>
                    <input type="text" id="title" wire:model="title"
                        class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all placeholder:font-normal placeholder:text-slate-400 @error('title') border-red-500 bg-red-50/50 @enderror"
                        placeholder="Contoh: Mitra tidak merespon chat">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Laporan -->
                <div>
                    <label for="report_type" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Jenis Laporan
                    </label>
                    <select id="report_type" wire:model="report_type"
                        class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all bg-white @error('report_type') border-red-500 bg-red-50/50 @enderror">
                        <option value="">Pilih Jenis Laporan</option>
                        @foreach ($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('report_type')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    @if($report_type === 'lainnya')
                        <div class="mt-3">
                            <label for="custom_help_type" class="block text-xs font-bold text-slate-800 mb-1.5">Jenis Bantuan (jika tidak ada di daftar)</label>
                            <input type="text" id="custom_help_type" wire:model="custom_help_type"
                                class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all placeholder:font-normal placeholder:text-slate-400 @error('custom_help_type') border-red-500 @enderror"
                                placeholder="Tulis jenis bantuan yang tidak ada di pilihan">
                            @error('custom_help_type')
                                <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Bantuan yang Dilaporkan (Optional) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="help_id" class="block text-xs font-bold text-slate-800">
                            Bantuan yang Dilaporkan
                        </label>
                        @if($help_id)
                            <span class="text-[10px] font-bold text-[#0066FF] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Terisi Otomatis</span>
                        @else
                            <span class="text-[11px] text-slate-400 font-medium">(Opsional)</span>
                        @endif
                    </div>
                    <select id="help_id" wire:model.live="help_id"
                        class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all bg-white {{ $help_id ? 'bg-slate-50' : '' }}">
                        <option value="">Pilih Bantuan (Opsional)</option>
                        @foreach ($helps as $help)
                            <option value="{{ $help->id }}">Bantuan #{{ $help->id }} - {{ Str::limit($help->title, 40) }} {{ $help->mitra ? '(' . $help->mitra->name . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @if(!$help_id)
                        <p class="mt-1 text-[11px] text-slate-400">Pilih bantuan jika laporan terkait dengan bantuan tertentu</p>
                    @endif
                </div>

                <!-- User yang Dilaporkan (Optional) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="reported_user_id" class="block text-xs font-bold text-slate-800">
                            Mitra yang Dilaporkan
                        </label>
                        @if($reported_user_id)
                            <span class="text-[10px] font-bold text-[#0066FF] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Terisi Otomatis</span>
                        @else
                            <span class="text-[11px] text-slate-400 font-medium">(Opsional)</span>
                        @endif
                    </div>
                    <select id="reported_user_id" wire:model="reported_user_id"
                        class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all bg-white {{ $reported_user_id ? 'bg-slate-50' : '' }}">
                        <option value="">{{ $customerCity ? 'Pilih Mitra di Wilayah ' . $customerCity . ' (Opsional)' : 'Pilih Mitra (Opsional)' }}</option>
                        @foreach ($mitras as $mitra)
                            <option value="{{ $mitra->id }}">{{ $mitra->name }} ({{ $mitra->email }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400">
                        @if($customerCity && !$reported_user_id)
                            Menampilkan mitra aktif di wilayah <strong>{{ $customerCity }}</strong>
                        @elseif(!$reported_user_id)
                            Pilih mitra jika laporan terkait dengan mitra tertentu
                        @endif
                    </p>
                </div>

                <!-- Pesan -->
                <div>
                    <label for="message" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Detail Laporan
                    </label>
                    <textarea id="message" wire:model="message" rows="5"
                        class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all placeholder:text-slate-400 leading-relaxed @error('message') border-red-500 bg-red-50/50 @enderror"
                        placeholder="Jelaskan masalah yang Anda alami secara detail..."></textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-[11px] text-slate-400">Minimal 10 karakter, maksimal 2000 karakter</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2.5 pt-2">
                    <a href="{{ route('customer.dashboard') }}" wire:navigate
                        class="px-4 py-3 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-50 transition active:scale-95 flex items-center justify-center">
                        Batal
                    </a>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold text-xs rounded-xl shadow-md transition active:scale-95 text-center">
                        Kirim Laporan
                    </button>
                </div>
            </form>

            @php
                $targetHelpId = $help_id ?? $reported_help_id;
                $resolvedReports = collect();
                if (auth()->check()) {
                    $resolvedReports = \App\Models\PartnerReport::where(function ($q) {
                            $q->where('reporter_id', auth()->id())
                              ->orWhere('reported_user_id', auth()->id());
                        })
                        ->when($targetHelpId, function ($q, $hId) {
                            $q->where('reported_help_id', $hId);
                        })
                        ->when(!$targetHelpId && $reported_user_id, function ($q) use ($reported_user_id) {
                            $q->where(function ($sq) use ($reported_user_id) {
                                $sq->where('reported_user_id', $reported_user_id)
                                   ->orWhere('reporter_id', $reported_user_id);
                            });
                        })
                        ->whereIn('status', ['resolved', 'closed', 'dismissed', 'rejected'])
                        ->latest()
                        ->get();
                }
            @endphp

            @if($resolvedReports->count() > 0)
                <div class="mt-6 pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Riwayat Aduan Sebelumnya
                        </h2>
                        <span class="text-[11px] bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-full">
                            {{ $resolvedReports->count() }} Laporan
                        </span>
                    </div>
                    <div class="space-y-3">
                        @foreach($resolvedReports as $resRep)
                            @php
                                $isRes = in_array($resRep->status, ['resolved', 'closed']);
                                $isRej = in_array($resRep->status, ['rejected', 'dismissed']);
                            @endphp
                            <div class="bg-white rounded-2xl p-4 border {{ $isRes ? 'border-emerald-200' : ($isRej ? 'border-red-200' : 'border-slate-200') }} shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="text-xs font-bold text-slate-900">{{ $resRep->title }}</h3>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Dilaporkan: {{ $resRep->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    @if($isRes)
                                        <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-800 bg-emerald-100 rounded-full flex-shrink-0">
                                            ✅ Selesai
                                        </span>
                                    @elseif($isRej)
                                        <span class="px-2 py-0.5 text-[10px] font-bold text-red-800 bg-red-100 rounded-full flex-shrink-0">
                                            ❌ Ditolak
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-2 text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <strong class="text-slate-900 block mb-0.5 text-[11px]">Keluhan:</strong>
                                    {{ $resRep->message }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
