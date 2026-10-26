<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

/**
 * Logika unggah/hapus foto profil yang dipakai bersama oleh Customer dan
 * Mitra. Kelas pemakai wajib mengimplementasikan profileRouteName() dan
 * menyediakan render().
 */
trait UpdatesProfilePhoto
{
    use WithFileUploads;

    public $photo = null;

    public $showModal = false;

    protected $rules = [
        'photo' => 'required|image|max:2048', // max 2MB
    ];

    protected $messages = [
        'photo.required' => 'Pilih foto terlebih dahulu',
        'photo.image' => 'File harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 2MB',
    ];

    /**
     * Nama route halaman profil yang dituju setelah aksi berhasil.
     */
    abstract protected function profileRouteName(): string;

    #[On('openModal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->photo = null;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->photo = null;
        $this->resetErrorBag();
    }

    public function updatedPhoto()
    {
        $this->resetErrorBag('photo');
    }

    public function updatePhoto()
    {
        $this->validate();

        try {
            $user = auth()->user();

            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $this->photo->store('profile-photos', 'public');

            $user->update([
                'profile_photo' => $path,
            ]);

            session()->flash('status', 'Foto profil berhasil diperbarui!');

            $this->closeModal();
            $this->dispatch('profile-photo-updated');

            return redirect()->route($this->profileRouteName());
        } catch (\Exception $e) {
            Log::error('Error updating profile photo: '.$e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengupload foto.');
        }
    }

    #[On('removePhoto')]
    public function removePhoto()
    {
        try {
            $user = auth()->user();

            // Delete photo file
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Kosongkan kolom yang menampung foto profil. Kolom selfie_photo
            // tidak boleh disentuh karena dipakai untuk verifikasi KTP.
            $user->update([
                'profile_photo' => null,
            ]);

            session()->flash('status', 'Foto profil berhasil dihapus!');

            return redirect()->route($this->profileRouteName());
        } catch (\Exception $e) {
            Log::error('Error removing profile photo: '.$e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus foto.');
        }
    }
}
