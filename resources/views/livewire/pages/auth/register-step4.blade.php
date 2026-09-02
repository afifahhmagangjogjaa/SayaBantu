<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\City;
use App\Notifications\NewRegistrationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public $step1_data;
    public $step2_data;
    public $step3_data;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree_terms = false;

    public function mount()
    {
        // Cek apakah registration UUID ada
        $uuid = Session::get('registration_uuid');
        if (!$uuid) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        $registration = Registration::where('uuid', $uuid)->first();
        if (!$registration || !$registration->ktp_photo_path || !$registration->selfie_photo_path) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        // Load step data from registration record
        $this->step1_data = $registration->only([
            'nik',
            'full_name',
            'place_of_birth',
            'date_of_birth',
            'gender',
            'address',
            'rt',
            'rw',
            'kelurahan',
            'kecamatan',
            'city',
            'province',
            'religion',
            'marital_status',
            'occupation'
        ]);

        $this->step2_data = ['ktp_photo_path' => $registration->ktp_photo_path];
        $this->step3_data = ['selfie_photo_path' => $registration->selfie_photo_path];
        // Prefill email if user previously entered it
        $this->email = $registration->email ?? $this->email;
    }



    public function complete(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree_terms' => ['accepted'],
        ]);

        // Ambil registration
        $uuid = Session::get('registration_uuid');
        $registration = Registration::where('uuid', $uuid)->first();
        if (!$registration) {
            $this->redirect(route('register.step1'), navigate: true);
            return;
        }

        // Gabungkan semua data
        $userData = [
            'name' => $registration->full_name,
            'email' => $validated['email'],
            'role' => $registration->role ?? 'customer',
            'password' => Hash::make($validated['password']),
            'nik' => $registration->nik,
            'place_of_birth' => $registration->place_of_birth,
            'date_of_birth' => $registration->date_of_birth,
            'gender' => $registration->gender,
            'address' => $registration->address,
            'rt' => $registration->rt,
            'rw' => $registration->rw,
            'kelurahan' => $registration->kelurahan,
            'kecamatan' => $registration->kecamatan,
            'city' => $registration->city,
            'province' => $registration->province,
            'religion' => $registration->religion,
            'marital_status' => $registration->marital_status,
            'occupation' => $registration->occupation,
            'ktp_photo' => $registration->ktp_photo_path,
            'selfie_photo' => $registration->selfie_photo_path,
        ];

        // Buat user baru tetapi jangan login — akun perlu verifikasi admin
        $userData['status'] = 'inactive';
        $userData['verified'] = false;

        $user = User::create($userData);

        // Jika nama kota pada registration sesuai dengan record di tabel cities,
        // set relasi city_id agar user otomatis terkait dengan admin kota tersebut.
        try {
            if (!empty($registration->city_id)) {
                $user->city_id = $registration->city_id;
                $user->save();
            } elseif (!empty($registration->city)) {
                $city = City::whereRaw('LOWER(name) = ?', [strtolower($registration->city)])->first();
                if ($city) {
                    $user->city_id = $city->id;
                    $user->save();
                }
            }
        } catch (\Exception $e) {
            // jika terjadi error mapping city, jangan ganggu proses pendaftaran
        }

        event(new Registered($user));

        // Notify super admins about new registration
        try {
            $superAdmins = User::where('role', 'super_admin')->where('status', 'active')->get();
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->notify(new NewRegistrationNotification($user));
            }
        } catch (\Throwable $ne) {
            // Ignore notification failure
        }

        // Tandai registration menunggu verifikasi admin
        $registration->update([
            'status' => 'pending_verification',
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Hapus UUID session
        Session::forget('registration_uuid');

        // Clear client-side saved draft for step4 (email)
        $this->dispatch('clear-registration-step4');

        // Redirect to a success/awaiting-verification page
        $this->redirect(route('registration.success'), navigate: true);
    }

    public function previousStep(): void
    {
        $this->redirect(route('register.step3'), navigate: true);
    }

    public function editStep($step): void
    {
        $this->redirect(route("register.step{$step}"), navigate: true);
    }
}; ?>
<div class="w-full px-1">
    <!-- Header Step 4 -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Verifikasi & Buat Akun</h2>

        <!-- Step Progress Bar -->
        <div class="flex items-center gap-1.5 mb-2">
            <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
        </div>

        <!-- Sub Row: Left hint & Right Step indicator -->
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>Periksa data & buat keamanan akun</span>
            <span class="font-bold text-blue-600">Langkah 4 dari 4</span>
        </div>
    </div>

    <form wire:submit="complete" class="space-y-5">
        <!-- 1. Ringkasan Data Pribadi & Alamat -->
        <div class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-gray-200/80">
                <h3 class="font-bold text-xs text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Ringkasan Data Diri
                </h3>
                <button type="button" wire:click="editStep(1)" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-full transition">
                    Ubah
                </button>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">NIK</span>
                    <span class="font-bold text-gray-900 text-right tracking-wide">{{ $step1_data['nik'] }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">Nama Lengkap</span>
                    <span class="font-bold text-gray-900 text-right">{{ $step1_data['full_name'] }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">Tempat, Tgl Lahir</span>
                    <span class="font-bold text-gray-900 text-right">{{ $step1_data['place_of_birth'] }}, {{ date('d/m/Y', strtotime($step1_data['date_of_birth'])) }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">Jenis Kelamin</span>
                    <span class="font-bold text-gray-900 text-right">{{ $step1_data['gender'] }}</span>
                </div>
                <div class="py-1">
                    <span class="text-gray-500 font-medium block mb-1">Alamat Domisili</span>
                    <div class="bg-white rounded-xl p-2.5 border border-gray-200/80 text-gray-800 leading-relaxed font-semibold">
                        <div>{{ $step1_data['address'] }}</div>
                        <div class="text-gray-600 font-normal mt-0.5">
                            RT {{ $step1_data['rt'] }} / RW {{ $step1_data['rw'] }}, Kel. {{ $step1_data['kelurahan'] }}, Kec. {{ $step1_data['kecamatan'] }}
                        </div>
                        <div class="text-gray-600 font-normal">
                            {{ $step1_data['city'] }}, {{ $step1_data['province'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Dokumen Foto (KTP & Selfie) -->
        <div class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-4 shadow-2xs">
            <h3 class="font-bold text-xs text-gray-900 uppercase tracking-wider pb-2.5 mb-3 border-b border-gray-200/80 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
                Foto Dokumen
            </h3>

            <div class="grid grid-cols-2 gap-3">
                <!-- Foto KTP -->
                <div class="bg-white rounded-xl p-2 border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1.5 px-1">
                        <span class="font-bold text-xs text-gray-800">Foto KTP</span>
                        <button type="button" wire:click="editStep(2)" class="text-blue-600 text-[11px] font-bold hover:underline">Ubah</button>
                    </div>
                    <div class="aspect-4/3 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center border border-gray-100">
                        <img src="{{ Storage::url($step2_data['ktp_photo_path']) }}" alt="KTP" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Foto Selfie -->
                <div class="bg-white rounded-xl p-2 border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1.5 px-1">
                        <span class="font-bold text-xs text-gray-800">Selfie + KTP</span>
                        <button type="button" wire:click="editStep(3)" class="text-blue-600 text-[11px] font-bold hover:underline">Ubah</button>
                    </div>
                    <div class="aspect-4/3 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center border border-gray-100">
                        <img src="{{ Storage::url($step3_data['selfie_photo_path']) }}" alt="Selfie" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Form Akun Login -->
        <div class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-4 shadow-2xs space-y-5">
            <h3 class="font-bold text-xs text-gray-900 uppercase tracking-wider pb-2.5 mb-3 border-b border-gray-200/80 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Buat Akun Login
            </h3>

            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Email <span class="text-red-500">*</span>
                </label>
                <input wire:model="email" id="email" type="email" placeholder="nama@email.com"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ show: false }">
                    <input wire:model="password" id="password" x-bind:type="show ? 'text' : 'password'" placeholder="Minimal 8 karakter"
                        class="w-full py-3 pl-4 pr-12 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="show" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ show: false }">
                    <input wire:model="password_confirmation" id="password_confirmation" x-bind:type="show ? 'text' : 'password'" placeholder="Ketik ulang password"
                        class="w-full py-3 pl-4 pr-12 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="show" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <!-- 4. Terms & Conditions -->
        <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input wire:model="agree_terms" type="checkbox" class="w-4 h-4 text-blue-600 rounded mt-0.5 focus:ring-blue-500">
                <span class="text-xs text-gray-700 leading-relaxed flex-1">
                    Saya menyetujui <a href="#" class="text-blue-600 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-blue-600 font-bold hover:underline">Kebijakan Privasi</a> yang berlaku.
                </span>
            </label>
            <x-input-error :messages="$errors->get('agree_terms')" class="mt-1" />
        </div>

        <!-- 5. Complete Button -->
        <div class="pt-5 pb-3">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-green-600 hover:bg-green-700 active:scale-98 text-white font-bold py-3.5 px-4 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50 flex items-center justify-center gap-2">
                <span wire:loading.remove class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Selesaikan Pendaftaran</span>
                </span>
                <span wire:loading class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mendaftarkan akun...</span>
                </span>
            </button>
        </div>
    </form>

    <script>
        (function () {
            const key = 'registration_step4_email';
            const el = document.getElementById('email');
            window.addEventListener('DOMContentLoaded', () => {
                try {
                    const val = localStorage.getItem(key);
                    if (val !== null && el) {
                        el.value = val;
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } catch (e) {}
            });

            if (el) {
                el.addEventListener('input', (ev) => {
                    try { localStorage.setItem(key, ev.target.value); } catch (e) {}
                });
            }

            document.addEventListener('livewire:load', function () {
                if (window.Livewire) {
                    window.Livewire.on('clear-registration-step4', () => {
                        try { localStorage.removeItem(key); } catch (e) {}
                    });
                }
            });
        })();
    </script>
</div>