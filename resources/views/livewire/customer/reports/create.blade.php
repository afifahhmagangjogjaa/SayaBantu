<div class="min-h-screen bg-gray-50 p-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('customer.dashboard') }}"
                class="inline-flex items-center text-primary-600 hover:text-primary-700 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Buat Laporan Aduan</h1>
            <p class="text-sm text-gray-600 mt-1">Laporkan masalah yang Anda alami di platform</p>
        </div>

        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                {{ session('message') }}
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="submit" class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 space-y-6">
            <!-- Judul -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Judul Laporan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" wire:model="title"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                    placeholder="Contoh: Mitra tidak merespon chat">
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Laporan -->
            <div>
                <label for="report_type" class="block text-sm font-semibold text-gray-700 mb-2">
                    Jenis Laporan <span class="text-red-500">*</span>
                </label>
                <select id="report_type" wire:model="report_type"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('report_type') border-red-500 @enderror">
                    <option value="">Pilih Jenis Laporan</option>
                    @foreach ($reportTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('report_type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if($report_type === 'lainnya')
                    <div class="mt-3">
                        <label for="custom_help_type" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Bantuan
                            (jika tidak ada di daftar)</label>
                        <input type="text" id="custom_help_type" wire:model="custom_help_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('custom_help_type') border-red-500 @enderror"
                            placeholder="Tulis jenis bantuan yang tidak ada di pilihan">
                        @error('custom_help_type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Bantuan yang Dilaporkan (Optional) -->
            <div>
                <label for="help_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-between">
                        <span>Bantuan yang Dilaporkan</span>
                        @if($help_id)
                            <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Terisi Otomatis</span>
                        @else
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        @endif
                    </span>
                </label>
                <select id="help_id" wire:model.live="help_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 {{ $help_id ? 'bg-gray-50' : '' }}">
                    <option value="">Pilih Bantuan (Opsional)</option>
                    @foreach ($helps as $help)
                        <option value="{{ $help->id }}">Bantuan #{{ $help->id }} - {{ Str::limit($help->title, 40) }} {{ $help->mitra ? '(' . $help->mitra->name . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @if(!$help_id)
                    <p class="mt-1 text-xs text-gray-500">Pilih bantuan jika laporan terkait dengan bantuan tertentu</p>
                @endif
            </div>

            <!-- User yang Dilaporkan (Optional) -->
            <div>
                <label for="reported_user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-between">
                        <span>Mitra yang Dilaporkan</span>
                        @if($reported_user_id)
                            <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Terisi Otomatis</span>
                        @else
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        @endif
                    </span>
                </label>
                <select id="reported_user_id" wire:model="reported_user_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 {{ $reported_user_id ? 'bg-gray-50' : '' }}">
                    <option value="">{{ $customerCity ? 'Pilih Mitra di Wilayah ' . $customerCity . ' (Opsional)' : 'Pilih Mitra (Opsional)' }}</option>
                    @foreach ($mitras as $mitra)
                        <option value="{{ $mitra->id }}">{{ $mitra->name }} ({{ $mitra->email }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    @if($customerCity && !$reported_user_id)
                        Menampilkan mitra aktif di wilayah <strong>{{ $customerCity }}</strong>
                    @elseif(!$reported_user_id)
                        Pilih mitra jika laporan terkait dengan mitra tertentu
                    @endif
                </p>
            </div>

            <!-- Pesan -->
            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                    Detail Laporan <span class="text-red-500">*</span>
                </label>
                <textarea id="message" wire:model="message" rows="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('message') border-red-500 @enderror"
                    placeholder="Jelaskan masalah yang Anda alami secara detail..."></textarea>
                @error('message')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter, maksimal 2000 karakter</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('customer.dashboard') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-primary-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
            <div class="mt-8 border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Aduan Sebelumnya
                    </h2>
                    <span class="text-xs bg-gray-100 text-gray-800 font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $resolvedReports->count() }} Laporan
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($resolvedReports as $resRep)
                        @php
                            $isRes = in_array($resRep->status, ['resolved', 'closed']);
                            $isRej = in_array($resRep->status, ['rejected', 'dismissed']);
                        @endphp
                        <div class="bg-white rounded-2xl p-4 border {{ $isRes ? 'border-emerald-200' : ($isRej ? 'border-red-200' : 'border-gray-200') }} shadow-xs">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $resRep->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Dilaporkan: {{ $resRep->created_at->format('d M Y H:i') }}</p>
                                </div>
                                @if($isRes)
                                    <span class="px-2.5 py-1 text-[11px] font-bold text-emerald-800 bg-emerald-100 rounded-full flex-shrink-0">
                                        ✅ Selesai
                                    </span>
                                @elseif($isRej)
                                    <span class="px-2.5 py-1 text-[11px] font-bold text-red-800 bg-red-100 rounded-full flex-shrink-0">
                                        ❌ Ditolak
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 text-xs text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <strong class="text-gray-900 block mb-0.5">Keluhan:</strong>
                                {{ $resRep->message }}
                            </div>

                            @if(!empty($resRep->admin_notes))
                                <div class="mt-2 p-3 {{ $isRej ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200' }} rounded-xl text-xs">
                                    <strong class="{{ $isRej ? 'text-red-950' : 'text-emerald-950' }} block mb-0.5">
                                        {{ $isRej ? 'Alasan / Catatan Penolakan Admin:' : 'Catatan Tindakan Admin:' }}
                                    </strong>
                                    <p class="{{ $isRej ? 'text-red-900' : 'text-emerald-900' }} whitespace-pre-line">{{ $resRep->admin_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>