@extends('layouts.mitra')

@section('content')

    @php
        $status = $report->status ?? 'pending';
        $isResolved = in_array($status, ['resolved', 'closed']);
        $isRejected = in_array($status, ['rejected', 'dismissed']);
        $isCompleted = $isResolved || $isRejected;
    @endphp

    <div class="min-h-screen bg-gray-50 p-4 pb-32">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ $report->reported_help_id ? route('mitra.chat', ['help' => $report->reported_help_id]) : route('mitra.dashboard') }}"
                    class="inline-flex items-center text-primary-600 hover:text-primary-700 mb-4 font-medium text-sm">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Percakapan
                </a>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isCompleted ? 'Riwayat Laporan Aduan' : 'Status Laporan Aduan' }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    @if($isRejected)
                        Laporan aduan ini telah ditinjau dan ditutup/ditolak oleh Admin
                    @elseif($isResolved)
                        Riwayat penanganan dan keputusan penyelesaian aduan untuk percakapan ini
                    @else
                        Detail penanganan dan keputusan admin untuk percakapan ini
                    @endif
                </p>
            </div>

            @if (session('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-2xl text-sm font-medium">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Kartu Laporan Utama -->
            <div class="bg-white rounded-2xl shadow-md border {{ $isResolved ? 'border-emerald-200' : ($isRejected ? 'border-red-200' : 'border-gray-200') }} p-6 space-y-4 mb-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ $report->title }}</h2>
                        <p class="text-xs text-gray-500 mt-1">Dikirim pada: {{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($isResolved)
                        <span class="px-3 py-1 text-xs font-bold text-emerald-800 bg-emerald-100 rounded-full flex-shrink-0">
                            ✅ Selesai
                        </span>
                    @elseif($isRejected)
                        <span class="px-3 py-1 text-xs font-bold text-red-800 bg-red-100 rounded-full flex-shrink-0">
                            ❌ Ditolak
                        </span>
                    @endif
                </div>

                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-700 mb-1.5">Status:</p>
                    <div>
                        @if($status === 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1.5 rounded-full inline-block">
                                ⏳ Menunggu Peninjauan Admin
                            </span>
                        @elseif($status === 'in_progress' || $status === 'processing')
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1.5 rounded-full inline-block">
                                🔍 Sedang Ditangani Admin
                            </span>
                        @elseif($status === 'resolved' || $status === 'closed')
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-3 py-1.5 rounded-full inline-block">
                                ✅ Aduan Telah Diselesaikan
                            </span>
                        @elseif($status === 'rejected' || $status === 'dismissed')
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1.5 rounded-full inline-block">
                                ❌ Laporan Ditolak / Ditutup oleh Admin
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-full inline-block">
                                {{ ucfirst($status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-3">
                    <p class="text-sm font-medium text-gray-700">Jenis Laporan</p>
                    <p class="mt-1 text-gray-800 text-sm font-medium">{{ $report->report_type }}</p>
                </div>

                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-700">Jenis Bantuan (jika ada)</p>
                    <p class="mt-1 text-gray-800 text-sm font-medium">
                        {{ $report->reported_help_text ?? ($report->reportedHelp ? $report->reportedHelp->title : '-') }}
                    </p>
                </div>

                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-700">Customer yang Dilaporkan</p>
                    <p class="mt-1 text-gray-800 text-sm font-medium">
                        {{ $report->reportedUser?->name ?? ($report->reported_user_text ?? '-') }}
                    </p>
                </div>

                <div class="pt-2">
                    <p class="text-sm font-medium text-gray-700">Detail Laporan</p>
                    <p class="mt-1 text-gray-800 text-sm whitespace-pre-line leading-relaxed">{{ $report->message }}</p>
                </div>

                @if(!empty($report->admin_notes))
                    <div class="mt-4 p-4 {{ $isRejected ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200' }} rounded-xl">
                        <p class="text-xs font-bold {{ $isRejected ? 'text-red-800' : 'text-emerald-800' }} uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            @if($isRejected)
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Alasan / Catatan dari Admin:
                            @else
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Catatan Tindakan dari Admin:
                            @endif
                        </p>
                        <p class="text-sm {{ $isRejected ? 'text-red-900' : 'text-emerald-900' }} whitespace-pre-line">{{ $report->admin_notes }}</p>
                    </div>
                @endif

                <div class="pt-6 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a href="{{ route('mitra.reports.create', ['user_id' => $report->reported_user_id, 'help_id' => $report->reported_help_id, 'new' => 1]) }}" 
                        class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajukan Laporan Baru
                    </a>
                    <a href="{{ $report->reported_help_id ? route('mitra.chat', ['help' => $report->reported_help_id]) : route('mitra.dashboard') }}" 
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                        Kembali ke Percakapan
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
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Riwayat Aduan pada Percakapan Ini
                        </h3>
                        <span class="text-xs text-gray-500 font-medium">{{ $otherReports->count() }} Laporan Lainnya</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($otherReports as $oRep)
                            <div class="bg-white rounded-2xl p-4 border {{ in_array($oRep->status, ['resolved', 'closed']) ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200' }} shadow-xs">
                                <div class="flex items-center justify-between gap-2 mb-1.5">
                                    <h4 class="font-bold text-sm text-gray-900">{{ $oRep->title }}</h4>
                                    @php $oSt = $oRep->status ?? 'pending'; @endphp
                                    @if(in_array($oSt, ['resolved', 'closed']))
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✅ Selesai
                                        </span>
                                    @elseif($oSt === 'in_progress' || $oSt === 'processing')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            🔍 Sedang Ditangani
                                        </span>
                                    @elseif($oSt === 'rejected' || $oSt === 'dismissed')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-gray-500 mb-2">Dikirim: {{ $oRep->created_at->format('d M Y, H:i') }} oleh {{ $oRep->reporter?->name ?? ($oRep->reporter_id == auth()->id() ? 'Anda' : 'Pengguna') }}</p>
                                <div class="text-xs text-gray-700 bg-white p-2.5 rounded-xl border border-gray-100">
                                    <strong class="text-gray-900 block mb-0.5">Keluhan:</strong>
                                    {{ $oRep->message }}
                                </div>
                                @if(!empty($oRep->admin_notes))
                                    <div class="mt-2 p-2.5 bg-emerald-50 rounded-xl text-xs text-emerald-900 border border-emerald-200">
                                        <strong class="text-emerald-950 block mb-0.5">Catatan Tindakan Admin:</strong>
                                        {{ $oRep->admin_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        (function() {
            let currentStatus = @json($report->status);
            let currentNotes = @json($report->admin_notes);
            let checkUrl = "{{ route('mitra.reports.status-check', $report->id) }}";

            function checkReportStatus() {
                fetch(checkUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.status) {
                        if (data.status !== currentStatus || data.admin_notes !== currentNotes) {
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

@endsection