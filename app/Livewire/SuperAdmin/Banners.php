<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.superadmin', ['title' => 'Pengaturan Banner', 'breadcrumb' => 'Super Admin'])]
class Banners extends Component
{
    use WithFileUploads;

    // store arrays of stored paths
    public $customerBanners = [];
    public $mitraBanners = [];
    public $homeBanners = [];

    // temporary uploaded files (multiple)
    public $customerUploads = [];
    public $mitraUploads = [];
    public $homeUploads = [];

    public function mount()
    {
        $this->customerBanners = json_decode((string) AppSetting::get('banner_customer', '[]'), true) ?: [];
        $this->mitraBanners = json_decode((string) AppSetting::get('banner_mitra', '[]'), true) ?: [];
        $this->homeBanners = json_decode((string) AppSetting::get('banner_home', '[]'), true) ?: [];
    }

    public function saveCustomer()
    {
        $this->validate([
            'customerUploads.*' => 'required|image|max:10240',
        ], [
            'customerUploads.*.image' => 'File harus berupa gambar (JPG, PNG, WebP, dll).',
            'customerUploads.*.max' => 'Ukuran file maksimal 10MB per gambar.',
        ]);

        if (empty($this->customerUploads)) {
            session()->flash('info', 'Pilih file banner customer terlebih dahulu.');
            return;
        }

        try {
            foreach ($this->customerUploads as $f) {
                $path = $f->store('banners', 'public');
                $this->customerBanners[] = $path;
            }
            $this->customerUploads = [];
            AppSetting::set('banner_customer', json_encode(array_values($this->customerBanners)));
            session()->flash('message', 'Banner customer berhasil disimpan.');
            $this->dispatchSliderUpdate();
        } catch (\Exception $e) {
            Log::error('Error saving customer banner: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan banner customer: ' . $e->getMessage());
        }
    }

    public function saveMitra()
    {
        $this->validate([
            'mitraUploads.*' => 'required|image|max:10240',
        ], [
            'mitraUploads.*.image' => 'File harus berupa gambar (JPG, PNG, WebP, dll).',
            'mitraUploads.*.max' => 'Ukuran file maksimal 10MB per gambar.',
        ]);

        if (empty($this->mitraUploads)) {
            session()->flash('info', 'Pilih file banner mitra terlebih dahulu.');
            return;
        }

        try {
            foreach ($this->mitraUploads as $f) {
                $path = $f->store('banners', 'public');
                $this->mitraBanners[] = $path;
            }
            $this->mitraUploads = [];
            AppSetting::set('banner_mitra', json_encode(array_values($this->mitraBanners)));
            session()->flash('message', 'Banner mitra berhasil disimpan.');
            $this->dispatchSliderUpdate();
        } catch (\Exception $e) {
            Log::error('Error saving mitra banner: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan banner mitra: ' . $e->getMessage());
        }
    }

    public function saveHome()
    {
        $this->validate([
            'homeUploads.*' => 'required|image|max:10240',
        ], [
            'homeUploads.*.image' => 'File harus berupa gambar (JPG, PNG, WebP, dll).',
            'homeUploads.*.max' => 'Ukuran file maksimal 10MB per gambar.',
        ]);

        if (empty($this->homeUploads)) {
            session()->flash('info', 'Pilih file banner beranda terlebih dahulu.');
            return;
        }

        try {
            foreach ($this->homeUploads as $f) {
                $path = $f->store('banners', 'public');
                $this->homeBanners[] = $path;
            }
            $this->homeUploads = [];
            AppSetting::set('banner_home', json_encode(array_values($this->homeBanners)));
            session()->flash('message', 'Banner beranda berhasil disimpan.');
            $this->dispatchSliderUpdate();
        } catch (\Exception $e) {
            Log::error('Error saving home banner: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan banner beranda: ' . $e->getMessage());
        }
    }

    public function save()
    {
        $savedAny = false;
        if (!empty($this->customerUploads)) {
            $this->saveCustomer();
            $savedAny = true;
        }
        if (!empty($this->mitraUploads)) {
            $this->saveMitra();
            $savedAny = true;
        }
        if (!empty($this->homeUploads)) {
            $this->saveHome();
            $savedAny = true;
        }

        if (!$savedAny) {
            session()->flash('info', 'Tidak ada file yang dipilih untuk diunggah.');
        }
    }

    public function removeCustomerUpload($index)
    {
        if (isset($this->customerUploads[$index])) {
            unset($this->customerUploads[$index]);
            $this->customerUploads = array_values($this->customerUploads);
        }
    }

    public function removeMitraUpload($index)
    {
        if (isset($this->mitraUploads[$index])) {
            unset($this->mitraUploads[$index]);
            $this->mitraUploads = array_values($this->mitraUploads);
        }
    }

    public function removeHomeUpload($index)
    {
        if (isset($this->homeUploads[$index])) {
            unset($this->homeUploads[$index]);
            $this->homeUploads = array_values($this->homeUploads);
        }
    }

    public function removeCustomer($index)
    {
        if (!isset($this->customerBanners[$index])) return;
        $path = $this->customerBanners[$index];
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        array_splice($this->customerBanners, $index, 1);
        AppSetting::set('banner_customer', json_encode(array_values($this->customerBanners)));
        session()->flash('message', 'Banner customer dihapus.');
        $this->dispatchSliderUpdate();
    }

    public function removeMitra($index)
    {
        if (!isset($this->mitraBanners[$index])) return;
        $path = $this->mitraBanners[$index];
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        array_splice($this->mitraBanners, $index, 1);
        AppSetting::set('banner_mitra', json_encode(array_values($this->mitraBanners)));
        session()->flash('message', 'Banner mitra dihapus.');
        $this->dispatchSliderUpdate();
    }

    public function removeHome($index)
    {
        if (!isset($this->homeBanners[$index])) return;
        $path = $this->homeBanners[$index];
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        array_splice($this->homeBanners, $index, 1);
        AppSetting::set('banner_home', json_encode(array_values($this->homeBanners)));
        session()->flash('message', 'Banner beranda dihapus.');
        $this->dispatchSliderUpdate();
    }

    private function dispatchSliderUpdate()
    {
        $this->dispatch('bannersSaved', [
            'customer' => array_values($this->customerBanners),
            'mitra' => array_values($this->mitraBanners),
            'home' => array_values($this->homeBanners),
        ]);
    }

    public function render()
    {
        return view('superadmin.banners');
    }
}
