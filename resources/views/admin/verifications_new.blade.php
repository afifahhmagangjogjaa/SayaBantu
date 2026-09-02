<div>
    <div class="px-8 py-6">
        <h1 class="text-2xl font-bold">Verifikasi KTP Mitra (Sederhana)</h1>
    </div>

    <div class="p-6">
        <div class="mb-4 flex items-center space-x-3">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." class="px-3 py-2 border rounded" />
            <select wire:model="perPage" class="px-3 py-2 border rounded">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="bg-white rounded shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-center w-12">No</th>
                        <th class="p-3 text-left">Nama</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $r)
                        <tr class="border-t">
                            <td class="p-3 text-center text-gray-500">{{ $registrations->firstItem() + $loop->index }}</td>
                            <td class="p-3">
                                <div>{{ $r->full_name }}</div>
                                <div class="text-xs text-gray-400 font-mono">ID: #{{ $r->id }}</div>
                            </td>
                            <td class="p-3">{{ $r->email }}</td>
                            <td class="p-3">{{ ucfirst($r->status ?? 'pending') }}</td>
                            <td class="p-3 text-center">
                                <button type="button" wire:click="view({{ $r->id }})" class="px-3 py-1 bg-blue-600 text-white rounded">Lihat</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $registrations->links() }}</div>
    </div>

    @if($showModal && $selectedId)
        @php $reg = \App\Models\Registration::find($selectedId); @endphp
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded p-6 w-full max-w-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Detail Registrasi</h3>
                    <button type="button" wire:click="close" class="text-gray-600">Tutup</button>
                </div>
                @if($reg)
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong>Nama:</strong> {{ $reg->full_name }}</div>
                        <div><strong>Email:</strong> {{ $reg->email }}</div>
                        <div><strong>NIK:</strong> {{ $reg->nik }}</div>
                        <div><strong>Status:</strong> {{ $reg->status }}</div>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button type="button" wire:click="approve({{ $reg->id }})" class="px-4 py-2 bg-green-600 text-white rounded">Verifikasi</button>
                        <button type="button" wire:click="reject({{ $reg->id }})" class="px-4 py-2 bg-red-600 text-white rounded">Tolak</button>
                    </div>
                @else
                    <div class="text-gray-500">Registrasi tidak ditemukan.</div>
                @endif
            </div>
        </div>
    @endif

    @if($previewPhoto)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded">
                <button type="button" wire:click="closePreview" class="mb-2">Tutup</button>
                <img src="{{ $previewPhoto }}" alt="Preview" class="max-w-[80vw] max-h-[80vh]" />
            </div>
        </div>
    @endif
</div>
