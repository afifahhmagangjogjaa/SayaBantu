@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- 1. Total Pengguna -->
            <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/30 p-4 rounded-2xl border border-blue-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#0066FF] border border-blue-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-blue-900/70 truncate">Total Pengguna</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $counts['total'] ?? ($blocked->total ?? $blocked->count()) }}</div>
                </div>
            </div>

            <!-- 2. Diblokir -->
            <div class="bg-gradient-to-br from-rose-50/80 via-white to-rose-50/30 p-4 rounded-2xl border border-rose-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-rose-900/70 truncate">Diblokir</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-rose-700 leading-tight">{{ $counts['blocked'] ?? 0 }}</div>
                </div>
            </div>

            <!-- 3. Aktif -->
            <div class="bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 p-4 rounded-2xl border border-emerald-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 border border-emerald-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-emerald-900/70 truncate">Aktif</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-emerald-700 leading-tight">{{ $counts['active'] ?? 0 }}</div>
                </div>
            </div>

            <!-- 4. Mitra -->
            <div class="bg-gradient-to-br from-sky-50/80 via-white to-sky-50/30 p-4 rounded-2xl border border-sky-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 border border-sky-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-sky-900/70 truncate">Mitra</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-sky-700 leading-tight">{{ $counts['mitra'] ?? 0 }}</div>
                </div>
            </div>

            <!-- 5. Customer -->
            <div class="bg-gradient-to-br from-indigo-50/80 via-white to-indigo-50/30 p-4 rounded-2xl border border-indigo-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 border border-indigo-200/80 flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-indigo-900/70 truncate">Customer</div>
                    <div class="mt-0.5 text-xl sm:text-2xl font-black text-indigo-700 leading-tight">{{ $counts['customer'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div>
            <form method="GET" action="{{ route('admin.partners.blocked') }}"
                class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-4 sm:p-5">
                
                <!-- Header / Title -->
                <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-[#0066FF] flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Filter Blokir Pengguna</h3>
                    </div>

                    @if (request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.partners.blocked') }}" wire:navigate
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100/80 px-2.5 py-1 rounded-lg transition border border-rose-200/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filter
                        </a>
                    @endif
                </div>

                <!-- Filter Controls Grid (12 Columns) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Cari Pengguna</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input id="partner-search" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, email, atau no. telepon..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] transition" />
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Role</label>
                        <select name="role" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm text-gray-800 font-medium focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                            <option value="">Semua Role</option>
                            <option value="mitra" {{ request('role')=='mitra' ? 'selected' : '' }}>Mitra</option>
                            <option value="customer" {{ request('role')=='customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-gray-50/50 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-sm text-gray-800 font-medium focus:ring-2 focus:ring-[#0066FF] focus:border-[#0066FF] cursor-pointer transition">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="blocked" {{ request('status')=='blocked' ? 'selected' : '' }}>Diblokir</option>
                        </select>
                    </div>

                    <!-- Action Button -->
                    <div class="lg:col-span-2 flex items-center">
                        <button type="submit"
                            class="w-full h-[42px] inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0040A8] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Terapkan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 leading-tight">Daftar Pengguna</h2>
                    <p class="text-xs text-gray-500 mt-1">Kelola status akun pengguna dengan role Mitra dan Customer.</p>
                </div>
                @if (($blocked->total() ?? 0) > 0)
                    <p class="text-xs text-gray-500 font-medium">Total {{ $blocked->total() }} pengguna.</p>
                @endif
            </div>

            @if ($blocked->isEmpty())
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 9V5a4 4 0 118 0v4m-1 4H9m10 0a2 2 0 01-2 2H9a2 2 0 01-2-2m12 0V9a2 2 0 00-2-2h-1M7 13v-2a2 2 0 012-2h1" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">Belum ada pengguna dengan role Mitra atau Customer.</p>
                    <p class="text-gray-400 text-xs mt-1">Pengguna dapat diblokir atau diaktifkan dari halaman ini.</p>
                </div>
            @else
                <div class="px-6 py-5">
                    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-2xs">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/90 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-12">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kota</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($blocked as $user)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-3 py-3 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                            {{ ($blocked->firstItem() ?: 1) + $loop->index }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                            <a href="{{ route('admin.users.show', $user) }}" wire:navigate
                                                class="text-sm font-semibold text-primary-600 hover:text-primary-700 hover:underline">
                                                {{ $user->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-700 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">{{ $user->role === 'kustomer' ? 'Customer' : ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                            @php
                                                $cityRelation = $user->getRelation('city');
                                            @endphp
                                            @if($cityRelation && is_object($cityRelation))
                                                {{ $cityRelation->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($user->status === 'blocked')
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">Diblokir</span>
                                            @else
                                                <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">Aktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5 leading-none">
                                                <a href="{{ route('admin.partners.activity', ['search' => $user->email]) }}" wire:navigate
                                                    class="inline-flex items-center justify-center px-2.5 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 transition shadow-2xs leading-none">
                                                    Aktivitas
                                                </a>

                                                <button type="button"
                                                    data-form-id="block-form-{{ $user->id }}"
                                                    class="open-blocked-action-btn inline-flex items-center justify-center px-2.5 py-1.5 border border-transparent rounded-lg text-xs font-medium transition shadow-2xs leading-none cursor-pointer {{ $user->status === 'blocked' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                                                    {{ $user->status === 'blocked' ? 'Buka' : 'Blokir' }}
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('admin.partners.toggle', $user->id) }}" id="block-form-{{ $user->id }}" class="hidden confirm-action-form" data-action-type="{{ $user->status === 'blocked' ? 'unblock' : 'block' }}" data-user-name="{{ $user->name }}">
                                                @csrf
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-700">{{ $blocked->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-700">{{ $blocked->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ $blocked->total() ?? $blocked->count() }}</span> data
                            <span class="text-gray-300 mx-1.5">•</span>
                            <span>Halaman <span class="font-semibold text-gray-700">{{ $blocked->currentPage() }}</span> dari <span class="font-semibold text-gray-700">{{ $blocked->lastPage() }}</span></span>
                        </div>
                        <div>
                            {{ $blocked->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

{{-- Confirmation modal for block/unblock actions --}}
<div id="confirmActionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b">
            <h3 id="confirmActionTitle" class="text-lg font-semibold text-gray-900">Konfirmasi</h3>
        </div>
        <div class="px-6 py-4">
            <p id="confirmActionMessage" class="text-sm text-gray-700"></p>
        </div>
        <div class="px-6 py-4 flex justify-end space-x-2 border-t">
            <button id="confirmCancel" class="px-4 py-2 rounded bg-gray-100 text-gray-700">Batal</button>
            <button id="confirmProceed" class="px-4 py-2 rounded bg-red-600 text-white">Lanjutkan</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('confirmActionModal');
        const message = document.getElementById('confirmActionMessage');
        const title = document.getElementById('confirmActionTitle');
        const btnCancel = document.getElementById('confirmCancel');
        const btnProceed = document.getElementById('confirmProceed');

        let pendingForm = null;

        function openModal(actionType, userName) {
            title.textContent = 'Konfirmasi ' + (actionType === 'block' ? 'Blokir' : 'Buka Blokir');
            message.textContent = (actionType === 'block') ? `Anda yakin ingin memblokir pengguna "${userName}"? Tindakan ini akan mencegah pengguna masuk.` : `Anda yakin ingin membuka blokir pengguna "${userName}"? Pengguna akan dapat masuk kembali.`;
            btnProceed.textContent = (actionType === 'block') ? 'Blokir' : 'Buka Blokir';
            if (actionType === 'block') {
                btnProceed.classList.remove('bg-green-600');
                btnProceed.classList.add('bg-red-600');
            } else {
                btnProceed.classList.remove('bg-red-600');
                btnProceed.classList.add('bg-green-600');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingForm = null;
        }

        // Delegate clicks: intercept button clicks with .open-blocked-action-btn or inside .confirm-action-form
        document.addEventListener('click', function (e) {
            const blockBtn = e.target.closest && e.target.closest('.open-blocked-action-btn');
            if (blockBtn) {
                e.preventDefault();
                const formId = blockBtn.getAttribute('data-form-id');
                const form = document.getElementById(formId);
                if (!form) return;
                pendingForm = form;
                const actionType = form.getAttribute('data-action-type') || 'block';
                const userName = form.getAttribute('data-user-name') || '';
                openModal(actionType, userName);
                return;
            }

            const btn = e.target.closest && e.target.closest('button');
            if (!btn) return;
            const form = btn.closest && btn.closest('form.confirm-action-form');
            if (!form) return;

            // Only intercept when button would submit the form
            const type = (btn.getAttribute('type') || 'submit').toLowerCase();
            if (type !== 'submit') return;

            e.preventDefault();
            pendingForm = form;
            const actionType = form.getAttribute('data-action-type') || 'block';
            const userName = form.getAttribute('data-user-name') || '';
            openModal(actionType, userName);
        }, true);

        btnCancel.addEventListener('click', function () {
            closeModal();
        });

        btnProceed.addEventListener('click', function () {
            if (!pendingForm) return closeModal();
            // Submit the stored form
            pendingForm.submit();
            closeModal();
        });
    });
</script>

<script>
    // Clear search button behavior
    document.addEventListener('DOMContentLoaded', function () {
        var search = document.getElementById('partner-search');
        var clearBtn = document.getElementById('clear-search');
        function updateClearVisibility() {
            if (!search) return;
            if (search.value && search.value.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        if (search && clearBtn) {
            updateClearVisibility();
            search.addEventListener('input', updateClearVisibility);
            clearBtn.addEventListener('click', function () {
                search.value = '';
                updateClearVisibility();
                // optionally submit to reset results client-side
            });
        }
    });
</script>
