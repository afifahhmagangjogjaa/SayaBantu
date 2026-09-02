@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Withdraw</h2>
                    <p class="text-xs text-gray-500 mt-1">Menampilkan permintaan tarik saldo terbaru dari mitra.</p>
                </div>
            </div>

            <!-- Summary Cards (Clickable Quick Filters) -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    <a href="{{ route('admin.withdraws.index') }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ !request('status') ? 'bg-primary-50 border-primary-300 ring-2 ring-primary-200' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                        <div class="text-xs font-medium text-gray-500">Semua Request</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">{{ $counts['all'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('admin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'pending' ? 'bg-yellow-50 border-yellow-300 ring-2 ring-yellow-200' : 'bg-white border-gray-200 hover:border-yellow-200' }}">
                        <div class="text-xs font-medium text-yellow-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                            Pending
                        </div>
                        <div class="text-xl font-bold text-yellow-800 mt-1">{{ $counts['pending'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('admin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'processing' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : 'bg-white border-gray-200 hover:border-blue-200' }}">
                        <div class="text-xs font-medium text-blue-700">Diproses</div>
                        <div class="text-xl font-bold text-blue-800 mt-1">{{ $counts['processing'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('admin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'success'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'success' ? 'bg-green-50 border-green-300 ring-2 ring-green-200' : 'bg-white border-gray-200 hover:border-green-200' }}">
                        <div class="text-xs font-medium text-green-700">Berhasil</div>
                        <div class="text-xl font-bold text-green-800 mt-1">{{ $counts['success'] ?? 0 }}</div>
                    </a>
                    <a href="{{ route('admin.withdraws.index', array_merge(request()->except('status', 'page'), ['status' => 'failed'])) }}" 
                        class="p-3.5 rounded-xl border transition-all duration-200 {{ request('status') === 'failed' ? 'bg-red-50 border-red-300 ring-2 ring-red-200' : 'bg-white border-gray-200 hover:border-red-200' }}">
                        <div class="text-xs font-medium text-red-700">Ditolak/Gagal</div>
                        <div class="text-xl font-bold text-red-800 mt-1">{{ $counts['failed'] ?? 0 }}</div>
                    </a>
                </div>

                <!-- Unified Filter Form -->
                <form method="GET" action="{{ route('admin.withdraws.index') }}" class="mt-4 pt-4 border-t border-gray-200/70">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Mitra / Rekening / ID / Nominal</label>
                            <div class="relative w-full flex items-center">
                                <!-- Search Icon strictly inside Input -->
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-gray-400" style="padding-left: 12px;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') ?? request('user') }}"
                                    style="padding-left: 38px; padding-right: 34px; padding-top: 8px; padding-bottom: 8px; font-size: 14px;"
                                    class="w-full bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                    placeholder="Nama mitra, no. rek, email, ID (#12), nominal..." />
                                @if(request('search') || request('user'))
                                    <a href="{{ route('admin.withdraws.index', request()->except('search', 'user', 'page')) }}" 
                                       class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer" 
                                       style="padding-right: 12px;"
                                       title="Hapus pencarian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil (Success)</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Ditolak / Gagal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bank</label>
                            <select name="bank_code" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer">
                                <option value="">Semua Bank</option>
                                @foreach(($banks ?? []) as $b)
                                    <option value="{{ $b }}" {{ request('bank_code') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition cursor-pointer" />
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            @if(request()->hasAny(['search', 'user', 'status', 'bank_code', 'date_from', 'date_to']))
                                <span class="inline-flex items-center text-primary-700 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary-600 mr-1.5"></span>
                                    Filter sedang aktif ({{ $items->total() }} data ditemukan)
                                </span>
                            @else
                                Menampilkan total {{ $items->total() }} data
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if(request()->hasAny(['search', 'user', 'status', 'bank_code', 'date_from', 'date_to']))
                                <a href="{{ route('admin.withdraws.index') }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-xs font-semibold">
                                    Reset Filter
                                </a>
                            @endif
                            <button type="submit" 
                                class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition text-xs font-semibold shadow-sm flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Cari / Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mitra</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jumlah</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Rekening Tujuan</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-2.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">{{ $items->firstItem() + $loop->index }}</td>
                                <td class="px-2.5 py-2.5 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-900">
                                        {{ $item->created_at->isToday() ? 'Hari ini' : ($item->created_at->isYesterday() ? 'Kemarin' : $item->created_at->translatedFormat('d M Y')) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-2.5 py-2.5">
                                    @if($item->user)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shadow-2xs flex-shrink-0">
                                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-sm font-semibold text-gray-900 truncate block max-w-[150px]">{{ $item->user->name }}</span>
                                                <div class="text-[10px] text-gray-500 mt-0.5">WD: #{{ $item->id }} • ID: {{ $item->user_id }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">User terhapus</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-2.5 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">Rp {{ number_format($item->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-2.5 py-2.5 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-800">{{ $item->bank_code }}</div>
                                    <div class="text-[10px] font-mono text-gray-500 mt-0.5">{{ $item->account_number }}</div>
                                </td>
                                <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1 animate-pulse"></span> Pending
                                        </span>
                                    @elseif($item->status === 'processing')
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span> Diproses
                                        </span>
                                    @elseif($item->status === 'success')
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span> Selesai
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span> Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-2.5 text-center whitespace-nowrap">
                                    @if(auth()->user() && auth()->user()->isSuperAdmin())
                                        <button data-id="{{ $item->id }}"
                                            class="open-withdraw-modal inline-flex items-center px-2.5 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-primary-600 transition shadow-2xs text-xs font-semibold">
                                            Proses
                                        </button>
                                    @else
                                        <a href="{{ route('admin.withdraws.show', $item) }}" 
                                           class="inline-flex items-center px-2.5 py-1 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-primary-600 transition shadow-2xs text-xs font-semibold">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada request withdraw</h3>
                                    <p class="mt-1 text-sm text-gray-500">Permintaan penarikan saldo dari mitra akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-center">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
    <!-- Modal container -->
    <div id="admin-withdraw-modal-container"></div>

    <script>
        (function () {
            function openModal(id) {
                var container = document.getElementById('admin-withdraw-modal-container');
                // Fetch modal HTML
                fetch('/admin/withdraws/' + id + '/modal')
                    .then(function (res) { return res.text(); })
                    .then(function (html) {
                        container.innerHTML = html;
                        // Ensure modal scripts/behavior are initialized after injection
                        initAdminWithdrawModal(container);
                        // Optionally scroll to top so modal is visible
                        window.scrollTo(0, 0);
                    })
                    .catch(function (err) { console.error('Failed to load modal:', err); });
            }

            document.querySelectorAll('.open-withdraw-modal').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = this.getAttribute('data-id');
                    openModal(id);
                });
            });
        })();

        // Initialize modal behavior for injected admin withdraw modal
        function initAdminWithdrawModal(container) {
            // Find the injected modal node
            var modal = container.querySelector('#admin-withdraw-modal');
            if (!modal) return;

            var overlay = modal.querySelector('#admin-withdraw-modal-overlay');
            var closeBtn = modal.querySelector('#close-admin-withdraw-modal');

            function removeModal() { container.innerHTML = ''; }
            if (closeBtn) closeBtn.addEventListener('click', removeModal);
            if (overlay) overlay.addEventListener('click', removeModal);

            // Inline reject form (toggle visibility inside the injected modal)
            // support both the old and new ID names for the reject trigger
            var openReject = modal.querySelector('#open-reject-modal-local, #open-reject-local');
            var rejectForm = modal.querySelector('#reject-form-local');
            var rejectCancel = modal.querySelector('#reject-cancel-local');

            // lock body scroll while modal is open and restore on close
            document.body.style.overflow = 'hidden';
            function restoreBodyScroll() { document.body.style.overflow = ''; }
            // ensure restore when modal removed
            var originalRemove = removeModal;
            removeModal = function () { restoreBodyScroll(); originalRemove(); };

            function showReject() {
                if (rejectForm) {
                    rejectForm.classList.remove('hidden');
                    // focus first input when shown
                    var input = rejectForm.querySelector('input[name="note"]');
                    if (input) input.focus();
                }
            }
            function hideReject() { if (rejectForm) rejectForm.classList.add('hidden'); }

            if (openReject) openReject.addEventListener('click', showReject);
            if (rejectCancel) rejectCancel.addEventListener('click', hideReject);
        }

        // Auto-refresh daftar withdraw setiap 8 detik jika tidak sedang membuka modal
        setInterval(function() {
            var modalContainer = document.getElementById('admin-withdraw-modal-container');
            var isModalOpen = modalContainer && modalContainer.children.length > 0;
            var isInputFocused = document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA');
            if (!isModalOpen && !isInputFocused) {
                // Fetch silent update
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(res) { return res.text(); })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newTable = doc.querySelector('.bg-white.rounded-2xl.shadow-lg');
                        var currentTable = document.querySelector('.bg-white.rounded-2xl.shadow-lg');
                        if (newTable && currentTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                            // Re-bind modal triggers
                            document.querySelectorAll('.open-withdraw-modal').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    var id = this.getAttribute('data-id');
                                    var container = document.getElementById('admin-withdraw-modal-container');
                                    fetch('/admin/withdraws/' + id + '/modal')
                                        .then(function (res) { return res.text(); })
                                        .then(function (mHtml) {
                                            container.innerHTML = mHtml;
                                            initAdminWithdrawModal(container);
                                            window.scrollTo(0, 0);
                                        });
                                });
                            });
                        }
                    })
                    .catch(function(e) { /* ignore */ });
            }
        }, 8000);
    </script>
@endsection