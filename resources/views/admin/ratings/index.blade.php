@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <!-- Filter Bar (Compact) -->
    <div class="bg-white rounded-xl shadow-2xs border border-gray-200 p-3.5 sm:p-4">
        <form method="GET" action="{{ route('admin.ratings.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama pengguna, email, no. hp..."
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="flex gap-2">
                    <select name="role" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-pointer font-medium">
                        <option value="" {{ !request('role') ? 'selected' : '' }}>Semua Role (Mitra & Customer)</option>
                        <option value="mitra" {{ request('role') === 'mitra' ? 'selected' : '' }}>Hanya Mitra</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Hanya Customer</option>
                    </select>
                    <button type="submit"
                        class="px-3.5 py-2 bg-primary-600 text-white text-xs font-semibold rounded-lg shadow-2xs hover:bg-primary-700 transition">
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Rekap Pengguna (Compact & Fits cleanly) -->
    <div class="bg-white rounded-xl shadow-2xs border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Rekap Rating & Ulasan Pengguna</h2>
                <p class="text-[11px] text-gray-500">Monitoring ulasan pengguna di wilayah Anda.</p>
            </div>
            @if ($users->total() > 0)
                <p class="text-[11px] text-gray-500 font-medium">Total {{ $users->total() }} pengguna</p>
            @endif
        </div>

        @if ($users->isEmpty())
            <div class="px-4 py-8 text-center text-gray-500 text-xs">
                Tidak ada pengguna ditemukan di wilayah Anda.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-bold text-gray-600 uppercase tracking-wider">Nama & Kontak</th>
                            <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-24">Role</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-bold text-gray-600 uppercase tracking-wider w-28">Kota</th>
                            <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-32">Rata-rata Rating</th>
                            <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-24">Ulasan</th>
                            <th class="px-3 py-2.5 text-center text-[11px] font-bold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($users as $u)
                            @php
                                $avgRating = $u->isMitra() ? $u->mitra_average_rating : $u->customer_average_rating;
                                $ratingCount = $u->isMitra() ? $u->mitra_rating_count : $u->customer_rating_count;
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors {{ $avgRating > 0 && $avgRating <= 2.5 ? 'bg-red-50/20' : '' }}">
                                <!-- No -->
                                <td class="px-3 py-2.5 text-center text-xs text-gray-500 font-medium whitespace-nowrap">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>

                                <!-- Nama & Kontak -->
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <div class="text-xs font-semibold text-gray-900 truncate leading-tight">{{ $u->name }}</div>
                                            @if($u->isShadowBanned())
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border" style="background-color: #f3e8ff; color: #6b21a8; border-color: #d8b4fe;">
                                                    👻 Shadow Ban
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-500 truncate mt-0.5">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $u->isMitra() ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $u->isMitra() ? 'Mitra' : 'Customer' }}
                                </span>
                            </td>

                            <!-- Kota -->
                            <td class="px-3 py-2.5 text-xs text-gray-600 whitespace-nowrap">
                                {{ $u->city?->name ?? $u->city_name ?? '-' }}
                            </td>

                            <!-- Rata-rata Rating -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                @if($ratingCount > 0)
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $avgRating <= 2.5 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                        <span class="text-yellow-500 text-xs">★</span>
                                        <span class="text-xs font-bold">{{ number_format($avgRating, 1) }}</span>
                                        <span class="text-[10px] text-gray-400">/ 5.0</span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400 italic">Belum ada</span>
                                @endif
                            </td>

                            <!-- Total Ulasan -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap text-xs font-bold text-gray-700">
                                {{ $ratingCount }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <a href="#" data-url="{{ route('admin.ratings.user', $u->id) }}"
                                    class="open-user-ratings-modal inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-black rounded-lg text-xs font-semibold transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex justify-center">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    (function(){
        function setupModalListeners(wrapper){
            if (!wrapper) return;
            var closeBtn = wrapper.querySelector('#user-ratings-modal-close-btn');
            var closeBtn2 = wrapper.querySelector('#user-ratings-modal-close-btn-2');
            var backdrop = wrapper.querySelector('#user-ratings-modal-backdrop');
            var shadowBanBtn = wrapper.querySelector('#admin-toggle-shadow-ban-btn');

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

            // Shadow Ban AJAX toggle
            if (shadowBanBtn) {
                shadowBanBtn.addEventListener('click', function(){
                    var url = this.getAttribute('data-url');
                    var btn = this;
                    var btnText = btn.querySelector('#shadow-ban-btn-text');
                    btn.disabled = true;
                    btn.classList.add('opacity-70');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                        }
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){
                        btn.disabled = false;
                        btn.classList.remove('opacity-70');
                        if (data && data.success) {
                            if (data.is_shadow_banned) {
                                btn.style.backgroundColor = '#7e22ce';
                                btn.style.color = '#ffffff';
                                btn.style.borderColor = '#6b21a8';
                                if (btnText) {
                                    btnText.textContent = 'Shadow Banned (Aktif)';
                                    btnText.style.color = '#ffffff';
                                }
                                btn.title = 'Klik untuk melepaskan Shadow Ban';
                            } else {
                                btn.style.backgroundColor = '#ffffff';
                                btn.style.color = '#374151';
                                btn.style.borderColor = '#d1d5db';
                                if (btnText) {
                                    btnText.textContent = 'Shadow Ban';
                                    btnText.style.color = '#374151';
                                }
                                btn.title = 'Aktifkan Shadow Ban (order/bantuan tidak akan masuk/tampil)';
                            }
                        }
                    })
                    .catch(function(err){
                        btn.disabled = false;
                        btn.classList.remove('opacity-70');
                        console.error('Shadow ban error:', err);
                        alert('Gagal mengubah status shadow ban.');
                    });
                });
            }

            // Shopee pill filtering
            var pills = wrapper.querySelectorAll('.shopee-pill');
            var reviewItems = wrapper.querySelectorAll('.shopee-review-item');
            var emptyNotice = wrapper.querySelector('#shopee-empty-filter');

            pills.forEach(function(pill){
                pill.addEventListener('click', function(){
                    var filter = this.getAttribute('data-filter');

                    pills.forEach(function(p){
                        var pFilter = p.getAttribute('data-filter');
                        if (pFilter === '2' || pFilter === '1') {
                            p.className = 'shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-red-600 hover:bg-red-50';
                        } else {
                            p.className = 'shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-white border-gray-200 text-gray-700 hover:bg-gray-50';
                        }
                    });

                    if (filter === '2' || filter === '1') {
                        this.className = 'shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-red-500 border-red-600 text-white font-bold';
                    } else {
                        this.className = 'shopee-pill px-2.5 py-1 text-[11px] font-semibold rounded-md border transition shadow-2xs bg-gray-200 border-gray-400 text-gray-900 font-bold';
                    }

                    var visibleCount = 0;
                    reviewItems.forEach(function(item){
                        var itemRating = item.getAttribute('data-rating');
                        var hasComment = item.getAttribute('data-has-comment') === '1';
                        var isAnonymous = item.getAttribute('data-is-anonymous') === '1';

                        var match = false;
                        if (filter === 'all') {
                            match = true;
                        } else if (filter === '5' || filter === '4' || filter === '3' || filter === '2' || filter === '1') {
                            match = (itemRating === filter);
                        } else if (filter === 'with_comment') {
                            match = hasComment;
                        } else if (filter === 'anonymous') {
                            match = isAnonymous;
                        }

                        if (match) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (emptyNotice) {
                        if (visibleCount === 0 && reviewItems.length > 0) {
                            emptyNotice.classList.remove('hidden');
                        } else {
                            emptyNotice.classList.add('hidden');
                        }
                    }
                });
            });
        }

        function openUserRatingsModal(url){
            fetch(url, {headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(function(res){
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.text();
                })
                .then(function(html){
                    var wrapper = document.createElement('div');
                    wrapper.id = 'modal-wrapper-container';
                    wrapper.innerHTML = html;
                    document.body.appendChild(wrapper);
                    setupModalListeners(wrapper);
                })
                .catch(function(err){
                    console.error('Failed to load modal:', err);
                    alert('Gagal memuat detail ulasan. Coba lagi.');
                });
        }

        document.addEventListener('click', function(e){
            var elUser = e.target.closest && e.target.closest('.open-user-ratings-modal');
            if (elUser) {
                e.preventDefault();
                var urlUser = elUser.getAttribute('data-url');
                if (urlUser) openUserRatingsModal(urlUser);
            }
        });
    })();
</script>
@endpush
@endsection
