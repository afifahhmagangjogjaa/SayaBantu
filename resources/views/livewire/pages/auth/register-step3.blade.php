<?php

use App\Models\Registration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.guest')] class extends Component {
    use WithFileUploads;

    public $selfie_photo;
    public $preview_url = null;

    public function mount()
    {
        // Cek apakah step 1 dan 2 sudah selesai (via registration record)
        $uuid = Session::get('registration_uuid');
        if (!$uuid) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        $registration = Registration::where('uuid', $uuid)->first();
        if (!$registration || !$registration->ktp_photo_path) {
            // KTP belum diupload, kembali ke step1
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        if ($registration->selfie_photo_path) {
            $this->preview_url = Storage::url($registration->selfie_photo_path);
        }
    }

    public function updatedSelfiePhoto()
    {
        $this->validate([
            'selfie_photo' => 'image|max:2048', // 2MB Max
        ]);

        $this->preview_url = $this->selfie_photo->temporaryUrl();
    }

    public function removePhoto(): void
    {
        $this->selfie_photo = null;
        $this->preview_url = null;

        $uuid = Session::get('registration_uuid');
        if ($uuid) {
            $registration = Registration::where('uuid', $uuid)->first();
            if ($registration && $registration->selfie_photo_path) {
                Storage::disk('public')->delete($registration->selfie_photo_path);
                $registration->update(['selfie_photo_path' => null]);
            }
        }
    }

    public function nextStep(): void
    {
        $uuid = Session::get('registration_uuid');
        $registration = Registration::where('uuid', $uuid)->first();

        if ($this->selfie_photo) {
            $this->validate([
                'selfie_photo' => ['image', 'max:2048'],
            ]);
            $path = $this->selfie_photo->store('selfie-photos', 'public');
            if ($registration) {
                $registration->update([
                    'selfie_photo_path' => $path,
                    'status' => 'in_progress',
                ]);
            }
        } elseif (!$registration || !$registration->selfie_photo_path) {
            $this->validate([
                'selfie_photo' => ['required', 'image', 'max:2048'],
            ]);
        }

        $this->redirect(route('register.step4'), navigate: true);
    }

    public function previousStep(): void
    {
        $this->redirect(route('register.step2'), navigate: true);
    }
}; ?>

<div class="flex-1 flex flex-col justify-between">
    <form wire:submit="nextStep" class="flex-1 flex flex-col justify-between">
        <div>
            <!-- Header Step 3 -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Foto Selfie + KTP</h2>

                <!-- Step Progress Bar -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                </div>

                <!-- Sub Row: Left hint & Right Step indicator -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Upload foto selfie sambil memegang KTP</span>
                    <span class="font-bold text-blue-600">Langkah 3 dari 4</span>
                </div>
            </div>

            <!-- Upload Area -->
            <div class="space-y-5">
                <div>
                    @if ($preview_url)
                        <!-- Preview Image -->
                        <div class="relative bg-white rounded-2xl overflow-hidden shadow-md border-2 border-blue-500">
                            <img src="{{ $preview_url }}" alt="Preview Selfie" class="w-full h-auto max-h-56 object-contain bg-gray-900">
                            <div class="absolute bottom-2.5 left-3 bg-gray-900/80 backdrop-blur-xs text-white text-[11px] font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                Foto Selfie Berhasil Dipilih
                            </div>
                            <button type="button" wire:click="removePhoto"
                                class="absolute top-3 right-3 bg-red-500 text-white rounded-full p-2 shadow-lg hover:bg-red-600 transition active:scale-95 cursor-pointer z-10" title="Hapus / Ganti Foto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <!-- Upload Placeholder -->
                        <label for="selfie_photo" class="block cursor-pointer">
                            <div class="bg-gradient-to-b from-blue-50/40 to-gray-50/70 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/60 transition py-7 px-5 text-center shadow-2xs group">
                                <div class="mx-auto w-16 h-16 bg-blue-100/90 text-blue-600 rounded-full flex items-center justify-center mb-3 shadow-2xs group-hover:scale-105 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 mb-1">Ambil atau Pilih Foto Selfie</h3>
                                <p class="text-xs text-gray-500 mb-3.5">Pegang KTP di samping wajah dengan jelas</p>
                                <div class="inline-flex items-center gap-1.5 bg-[#0098e7] hover:bg-[#0086cc] text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-xs transition active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>
                                    <span>Ambil Foto Selfie</span>
                                </div>
                            </div>
                            <input wire:model="selfie_photo" id="selfie_photo" type="file" accept="image/*" capture="user" class="hidden">
                        </label>
                    @endif

                    <x-input-error :messages="$errors->get('selfie_photo')" class="mt-2" />

                    @if ($selfie_photo && !$preview_url)
                        <div class="mt-3 text-center">
                            <div class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang memproses foto...
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Important Info Box -->
                <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3.5 text-amber-900">
                    <h4 class="text-xs font-bold text-amber-950 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Penting:
                    </h4>
                    <ul class="text-[11px] text-amber-900/90 space-y-1 pl-5 list-disc leading-relaxed">
                        <li>Wajah Anda dan fisik KTP harus terlihat utuh & jelas</li>
                        <li>Pegang KTP di samping wajah tanpa menutupi bagian muka</li>
                        <li>Gunakan pencahayaan yang cukup dan jangan gunakan filter</li>
                    </ul>
                </div>

                <!-- Contoh Selfie Benar & Salah -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Good Example -->
                    <div class="bg-white rounded-2xl p-3 border-2 border-green-500 shadow-2xs flex flex-col justify-between">
                        <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                            <img src="{{ asset('images/contoh-selfie-benar.jpg') }}" alt="Contoh Selfie Benar" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <p class="text-xs text-green-700 font-bold text-center">✓ Benar</p>
                        <p class="text-[11px] text-gray-500 text-center mt-0.5">Wajah & KTP jelas</p>
                    </div>

                    <!-- Bad Example -->
                    <div class="bg-white rounded-2xl p-3 border-2 border-red-500 shadow-2xs flex flex-col justify-between">
                        <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                            <img src="{{ asset('images/contoh-selfie-salah.jpg') }}" alt="Contoh Selfie Salah" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <p class="text-xs text-red-700 font-bold text-center">✗ Salah</p>
                        <p class="text-[11px] text-gray-500 text-center mt-0.5">Blur atau gelap</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Button (Docked cleanly at bottom) -->
        <div class="mt-auto pt-6 pb-2">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50 disabled:cursor-not-allowed"
                style="background-color: #0098e7;">
                <span wire:loading.remove>Lanjutkan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>