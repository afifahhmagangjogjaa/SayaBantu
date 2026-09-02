@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-6 border-b border-gray-100">
                <div class="flex items-start justify-between gap-6 flex-nowrap">
                    <div class="flex-shrink-0 pt-3">
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">Daftar Pengguna</h2>
                        <p class="text-xs text-gray-500 mt-1 whitespace-nowrap">Kelola pengguna dengan role Mitra dan Customer.</p>
                    </div>
                    <div class="flex-1 max-w-3xl ml-4 flex justify-end">
                        <form method="GET" action="{{ route('admin.partners.blocked') }}" class="w-full">
                            <div class="bg-white border border-gray-200 rounded-xl shadow-xs p-2.5 w-full">
                                <div class="flex items-center gap-2 flex-nowrap">
                                    <div class="relative flex-1 min-w-[180px]">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input id="partner-search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon" class="w-full pl-10 pr-8 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white" />
                                        <button type="button" id="clear-search" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 hidden">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <select name="role" onchange="this.form.submit()" class="w-32 px-2.5 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none cursor-pointer">
                                            <option value="">Semua Role</option>
                                            <option value="mitra" {{ request('role')=='mitra' ? 'selected' : '' }}>Mitra</option>
                                            <option value="customer" {{ request('role')=='customer' ? 'selected' : '' }}>Customer</option>
                                        </select>
                                        <select name="status" onchange="this.form.submit()" class="w-32 px-2.5 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none cursor-pointer">
                                            <option value="">Semua Status</option>
                                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="blocked" {{ request('status')=='blocked' ? 'selected' : '' }}>Diblokir</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-semibold shadow-xs hover:bg-primary-700 transition">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 8 4-16 3 8h4"/></svg>
                                            Filter
                                        </button>
                                        <a href="{{ route('admin.partners.blocked') }}" class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-2xs">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-1.5 text-xs text-gray-400">Gunakan filter untuk mencari pengguna berdasarkan nama, email, role atau status.</div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Top cards --}}
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <div class="text-xs text-gray-500">Total Pengguna</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900">{{ $counts['total'] ?? ($blocked->total ?? $blocked->count()) }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border">
                        <div class="text-xs text-red-600">Diblokir</div>
                        <div class="mt-1 text-xl font-semibold text-red-700">{{ $counts['blocked'] ?? 0 }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border">
                        <div class="text-xs text-green-600">Aktif</div>
                        <div class="mt-1 text-xl font-semibold text-green-700">{{ $counts['active'] ?? 0 }}</div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border">
                        <div class="text-xs text-blue-600">Mitra</div>
                        <div class="mt-1 text-xl font-semibold text-blue-700">{{ $counts['mitra'] ?? 0 }}</div>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg border">
                        <div class="text-xs text-indigo-600">Customer</div>
                        <div class="mt-1 text-xl font-semibold text-indigo-700">{{ $counts['customer'] ?? 0 }}</div>
                    </div>
                </div>
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
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                            <a href="{{ route('admin.users.show', $user) }}"
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
                                                <a href="{{ route('admin.partners.activity', ['search' => $user->email]) }}"
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

                <div class="px-6 pb-6 pt-1">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500">Menampilkan {{ $blocked->firstItem() ?? 0 }} - {{ $blocked->lastItem() ?? 0 }} dari {{ $blocked->total() ?? $blocked->count() }} hasil</div>
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
