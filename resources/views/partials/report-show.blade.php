{{-- Partial bersama halaman detail laporan: dipakai customer & mitra. Variabel wajib: $routePrefix, $reportedLabel --}}
    @php
        $status = $report->status ?? 'pending';
        $isResolved = in_array($status, ['resolved', 'closed']);
        $isRejected = in_array($status, ['rejected', 'dismissed']);
        $isCompleted = $isResolved || $isRejected;
    @endphp

    <div class="bg-slate-50 pb-6">
        <div class="max-w-md mx-auto">
            <!-- Header - SayaBantu Brand Blue with Wave Separator -->
            <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between text-white mb-3">
                        <a href="{{ $report->reported_help_id ? route($routePrefix.'.chat', ['help' => $report->reported_help_id]) : route($routePrefix.'.dashboard') }}" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 shrink-0 cursor-pointer" aria-label="Kembali">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="text-center flex-1 px-2">
                            <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                                {{ $isCompleted ? 'Riwayat Laporan Aduan' : 'Status Laporan Aduan' }}
                            </h1>
                            <p class="text-xs text-blue-100 font-medium mt-0.5">
                                @if($isRejected)
                                    Laporan aduan ditutup oleh Admin
                                @elseif($isResolved)
                                    Aduan telah diselesaikan oleh Admin
                                @else
                                    Detail penanganan aduan Anda
                                @endif
                            </p>
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
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-bold shadow-2xs flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Kartu Laporan Utama -->
                <div class="bg-white rounded-2xl shadow-sm border {{ $isResolved ? 'border-emerald-200' : ($isRejected ? 'border-red-200' : 'border-slate-200/80') }} p-5 space-y-4">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 leading-snug">{{ $report->title }}</h2>
                            <p class="text-[11px] text-slate-400 mt-1">Dikirim pada: {{ $report->created_at->format('d M Y H:i') }} WIB</p>
                        </div>
                        @if($isResolved)
                            <span class="px-2.5 py-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 rounded-full flex-shrink-0">
                                ✅ Selesai
                            </span>
                        @elseif($isRejected)
                            <span class="px-2.5 py-1 text-[10px] font-bold text-red-800 bg-red-100 rounded-full flex-shrink-0">
                                ❌ Ditolak
                            </span>
                        @endif
                    </div>

                    <div class="pt-2">
                        <p class="text-xs font-bold text-slate-700 mb-1.5">Status Penanganan:</p>
                        <div>
                            @if($status === 'pending')
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    ⏳ Menunggu Peninjauan Admin
                                </span>
                            @elseif($status === 'in_progress' || $status === 'processing')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    🔍 Sedang Ditangani Admin
                                </span>
                            @elseif($status === 'resolved' || $status === 'closed')
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    ✅ Aduan Telah Diselesaikan
                                </span>
                            @elseif($status === 'rejected' || $status === 'dismissed')
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    ❌ Laporan Ditolak / Ditutup oleh Admin
                                </span>
                            @else
                                <span class="bg-slate-100 text-slate-800 text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    {{ ucfirst($status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-700 mb-1">Jenis Laporan</p>
                        <p class="text-xs text-slate-900 font-semibold">{{ $report->report_type }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-700 mb-1">Jenis Bantuan</p>
                        <p class="text-xs text-slate-900 font-semibold">
                            {{ $report->reported_help_text ?? ($report->reportedHelp ? $report->reportedHelp->title : '-') }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-700 mb-1">{{ $reportedLabel }}</p>
                        <p class="text-xs text-slate-900 font-semibold">
                            {{ $report->reportedUser?->name ?? ($report->reported_user_text ?? '-') }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-700 mb-1">Detail Laporan</p>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-800 whitespace-pre-line leading-relaxed">
                            {{ $report->message }}
                        </div>
                    </div>

                    @if($isResolved)
                        <div class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <p class="text-xs font-extrabold text-emerald-900 mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Hasil Penanganan:
                            </p>
                            <p class="text-xs text-emerald-900 leading-relaxed font-medium">
                                Laporan aduan Anda telah berhasil ditindaklanjuti dan diselesaikan oleh tim Admin sesuai kebijakan layanan SayaBantu. Terima kasih telah membantu menjaga keamanan dan kenyamanan bersama.
                            </p>
                        </div>
                    @elseif($isRejected)
                        <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <p class="text-xs font-extrabold text-slate-800 mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/></svg>
                                Hasil Penanganan:
                            </p>
                            <p class="text-xs text-slate-700 leading-relaxed font-medium">
                                Laporan aduan Anda telah ditinjau dan dinyatakan ditutup oleh tim Admin.
                            </p>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2.5">
                        <a href="{{ route($routePrefix.'.reports.create', ['user_id' => $report->reported_user_id, 'help_id' => $report->reported_help_id, 'new' => 1]) }}" wire:navigate 
                            class="flex-1 py-3 bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl text-xs font-bold transition shadow-md active:scale-95 flex items-center justify-center gap-1.5 text-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajukan Laporan Baru
                        </a>
                        <a href="{{ $report->reported_help_id ? route($routePrefix.'.chat', ['help' => $report->reported_help_id]) : route($routePrefix.'.dashboard') }}" 
                            class="px-4 py-3 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition active:scale-95 text-center">
                            Kembali ke Chat
                        </a>
                    </div>
                </div>

                @php
                    $otherReports = \App\Models\PartnerReport::where('reported_help_id', $report->reported_help_id)
                        ->where('id', '!=', $report->id)
                        ->latest()
                        ->get();
                @endphp

                @if($otherReports->count() > 0)
                    <div class="mt-6 pt-4 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Riwayat Aduan pada Percakapan Ini
                            </h3>
                            <span class="text-[11px] text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded-full">{{ $otherReports->count() }} Laporan</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($otherReports as $oRep)
                                <div class="bg-white rounded-2xl p-4 border {{ in_array($oRep->status, ['resolved', 'closed']) ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200' }} shadow-2xs">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <h4 class="font-bold text-xs text-slate-900">{{ $oRep->title }}</h4>
                                        @php $oSt = $oRep->status ?? 'pending'; @endphp
                                        @if(in_array($oSt, ['resolved', 'closed']))
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                ✅ Selesai
                                            </span>
                                        @elseif($oSt === 'in_progress' || $oSt === 'processing')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                🔍 Sedang Ditangani
                                            </span>
                                        @elseif($oSt === 'rejected' || $oSt === 'dismissed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                                ⏳ Menunggu
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 mb-2">Dikirim: {{ $oRep->created_at->format('d M Y, H:i') }} WIB oleh {{ $oRep->reporter?->name ?? ($oRep->reporter_id == auth()->id() ? 'Anda' : 'Pengguna') }}</p>
                                    <div class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <strong class="text-slate-900 block mb-0.5 text-[11px]">Keluhan:</strong>
                                        {{ $oRep->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function() {
            let currentStatus = @json($report->status);
            let checkUrl = "{{ route($routePrefix.'.reports.status-check', $report->id) }}";

            function checkReportStatus() {
                fetch(checkUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.status) {
                        if (data.status !== currentStatus) {
                            window.location.reload();
                        }
                    }
                })
                .catch(err => {});
            }

            // Polling realtime setiap 2 detik
            setInterval(checkReportStatus, 2000);
        })();
    </script>
