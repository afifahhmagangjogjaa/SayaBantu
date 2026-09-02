<?php

use App\Models\Registration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.guest')] class extends Component {
    use WithFileUploads;

    public $ktp_photo;
    public $preview_url = null;

    public function mount()
    {
        // Cek apakah step 1 sudah selesai (diperiksa via registration_uuid)
        $uuid = Session::get('registration_uuid');
        if (!$uuid) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        $registration = Registration::where('uuid', $uuid)->first();
        if (!$registration) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        if ($registration->ktp_photo_path) {
            $this->preview_url = Storage::url($registration->ktp_photo_path);
        }
    }

    public function updatedKtpPhoto()
    {
        $this->validate([
            'ktp_photo' => 'image|max:2048', // 2MB Max
        ]);

        $this->preview_url = $this->ktp_photo->temporaryUrl();
    }

    public function removePhoto(): void
    {
        $this->ktp_photo = null;
        $this->preview_url = null;

        $uuid = Session::get('registration_uuid');
        if ($uuid) {
            $registration = Registration::where('uuid', $uuid)->first();
            if ($registration && $registration->ktp_photo_path) {
                Storage::disk('public')->delete($registration->ktp_photo_path);
                $registration->update(['ktp_photo_path' => null]);
            }
        }
    }

    public function nextStep(): void
    {
        $uuid = Session::get('registration_uuid');
        $registration = Registration::where('uuid', $uuid)->first();

        if ($this->ktp_photo) {
            $this->validate([
                'ktp_photo' => ['image', 'max:2048'],
            ]);
            $path = $this->ktp_photo->store('ktp-photos', 'public');
            if ($registration) {
                $registration->update([
                    'ktp_photo_path' => $path,
                    'status' => 'in_progress',
                ]);
            }
        } elseif (!$registration || !$registration->ktp_photo_path) {
            $this->validate([
                'ktp_photo' => ['required', 'image', 'max:2048'],
            ]);
        }

        $this->redirect(route('register.step3'), navigate: true);
    }

    public function previousStep(): void
    {
        $this->redirect(route('register.step1'), navigate: true);
    }
}; ?>

<div class="flex-1 flex flex-col justify-between">
    <form wire:submit="nextStep" class="flex-1 flex flex-col justify-between">
        <div>
            <!-- Header Step 2 -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Upload KTP</h2>

                <!-- Step Progress Bar -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                </div>

                <!-- Sub Row: Left hint & Right Step indicator -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Upload foto fisik KTP asli Anda</span>
                    <span class="font-bold text-blue-600">Langkah 2 dari 4</span>
                </div>
            </div>

            <!-- Upload Area & Examples -->
            <div class="space-y-4">
                <div>
                    @if ($preview_url)
                        <!-- Preview Image -->
                        <div class="relative bg-white rounded-2xl overflow-hidden shadow-md border-2 border-blue-500">
                            <img src="{{ $preview_url }}" alt="Preview KTP" class="w-full h-auto max-h-56 object-contain bg-gray-900">
                            <div class="absolute bottom-2.5 left-3 bg-gray-900/80 backdrop-blur-xs text-white text-[11px] font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                Foto KTP Berhasil Dipilih
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
                        <label for="ktp_photo" class="block cursor-pointer">
                            <div class="bg-gradient-to-b from-blue-50/40 to-gray-50/70 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/60 transition py-7 px-5 text-center shadow-2xs group">
                                <div class="mx-auto w-16 h-16 bg-blue-100/90 text-blue-600 rounded-full flex items-center justify-center mb-3 shadow-2xs group-hover:scale-105 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 mb-1">Ambil atau Pilih Foto KTP</h3>
                                <p class="text-xs text-gray-500 mb-3.5">Format JPG, PNG (Maksimal 5MB)</p>
                                <div class="inline-flex items-center gap-1.5 bg-[#0098e7] hover:bg-[#0086cc] text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-xs transition active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Pilih Foto KTP</span>
                                </div>
                            </div>
                            <input wire:model="ktp_photo" id="ktp_photo" type="file" accept="image/*" class="hidden">
                        </label>
                    @endif

                    <x-input-error :messages="$errors->get('ktp_photo')" class="mt-2" />

                    @if ($ktp_photo && !$preview_url)
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

                <!-- Tips Box -->
                <div class="bg-blue-50/80 border border-blue-200/80 rounded-2xl p-3.5 text-blue-900">
                    <h4 class="text-xs font-bold text-blue-950 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Tips Foto KTP:
                    </h4>
                    <ul class="text-[11px] text-blue-900/90 space-y-1 pl-5 list-disc leading-relaxed">
                        <li>Pastikan foto fisik KTP terlihat utuh dan tidak terpotong</li>
                        <li>Semua tulisan (NIK, Nama, Alamat) terbaca jelas</li>
                        <li>Hindari pantulan lampu atau bayangan gelap</li>
                    </ul>
                </div>

                <!-- Contoh Foto KTP yang Baik -->
                <div class="bg-gray-50/70 border border-gray-200/80 rounded-2xl p-3.5 shadow-2xs">
                    <p class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Contoh Foto KTP yang Baik:
                    </p>
                    <div class="bg-white rounded-xl p-2.5 border-2 border-green-500 shadow-2xs">
                        <img src="{{ asset('images/contoh-ktp.jpg') }}" alt="Contoh Foto KTP" class="w-full max-h-40 object-contain rounded-lg mx-auto">
                        <p class="text-[11px] text-green-700 font-bold mt-1.5 text-center">✓ Posisi Lurus, Terang & Tulisan Terbaca Jelas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Button (Docked cleanly at bottom) -->
        <div class="mt-auto pt-5 pb-2">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50 disabled:cursor-not-allowed"
                style="background-color: #0098e7;">
                <span wire:loading.remove>Lanjutkan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>