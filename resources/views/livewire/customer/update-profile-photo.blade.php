<div>
    <!-- Upload Photo Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl transform transition-all">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Upload Foto Profil</h3>
                    <button wire:click="closeModal" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Upload Area -->
                <div class="mb-6" x-data="{ previewUrl: null }">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Foto Profil</label>

                    <div class="relative">
                        <input type="file" 
                               accept="image/*" 
                               class="hidden" 
                               id="photoInputCustomer"
                               x-on:change="
                                   if ($event.target.files[0]) {
                                       previewUrl = URL.createObjectURL($event.target.files[0]);
                                       $wire.upload('photo', $event.target.files[0], function() {}, function() {}, function() {});
                                   }
                               ">

                        <label for="photoInputCustomer"
                            class="relative flex flex-col items-center justify-center w-full h-52 border-2 border-dashed border-gray-300 hover:border-primary-500 rounded-2xl cursor-pointer hover:bg-gray-50/80 transition overflow-hidden group">
                            
                            <template x-if="previewUrl">
                                <div class="relative w-full h-full">
                                    <img :src="previewUrl" class="w-full h-full object-cover rounded-2xl" alt="Preview">
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="px-3.5 py-1.5 bg-white/90 backdrop-blur-xs text-gray-800 font-semibold text-xs rounded-lg shadow-sm">
                                            Klik untuk Ganti Foto
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!previewUrl">
                                <div class="w-full h-full flex items-center justify-center">
                                    @if ($photo && method_exists($photo, 'temporaryUrl'))
                                        @try
                                            <div class="relative w-full h-full">
                                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover rounded-2xl" alt="Preview">
                                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <span class="px-3.5 py-1.5 bg-white/90 backdrop-blur-xs text-gray-800 font-semibold text-xs rounded-lg shadow-sm">
                                                        Klik untuk Ganti Foto
                                                    </span>
                                                </div>
                                            </div>
                                        @catch (\Throwable $e)
                                            <div class="text-center p-4">
                                                <div class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-gray-700 font-bold">Klik untuk memilih foto</p>
                                                <p class="text-xs text-gray-400 mt-1">PNG, JPG hingga 2MB</p>
                                            </div>
                                        @endtry
                                    @else
                                        <div class="text-center p-4">
                                            <div class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mx-auto mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-700 font-bold">Klik untuk memilih foto</p>
                                            <p class="text-xs text-gray-400 mt-1">PNG, JPG hingga 2MB</p>
                                        </div>
                                    @endif
                                </div>
                            </template>
                        </label>
                    </div>

                    @error('photo')
                        <div class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror

                    <div wire:loading wire:target="photo" class="mt-2 text-sm text-blue-600 flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Memuat foto...</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button wire:click="closeModal" type="button"
                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="updatePhoto" type="button" wire:loading.attr="disabled" wire:target="updatePhoto"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl font-bold text-white hover:from-primary-700 hover:to-primary-800 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="updatePhoto">Upload</span>
                        <span wire:loading wire:target="updatePhoto" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengupload...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>