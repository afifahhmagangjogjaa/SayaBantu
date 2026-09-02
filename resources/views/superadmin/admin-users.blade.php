<div>
    <!-- Header Summary Card & Action -->
    <div class="bg-white rounded-2xl border border-gray-200/80 p-3 sm:px-5 sm:py-3.5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50/80 border border-blue-100/80 flex items-center justify-center text-blue-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-base sm:text-lg font-bold text-gray-900">
                Jumlah admin saat ini: <span class="text-blue-600 font-extrabold ml-1">{{ $users->total() }}</span>
            </span>
        </div>
        <button wire:click="openCreateModal"
            class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition shadow-sm hover:shadow flex items-center justify-center space-x-2 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Admin</span>
        </button>
    </div>
    <div wire:loading class="text-sm text-primary-700 mb-4">Memproses... Mohon tunggu.</div>

    <div>
        <!-- Filter Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5 mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
                <!-- Search Input with inline Icon -->
                <div class="relative w-full md:flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" style="padding-left: 14px;">
                        <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari nama, email, atau HP..."
                        style="padding-left: 44px; padding-right: 16px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                        class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <!-- Per Page Filter -->
                    <div class="relative min-w-[120px] w-full md:w-auto">
                        <select wire:model.live="perPage"
                            style="padding-left: 14px; padding-right: 36px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                            class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-800 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <option value="10">10 Data</option>
                            <option value="25">25 Data</option>
                            <option value="50">50 Data</option>
                            <option value="100">100 Data</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama & Email</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">No. HP</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Verified</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kota Dikelola</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Terdaftar</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-3 text-center text-xs font-medium text-gray-500 whitespace-nowrap">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>
                                <td class="px-2.5 py-3">
                                    <div class="flex items-center gap-3" style="gap: 12px;">
                                        <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0" style="margin-left: 4px;">
                                            <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[160px]" title="{{ $user->name }}">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-[160px]" title="{{ $user->email }}">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-700 font-mono">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full {{ $user->verified ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                        {{ $user->verified ? 'Terverifikasi' : 'Belum' }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full {{ (isset($user->status) && $user->status === 'active') ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ (isset($user->status) && $user->status === 'active') ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 text-xs text-gray-600">
                                    @if($user->managedCities && $user->managedCities->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->managedCities as $managedCity)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $managedCity->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span>{{ $user->city_name ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5 leading-none">
                                        {{-- Detail --}}
                                        <button wire:click="viewUser({{ $user->id }})" wire:loading.attr="disabled"
                                            wire:target="viewUser,editUser,confirmDelete,deleteUser"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        {{-- Edit --}}
                                        <button wire:click="editUser({{ $user->id }})" wire:loading.attr="disabled"
                                            wire:target="viewUser,editUser,confirmDelete,deleteUser"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- Toggle Status ON/OFF --}}
                                        <button type="button"
                                            x-data="{ on: {{ (isset($user->status) && $user->status === 'active') ? 'true' : 'false' }} }"
                                            @click="on = !on; $wire.toggleStatus({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleStatus({{ $user->id }})"
                                            class="inline-flex items-center gap-1.5 cursor-pointer focus:outline-none"
                                            title="{{ (isset($user->status) && $user->status === 'active') ? 'Aktif — Klik me-nonaktifkan' : 'Nonaktif — Klik mengaktifkan' }}">
                                            <span class="inline-flex items-center h-5 w-9 p-0.5 rounded-full transition-colors duration-200 ease-in-out"
                                                :class="on ? 'bg-emerald-500 justify-end' : 'bg-gray-300 justify-start'">
                                                <span class="h-4 w-4 rounded-full bg-white shadow ring-0"></span>
                                            </span>
                                        </button>
                                        {{-- Hapus --}}
                                        <button wire:click="confirmDelete({{ $user->id }})" wire:loading.attr="disabled"
                                            wire:target="viewUser,editUser,confirmDelete,deleteUser"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada data admin</p>
                                        <p class="text-gray-400 text-xs mt-0.5">Coba ubah filter atau tambah admin baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="bg-gray-50 px-6 py-5 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- View Admin Modal --}}
    @if($showViewModal && $selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}</div>
                        <div>
                            <div class="text-lg font-semibold text-gray-900">{{ $selectedUser->name }}</div>
                            <div class="text-sm text-gray-500">{{ $selectedUser->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click.prevent="closeModal"
                            class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                    </div>
                </div>

                <div class="px-6 py-6 max-h-[80vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500">Nama Lengkap</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->name }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Email</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->email }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">No. HP</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->phone ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">NIK</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->nik ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Tempat, Tanggal Lahir</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->place_of_birth ?? '-' }},
                                    {{ optional($selectedUser->date_of_birth)->format('d M Y') ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Jenis Kelamin</div>
                                <div class="font-medium text-gray-900">
                                    {{ $selectedUser->gender ? ucfirst($selectedUser->gender) : '-' }}</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <div class="text-xs text-gray-500">Role</div>
                                <div class="font-medium text-gray-900">Admin</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="font-medium text-gray-900">
                                    {{ $selectedUser->status ? ucfirst($selectedUser->status) : '—' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Verified</div>
                                <div class="font-medium text-gray-900">
                                    {{ $selectedUser->verified ? 'Terverifikasi' : 'Belum' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Kota</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->city_name ?? '-' }}</div>
                            </div>

                            @if($selectedUser->managedCities && $selectedUser->managedCities->count() > 0)
                            <div>
                                <div class="text-xs text-gray-500 mb-2">Kota yang Dikelola</div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedUser->managedCities as $managedCity)
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $managedCity->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div>
                                <div class="text-xs text-gray-500">Pekerjaan</div>
                                <div class="font-medium text-gray-900">{{ $selectedUser->occupation ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500">Terdaftar</div>
                                <div class="font-medium text-gray-900">
                                    {{ optional($selectedUser->created_at)->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="text-xs text-gray-500">Alamat Lengkap</div>
                        <div class="mt-2 p-4 bg-gray-50 rounded-lg text-gray-800">{{ $selectedUser->address ?? '-' }}</div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs text-gray-500">RT / RW</div>
                            <div class="font-medium text-gray-900">
                                {{ ($selectedUser->rt ?? '-') . ' / ' . ($selectedUser->rw ?? '-') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Kelurahan</div>
                            <div class="font-medium text-gray-900">{{ $selectedUser->kelurahan ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Kecamatan</div>
                            <div class="font-medium text-gray-900">{{ $selectedUser->kecamatan ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs text-gray-500">Provinsi</div>
                            <div class="font-medium text-gray-900">{{ $selectedUser->province ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Agama</div>
                            <div class="font-medium text-gray-900">{{ $selectedUser->religion ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Status Perkawinan</div>
                            <div class="font-medium text-gray-900">{{ $selectedUser->marital_status ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Create / Edit Admin Modal (polished) --}}
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $showEditModal ? 'Edit Admin' : 'Tambah Admin Baru' }}
                        </h3>
                        <p class="text-sm text-white/90">
                            {{ $showEditModal ? 'Perbarui informasi admin dengan hati-hati' : 'Lengkapi formulir untuk menambah admin baru' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click.prevent="closeModal"
                            class="text-white/90 hover:text-white p-2 rounded-md" aria-label="Tutup modal">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal body (form) -->
                <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
                    <form wire:submit.prevent="saveUser" class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Main form fields (2-column layout) -->
                            <div class="lg:col-span-2 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-medium text-gray-700">Nama Lengkap <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="name" placeholder="Nama lengkap"
                                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                            class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-gray-700">Email <span
                                                class="text-red-500">*</span></label>
                                        <input type="email" wire:model="email" placeholder="email@contoh.com"
                                            class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                        @error('email') <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-gray-700">No. HP <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="phone" placeholder="08xxxxxxxxxx"
                                            maxlength="13"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)" inputmode="numeric"
                                            class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                        @error('phone') <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-gray-700">Password 
                                            @if($showEditModal)
                                                <span class="text-gray-500 text-xs">(kosongkan jika tidak ingin mengubah)</span>
                                            @else
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="password" wire:model="password" 
                                            placeholder="{{ $showEditModal ? 'Isi untuk mengubah password' : 'Minimal 8 karakter' }}"
                                            class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                                        @error('password') <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-gray-700">Verifikasi <span class="text-red-500">*</span></label>
                                        <select wire:model="verified"
                                            class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            <option value="1">Terverifikasi</option>
                                            <option value="0">Belum</option>
                                        </select>
                                        @error('verified') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="space-y-3 border-t pt-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs font-medium text-gray-700">NIK (16 digit) <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="nik" maxlength="16"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)" inputmode="numeric"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono" />
                                            @error('nik') <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                            <select wire:model="gender"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                <option value="">-- Pilih Jenis Kelamin --</option>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                            @error('gender') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="place_of_birth" placeholder="Hanya huruf"
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            @error('place_of_birth') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                            <input type="date" wire:model="date_of_birth" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            @error('date_of_birth') <div class="text-sm text-red-600 mt-1">{{ $message }}
                                            </div> @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Kota Domisili <span class="text-red-500">*</span></label>
                                            <select wire:model="city_id"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                <option value="">-- Pilih Kota --</option>
                                                @foreach($cities as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('city_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Agama</label>
                                            <select wire:model="religion"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                <option value="">-- Pilih Agama --</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Kristen">Kristen</option>
                                                <option value="Katolik">Katolik</option>
                                                <option value="Hindu">Hindu</option>
                                                <option value="Buddha">Buddha</option>
                                                <option value="Khonghucu">Khonghucu</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Pekerjaan</label>
                                            <input type="text" wire:model="occupation" placeholder="Contoh: Admin Operasional"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Status Perkawinan</label>
                                            <select wire:model="marital_status"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Belum Kawin">Belum Kawin</option>
                                                <option value="Kawin">Kawin</option>
                                                <option value="Cerai Hidup">Cerai Hidup</option>
                                                <option value="Cerai Mati">Cerai Mati</option>
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-gray-700">RT</label>
                                                <input type="text" wire:model="rt" placeholder="001" maxlength="3"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" inputmode="numeric"
                                                    class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700">RW</label>
                                                <input type="text" wire:model="rw" placeholder="002" maxlength="3"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" inputmode="numeric"
                                                    class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Kelurahan / Desa <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="kelurahan" placeholder="Nama Kelurahan"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            @error('kelurahan') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="kecamatan" placeholder="Nama Kecamatan"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            @error('kecamatan') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="province" placeholder="Nama Provinsi"
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" />
                                            @error('province') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Multiple Cities for Admin -->
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-medium text-gray-700 mb-2 block">
                                                <svg class="w-4 h-4 inline mr-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Kota yang Dikelola Admin (Checklist)
                                            </label>
                                            <div class="mt-2 p-4 bg-gray-50 border border-gray-200 rounded-xl max-h-64 overflow-y-auto">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @forelse($availableCities as $c)
                                                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-primary-400 hover:bg-primary-50 transition-all cursor-pointer">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model="managed_city_ids" 
                                                                value="{{ $c->id }}"
                                                                class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-2 focus:ring-primary-500 cursor-pointer"
                                                            />
                                                            <div class="flex-1">
                                                                <div class="text-sm font-semibold text-gray-900">{{ $c->name }}</div>
                                                                <div class="text-xs text-gray-500">{{ $c->province ?? 'Indonesia' }}</div>
                                                            </div>
                                                        </label>
                                                    @empty
                                                        <div class="col-span-2 text-center text-sm text-gray-500 py-4">
                                                             Belum ada kota tersedia atau semua kota sudah dikelola oleh admin lain
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                                Admin dapat mengelola lebih dari satu kota. Pilih kota yang ingin dikelola oleh admin ini.
                                            </p>
                                            @error('managed_city_ids') 
                                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-xs font-medium text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                                            <textarea wire:model="address" rows="3" placeholder="Jl. Merdeka No. 123..."
                                                class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                                            @error('address') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4 flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-red-800">Gagal menyimpan</h4>
                                    <p class="text-xs text-red-700 mt-0.5">Silakan periksa kembali isian formulir yang masih kosong atau salah (scroll ke atas).</p>
                                </div>
                            </div>
                        @endif

                        <!-- Footer Actions inside form so submit works with enter -->
                        <div class="pt-2 border-t flex items-center justify-end gap-3">
                            <button type="button" wire:click.prevent="closeModal"
                                class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg flex items-center gap-2 text-sm"
                                wire:loading.attr="disabled">
                                <svg wire:loading class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                        fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>
                                </svg>
                                <span>{{ $showEditModal ? 'Perbarui Admin' : 'Simpan Admin' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Delete Modal --}}
    @if($showConfirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-md p-6">
                <h3 class="text-lg font-semibold mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-600 mb-4">Anda yakin ingin menghapus admin ini? Aksi ini tidak dapat dibatalkan.
                </p>
                <div class="text-right">
                    <button type="button" wire:click.prevent="closeModal"
                        class="px-4 py-2 mr-2 bg-gray-100 rounded-lg">Batal</button>
                    <button wire:click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded-lg">Hapus</button>
                </div>
            </div>
        </div>
    @endif

</div>
