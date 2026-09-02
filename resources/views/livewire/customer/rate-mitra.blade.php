<div class="mt-4">
    @if($alreadyRated)
        <div class="text-sm text-green-700 font-semibold">Anda sudah memberikan rating. Terima kasih!</div>
    @else
        <div class="space-y-3">
            <label class="text-sm text-gray-600">Beri Rating:</label>

            <div class="flex items-center gap-3">
                @for($i=1;$i<=5;$i++)
                    <button type="button" wire:click.prevent="setRating({{ $i }})" class="flex items-center justify-center w-9 h-9 rounded-full transition-transform transform hover:scale-110 focus:outline-none"
                        aria-label="Beri {{ $i }} bintang">
                        @if($rating >= $i)
                            <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endif
                    </button>
                @endfor
            </div>

            <div class="mt-3">
                <textarea wire:model="review" rows="4" class="w-full rounded-lg border border-gray-200 p-3 text-sm" placeholder="Cerita pengalaman Anda... (opsional)"></textarea>
                <div class="text-xs text-gray-400 mt-1">Maksimal 500 karakter</div>
            </div>

            <div class="mt-3 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" wire:model="is_anonymous" class="w-4 h-4 text-amber-500 rounded border-gray-300 focus:ring-amber-400">
                    <span class="text-xs text-gray-700 font-medium">Kirim sebagai Anonim (Sembunyikan nama saya)</span>
                </label>
                <p class="text-[11px] text-gray-400 mt-0.5 ml-6">Nama Anda tidak akan ditampilkan kepada mitra.</p>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <button type="button" wire:click="resetForm" onclick="if(document.currentScript){};" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">Reset</button>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('rate-modal-{{ $help->id }}').classList.add('hidden'); $wire.resetForm();" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">Batal</button>
                    <button wire:click="submitRating" class="px-4 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-white font-semibold">Kirim Rating</button>
                </div>
            </div>
        </div>
    @endif
</div>
