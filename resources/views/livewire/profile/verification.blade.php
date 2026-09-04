<div>
    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-xl border border-red-200 bg-red-50 text-red-700 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <!-- KTP Upload -->
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">Foto KTP</label>
            <label for="ktp-upload" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-[#0098e7] hover:bg-blue-50 transition bg-gray-50 relative overflow-hidden group cursor-pointer">
                <div class="space-y-1 text-center relative z-10">
                    @if ($ktp_photo)
                        <img src="{{ $ktp_photo->temporaryUrl() }}" alt="KTP Preview" class="mx-auto h-32 object-contain rounded mb-3">
                    @elseif ($existing_ktp)
                        <img src="{{ Storage::url($existing_ktp) }}" alt="KTP Saat Ini" class="mx-auto h-32 object-contain rounded mb-3">
                        <div class="absolute top-0 right-0 bg-green-500 text-white text-xs px-2 py-1 rounded-bl-lg rounded-tr-lg font-bold">
                            Terunggah
                        </div>
                    @else
                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-[#0098e7] transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif
                    <div class="flex text-sm text-gray-600 justify-center">
                        <span class="font-medium text-[#0098e7] group-hover:text-[#0077cc]">Pilih file gambar</span>
                        <input id="ktp-upload" wire:model="ktp_photo" type="file" class="sr-only" accept="image/*">
                        <p class="pl-1">atau drag & drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                </div>
                
                <div wire:loading wire:target="ktp_photo" class="absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                    <svg class="animate-spin h-8 w-8 text-[#0098e7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </label>
            @error('ktp_photo') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Selfie Upload -->
        <div>
            <label class="block text-sm font-bold text-gray-900 mb-2">Foto Selfie (Wajah & KTP)</label>
            <label for="selfie-upload" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-[#0098e7] hover:bg-blue-50 transition bg-gray-50 relative overflow-hidden group cursor-pointer">
                <div class="space-y-1 text-center relative z-10">
                    @if ($selfie_photo)
                        <img src="{{ $selfie_photo->temporaryUrl() }}" alt="Selfie Preview" class="mx-auto h-32 object-contain rounded mb-3">
                    @elseif ($existing_selfie)
                        <img src="{{ Storage::url($existing_selfie) }}" alt="Selfie Saat Ini" class="mx-auto h-32 object-contain rounded mb-3">
                        <div class="absolute top-0 right-0 bg-green-500 text-white text-xs px-2 py-1 rounded-bl-lg rounded-tr-lg font-bold">
                            Terunggah
                        </div>
                    @else
                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-[#0098e7] transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif
                    <div class="flex text-sm text-gray-600 justify-center">
                        <span class="font-medium text-[#0098e7] group-hover:text-[#0077cc]">Pilih file gambar</span>
                        <input id="selfie-upload" wire:model="selfie_photo" type="file" class="sr-only" accept="image/*">
                        <p class="pl-1">atau drag & drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                </div>
                
                <div wire:loading wire:target="selfie_photo" class="absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                    <svg class="animate-spin h-8 w-8 text-[#0098e7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </label>
            @error('selfie_photo') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-[#0098e7] to-[#0060b0] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0098e7] transition" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Simpan Data</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </form>
</div>
