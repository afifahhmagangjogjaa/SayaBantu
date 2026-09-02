<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Registration;
use App\Models\City;

class Verification extends Component
{
    use WithFileUploads;

    public $ktp_photo;
    public $selfie_photo;
    
    public $existing_ktp;
    public $existing_selfie;

    public function mount()
    {
        $user = Auth::user();
        $this->existing_ktp = $user->ktp_photo;
        $this->existing_selfie = $user->selfie_photo;
    }

    public function save()
    {
        $this->validate([
            'ktp_photo' => 'nullable|image|max:2048', // 2MB Max
            'selfie_photo' => 'nullable|image|max:2048', // 2MB Max
        ], [
            'ktp_photo.image' => 'File KTP harus berupa gambar.',
            'ktp_photo.max' => 'Ukuran gambar KTP maksimal 2MB.',
            'selfie_photo.image' => 'File Selfie harus berupa gambar.',
            'selfie_photo.max' => 'Ukuran gambar Selfie maksimal 2MB.',
        ]);

        $user = Auth::user();
        $changes = 0;

        if ($this->ktp_photo) {
            // Hapus yang lama jika ada
            if ($user->ktp_photo && Storage::disk('public')->exists($user->ktp_photo)) {
                Storage::disk('public')->delete($user->ktp_photo);
            }
            $path = $this->ktp_photo->store('ktp-photos', 'public');
            $user->ktp_photo = $path;
            $this->existing_ktp = $path;
            $changes++;
        }

        if ($this->selfie_photo) {
            // Hapus yang lama jika ada
            if ($user->selfie_photo && Storage::disk('public')->exists($user->selfie_photo)) {
                Storage::disk('public')->delete($user->selfie_photo);
            }
            $path = $this->selfie_photo->store('selfie-photos', 'public');
            $user->selfie_photo = $path;
            $this->existing_selfie = $path;
            $changes++;
        }

        if ($changes > 0) {
            $user->save();

            // Sinkronkan ke tabel registrations agar muncul di admin verifikasi KTP
            $this->syncToRegistrations($user);

            session()->flash('message', 'Data verifikasi berhasil diperbarui.');
            
            if (Auth::user()->role === 'mitra') {
                return $this->redirectRoute('mitra.profile.edit', navigate: true);
            }
            return $this->redirectRoute('profile.settings', navigate: true);
        } else {
            // Jika kedua file sudah terunggah sebelumnya, dan user tidak memilih file baru
            if ($user->ktp_photo && $user->selfie_photo) {
                // Pastikan tetap tersinkron jika belum pernah dibuat di registrations
                $this->syncToRegistrations($user);

                if (Auth::user()->role === 'mitra') {
                    return $this->redirectRoute('mitra.profile.edit', navigate: true);
                }
                return $this->redirectRoute('profile.settings', navigate: true);
            }

            session()->flash('error', 'Pilih minimal satu file (KTP atau Selfie) yang ingin diunggah sebelum menyimpan.');
        }
    }

    /**
     * Buat atau update record di tabel registrations berdasarkan data user.
     * Sehingga data muncul di halaman Verifikasi KTP admin.
     */
    protected function syncToRegistrations($user)
    {
        $existing = Registration::where('email', $user->email)->first();

        // Cari nama kota jika user->city kosong tapi city_id terisi
        $cityName = $user->city;
        if (empty($cityName) && !empty($user->city_id)) {
            $cityObj = City::find($user->city_id);
            $cityName = $cityObj ? $cityObj->name : null;
        }

        $role = $user->role;
        if ($role === 'kustomer' || $role === 'customer') {
            $role = 'customer';
        } else {
            $role = 'mitra';
        }

        if ($existing) {
            $existing->update([
                'role'             => $role,
                'ktp_photo_path'   => $user->ktp_photo   ?? $existing->ktp_photo_path,
                'selfie_photo_path' => $user->selfie_photo ?? $existing->selfie_photo_path,
                'city_id'          => $user->city_id     ?? $existing->city_id,
                'city'             => $cityName          ?? $existing->city,
                'status'           => 'pending_verification',
            ]);
        } else {
            Registration::create([
                'uuid'              => (string) Str::uuid(),
                'role'              => $role,
                'full_name'         => $user->name,
                'email'             => $user->email,
                'nik'               => $user->nik,
                'place_of_birth'    => $user->place_of_birth,
                'date_of_birth'     => $user->date_of_birth,
                'gender'            => $user->gender,
                'address'           => $user->address,
                'rt'                => $user->rt,
                'rw'                => $user->rw,
                'kelurahan'         => $user->kelurahan,
                'kecamatan'         => $user->kecamatan,
                'city'              => $cityName,
                'city_id'           => $user->city_id,
                'province'          => $user->province,
                'religion'          => $user->religion,
                'marital_status'    => $user->marital_status,
                'occupation'        => $user->occupation,
                'ktp_photo_path'    => $user->ktp_photo,
                'selfie_photo_path' => $user->selfie_photo,
                'status'            => 'pending_verification',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.profile.verification')->layout('layouts.app');
    }
}
