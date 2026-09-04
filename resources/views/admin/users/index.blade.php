@extends('layouts.admin')

@section('content')

    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="mb-6">
            <form method="GET" action="{{ url()->current() }}"
                class="bg-white rounded-2xl shadow-md border border-gray-200 p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-gray-700">Filter {{ request()->routeIs('admin.customers*') ? 'Customer' : (request()->routeIs('admin.mitra*') ? 'Mitra' : 'Pengguna') }}</p>
                    @if (request()->hasAny(['search', 'role', 'account_status', 'ktp_status']))
                        <a href="{{ url()->current() }}"
                            class="text-[11px] text-gray-400 hover:text-gray-600 underline">Reset filter</a>
                    @endif
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari {{ request()->routeIs('admin.customers*') ? 'Customer' : (request()->routeIs('admin.mitra*') ? 'Mitra' : 'Pengguna') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama atau email..."
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ !request()->routeIs('admin.customers*') && !request()->routeIs('admin.mitra*') ? '4' : '3' }} gap-4 items-end">
                        @if(!request()->routeIs('admin.customers*') && !request()->routeIs('admin.mitra*'))
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Role</label>
                            <select name="role" onchange="this.form.submit()"
                                class="w-full pl-3.5 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer">
                                <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Role</option>
                                <option value="mitra" {{ request('role') === 'mitra' ? 'selected' : '' }}>Mitra</option>
                                <option value="kustomer" {{ in_array(request('role'), ['kustomer', 'customer']) ? 'selected' : '' }}>Customer</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Status Akun</label>
                            <select name="account_status" onchange="this.form.submit()"
                                class="w-full pl-3.5 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer">
                                <option value="" {{ request('account_status') === null || request('account_status') === '' ? 'selected' : '' }}>Semua</option>
                                <option value="active" {{ request('account_status') === 'active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="inactive"
                                    {{ request('account_status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="blocked" {{ request('account_status') === 'blocked' ? 'selected' : '' }}>Diblokir
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Status KTP</label>
                            <select name="ktp_status" onchange="this.form.submit()"
                                class="w-full pl-3.5 pr-8 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer">
                                <option value="" {{ request('ktp_status') === null || request('ktp_status') === '' ? 'selected' : '' }}>Semua</option>
                                <option value="uploaded" {{ request('ktp_status') === 'uploaded' ? 'selected' : '' }}>Sudah
                                    Upload</option>
                                <option value="missing" {{ request('ktp_status') === 'missing' ? 'selected' : '' }}>Belum
                                    Upload</option>
                            </select>
                        </div>

                        <div class="flex justify-start sm:justify-end">
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[11px] text-gray-400">Kombinasikan pencarian, role, status akun, dan status KTP untuk
                        menemukan pengguna secara cepat.</p>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Pengguna</h2>
                    <p class="text-xs text-gray-500 mt-1">Pantau dan kelola akun pengguna yang terdaftar di platform.</p>
                </div>
                @if ($users->total() > 0)
                    <p class="text-[11px] text-gray-500">Total {{ $users->total() }} pengguna.</p>
                @endif
            </div>

            @if ($users->isEmpty())
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-5-4M9 20H4v-2a4 4 0 015-4m4-6a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">Belum ada pengguna di kota Anda.</p>
                    <p class="text-gray-400 text-xs mt-1">Pengguna baru akan muncul di sini setelah mereka mendaftar.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-12">No</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pengguna</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kota</th>
                                <th class="px-4 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Terdaftar</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">KTP</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Rating</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-xs">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-gray-900 leading-tight">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ in_array($user->role, ['customer', 'kustomer']) ? 'Customer' : ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                        @if(!empty($user->city_name))
                                             {{ $user->city_name }}
                                        @elseif($user->city_id)
                                            {{ $user->city_name ?? $user->city_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                        {{ optional($user->created_at)->format('Y-m-d') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($user->ktp_path || $user->ktp_photo)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-200" title="KTP Telah Diunggah">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Ada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-50 text-gray-500 text-[10px] font-semibold border border-gray-200">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @php
                                            $ratingsCount = $user->ratings_count ?? 0;
                                            $avgRating = $user->average_rating ?? null;
                                        @endphp

                                        @if (!is_null($avgRating) && $avgRating > 0 && $ratingsCount > 0)
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                                ★ {{ number_format($avgRating, 1) }}
                                            </span>
                                            <span class="text-[11px] text-gray-500 ml-1">({{ $ratingsCount }})</span>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if ($user->status === 'blocked')
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                                                Diblokir
                                            </span>
                                        @elseif ($user->status === 'inactive')
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                Nonaktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-2 leading-none">
                                            <a href="#" data-url="{{ route('admin.users.show', $user) }}" 
                                               class="open-user-detail p-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg transition shadow-2xs leading-none cursor-pointer"
                                               title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <button type="button"
                                                data-form-id="block-form-{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-is-blocked="{{ $user->status === 'blocked' ? '1' : '0' }}"
                                                class="open-block-modal inline-flex items-center justify-center px-2.5 py-1.5 border border-transparent rounded-lg text-xs font-medium transition shadow-2xs leading-none cursor-pointer {{ $user->status === 'blocked' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-red-600 text-white hover:bg-red-700' }}">
                                                {{ $user->status === 'blocked' ? 'Buka' : 'Blokir' }}
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.partners.toggle', $user->id) }}" method="POST" id="block-form-{{ $user->id }}" class="hidden block-toggle-form">
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-center">
                        {{ $users->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

<!-- Confirm Block Modal -->
<div id="confirm-block-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="confirm-block-backdrop" class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md z-10">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold">Konfirmasi</h3>
            <button id="confirm-block-close" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-4">
            <p id="confirm-block-message" class="text-sm text-gray-700">Apakah Anda yakin ingin memblokir pengguna ini?</p>
        </div>
        <div class="p-4 border-t flex justify-end gap-2">
            <button id="confirm-block-cancel" class="px-4 py-2 bg-gray-100 rounded">Batal</button>
            <button id="confirm-block-confirm" class="px-4 py-2 bg-red-600 text-white rounded">Konfirmasi</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        function setupModalListeners(wrapper){
            if (!wrapper) return;
            var closeBtn = wrapper.querySelector('#modal-close-btn');
            var closeBtn2 = wrapper.querySelector('#modal-close-btn-2');
            var backdrop = wrapper.querySelector('#modal-backdrop');

            function removeWrapper(){
                if (wrapper && wrapper.parentNode) wrapper.parentNode.removeChild(wrapper);
                document.removeEventListener('keydown', onKeyDown);
            }

            function onKeyDown(e){
                if (e.key === 'Escape') removeWrapper();
            }

            if (closeBtn) closeBtn.addEventListener('click', removeWrapper);
            if (closeBtn2) closeBtn2.addEventListener('click', removeWrapper);
            if (backdrop) backdrop.addEventListener('click', removeWrapper);
            document.addEventListener('keydown', onKeyDown);
        }

        function openUserDetail(url){
            fetch(url, {headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(function(res){
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.text();
                })
                .then(function(html){
                    // create wrapper and insert modal HTML
                    var wrapper = document.createElement('div');
                    wrapper.id = 'user-detail-modal-wrapper';
                    wrapper.innerHTML = html;
                    document.body.appendChild(wrapper);
                    // attach listeners to modal elements inside the wrapper
                    setupModalListeners(wrapper);
                })
                .catch(function(err){
                    console.error('Failed to load user detail:', err);
                    alert('Gagal memuat detail pengguna. Coba lagi.');
                });
        }

        document.addEventListener('click', function(e){
            var el = e.target.closest && e.target.closest('.open-user-detail');
            if (!el) return;
            e.preventDefault();
            var url = el.getAttribute('data-url');
            if (url) openUserDetail(url);
        });

        // Block/unblock confirmation modal handling
        var blockModal = null;
        var blockBackdrop = null;
        var blockMsg = null;
        var blockCancel = null;
        var blockClose = null;
        var blockConfirm = null;
        var blockFormToSubmit = null;

        function initBlockModalElements(){
            blockModal = document.getElementById('confirm-block-modal');
            if (!blockModal) return false;
            blockBackdrop = document.getElementById('confirm-block-backdrop');
            blockMsg = document.getElementById('confirm-block-message');
            blockCancel = document.getElementById('confirm-block-cancel');
            blockClose = document.getElementById('confirm-block-close');
            blockConfirm = document.getElementById('confirm-block-confirm');
            return true;
        }

        function showBlockModal(form){
            if (!initBlockModalElements()) return;
            blockFormToSubmit = form;
            var name = form.dataset.userName || '';
            var isBlocked = (form.dataset.isBlocked === '1');

            if (isBlocked) {
                blockMsg.textContent = 'Apakah Anda yakin ingin membuka blokir pengguna "' + name + '"?';
                blockConfirm.textContent = 'Buka Blokir';
                blockConfirm.classList.remove('bg-red-600');
                blockConfirm.classList.add('bg-green-600');
            } else {
                blockMsg.textContent = 'Apakah Anda yakin ingin memblokir pengguna "' + name + '"?';
                blockConfirm.textContent = 'Konfirmasi';
                blockConfirm.classList.remove('bg-green-600');
                blockConfirm.classList.add('bg-red-600');
            }

            blockModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideBlockModal(){
            if (!initBlockModalElements()) return;
            blockModal.classList.add('hidden');
            document.body.style.overflow = '';
            blockFormToSubmit = null;
        }

        // Delegate click for open-block-modal
        document.addEventListener('click', function(ev){
            var btn = ev.target.closest && ev.target.closest('.open-block-modal');
            if (!btn) return;
            ev.preventDefault();
            var formId = btn.getAttribute('data-form-id');
            var form = document.getElementById(formId);
            if (!form) return;
            form.dataset.userName = btn.getAttribute('data-user-name') || '';
            form.dataset.isBlocked = btn.getAttribute('data-is-blocked') || '0';
            showBlockModal(form);
        });

        // Intercept submit on block forms
        document.addEventListener('submit', function(ev){
            var f = ev.target.closest && ev.target.closest('.block-toggle-form');
            if (!f) return;
            ev.preventDefault();
            showBlockModal(f);
        }, true);

        // Delegate clicks for modal buttons (in case init runs before modal exists)
        document.addEventListener('click', function(ev){
            var t = ev.target;
            if (!t) return;
            if (t.id === 'confirm-block-cancel' || t.id === 'confirm-block-close' || t.id === 'confirm-block-backdrop'){
                hideBlockModal();
            }
            if (t.id === 'confirm-block-confirm'){
                if (blockFormToSubmit) {
                    // submit the original form
                    blockFormToSubmit.submit();
                }
            }
        });
    })();
</script>
@endpush
