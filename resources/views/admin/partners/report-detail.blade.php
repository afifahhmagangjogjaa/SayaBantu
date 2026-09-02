@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <!-- Top Back Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:px-6 sm:py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.partners.report') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Laporan</span>
                </a>
            </div>
            <div class="text-xs text-gray-400">
                ID Aduan: <span class="font-mono font-semibold text-gray-700">#{{ $report->id }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Utama -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Laporan</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Judul</dt>
                            <dd class="text-sm text-gray-900 font-medium">{{ $report->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Pesan</dt>
                            <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $report->message }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Jenis Laporan</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        {{ $report->report_type_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori</dt>
                                <dd>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $report->isFromCustomer() ? 'bg-primary-100 text-primary-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $report->category_label }}
                                    </span>
                                </dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                            <dd>
                                @if ($report->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        Pending
                                    </span>
                                @elseif ($report->status === 'in_progress')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        In Progress
                                    </span>
                                @elseif ($report->status === 'resolved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Resolved
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        Dismissed
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Dibuat</dt>
                            <dd class="text-sm text-gray-700">{{ $report->created_at->format('d F Y, H:i') }} WIB</dd>
                        </div>
                        @if ($report->resolved_at)
                            <div>
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Diselesaikan</dt>
                                <dd class="text-sm text-gray-700">{{ $report->resolved_at->format('d F Y, H:i') }} WIB</dd>
                                @if ($report->resolvedBy)
                                    <dd class="text-xs text-gray-500 mt-1">Oleh: {{ $report->resolvedBy->name }}</dd>
                                @endif
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Informasi Reporter -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Reporter</h2>
                    @if ($report->reporter)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('admin.users.show', $report->reporter) }}"
                                    class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                    {{ $report->reporter->name }}
                                </a>
                                <div class="text-sm text-gray-500 mt-1">{{ $report->reporter->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Role: <span class="font-semibold">{{ ucfirst($report->reporter->role) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $report->reporter) }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                Lihat Profil
                            </a>
                        </div>
                    @elseif ($report->user)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('admin.users.show', $report->user) }}"
                                    class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                    {{ $report->user->name }}
                                </a>
                                <div class="text-sm text-gray-500 mt-1">{{ $report->user->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Role: <span class="font-semibold">{{ ucfirst($report->user->role) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $report->user) }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                Lihat Profil
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Informasi reporter tidak tersedia.</p>
                    @endif
                </div>

                <!-- Informasi User/Help yang Dilaporkan -->
                @if ($report->reportedUser || $report->reportedHelp || $report->reported_user_text || $report->reported_help_text)
                    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Yang Dilaporkan</h2>
                        @if ($report->reportedUser)
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                        class="text-primary-600 hover:text-primary-700 hover:underline font-medium">
                                        {{ $report->reportedUser->name }}
                                    </a>
                                    <div class="text-sm text-gray-500 mt-1">{{ $report->reportedUser->email }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Role: <span class="font-semibold">{{ ucfirst($report->reportedUser->role) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $report->reportedUser) }}"
                                    class="px-3 py-1.5 border border-gray-300 rounded-full text-xs text-gray-700 hover:bg-gray-50">
                                    Lihat Profil
                                </a>
                            </div>
                        @elseif ($report->reported_user_text)
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <span class="text-xs text-gray-500 font-semibold uppercase">Pihak / Pengguna:</span>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $report->reported_user_text }}</p>
                            </div>
                        @endif
                        @if ($report->reportedHelp)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">Bantuan #{{ $report->reportedHelp->id }}</div>
                                        <div class="text-sm text-gray-500 mt-1">{{ $report->reportedHelp->title }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $report->reportedHelp->status)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($report->reported_help_text)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <span class="text-xs text-gray-500 font-semibold uppercase">Bantuan Terkait:</span>
                                <p class="font-medium text-gray-900 mt-0.5">{{ $report->reported_help_text }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-6">
                <!-- Update Status -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Kelola Status Laporan</h2>
                    <form method="POST" action="{{ route('admin.partners.reports.update', $report) }}">
                        @csrf
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Pilih Status Penanganan:</label>
                        <select name="status"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-3 bg-gray-50">
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu Peninjauan (Pending)</option>
                            <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang Ditangani (In Progress)</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                            <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Ditolak / Ditutup (Dismissed)</option>
                        </select>
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                            Simpan Perubahan Status
                        </button>
                    </form>
                </div>

                <!-- Catatan Admin -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Admin</h2>
                    @if ($report->admin_notes)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->admin_notes }}
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mb-4">Belum ada catatan admin.</p>
                    @endif
                    <form method="POST" action="{{ route('admin.partners.reports.add-note', $report) }}">
                        @csrf
                        <textarea name="admin_notes" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-3"
                            placeholder="Tambahkan catatan admin...">{{ $report->admin_notes }}</textarea>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-gray-900">
                            {{ $report->admin_notes ? 'Update Catatan' : 'Tambah Catatan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

