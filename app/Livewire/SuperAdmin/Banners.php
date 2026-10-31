<?php

namespace App\Livewire\SuperAdmin;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.superadmin', ['title' => 'Pengaturan Banner, Ikon & Promosi', 'breadcrumb' => 'Super Admin'])]
class Banners extends Component
{
    use WithFileUploads;

    // Home / Landing Page Banners
    public array $homeBanners = [];

    public array $homeUploads = [];

    // App Icon
    public $appIcon = null;

    public $iconUpload = null;

    // Dashboard Promos / Ads (Multiple Items)
    public array $promos = [];

    // Modal & Form State for Add / Edit Promo
    public bool $showPromoModal = false;

    public ?string $editingPromoId = null;

    public string $promoFormLayout = 'standard'; // 'standard' or 'full_image'

    public bool $promoFormActive = true;

    public string $promoFormBadge = 'sayabantu.com 🤝';

    public string $promoFormTitle = '';

    public string $promoFormDesc = '';

    public string $promoFormButtonText = 'Kunjungi';

    public string $promoFormButtonLink = '';

    public bool $promoFormTargetBlank = false;

    public ?string $promoFormExistingImage = null;

    public $promoFormImageUpload = null;

    public function mount()
    {
        $this->homeBanners = json_decode((string) AppSetting::get('banner_home', '[]'), true) ?: [];
        $this->appIcon = AppSetting::get('app_icon', null);

        // Load multiple promos
        $rawPromos = AppSetting::get('dashboard_promos');
        if ($rawPromos) {
            $decoded = json_decode((string) $rawPromos, true);
            if (is_array($decoded)) {
                $this->promos = $decoded;
            }
        }

        if (empty($this->promos)) {
            // Check fallback single promo
            $singleRaw = AppSetting::get('dashboard_promo');
            if ($singleRaw) {
                $single = json_decode((string) $singleRaw, true);
                if (is_array($single) && ! empty($single['title'])) {
                    $single['id'] = $single['id'] ?? 'promo_'.uniqid();
                    $single['layout'] = $single['layout'] ?? 'standard';
                    $this->promos = [$single];
                }
            }

            if (empty($this->promos)) {
                $this->promos = [
                    [
                        'id' => 'promo_default',
                        'layout' => 'standard',
                        'is_active' => true,
                        'badge' => 'sayabantu.com 🤝',
                        'title' => 'Butuh bantuan untuk pekerjaan kecil hingga besar?',
                        'description' => 'Pesan jasa serabutan cepat, mudah, dan terpercaya kapan saja.',
                        'button_text' => 'Kunjungi',
                        'button_link' => route('customer.helps.create', absolute: false),
                        'target_blank' => false,
                        'image' => null,
                    ],
                ];
                AppSetting::set('dashboard_promos', json_encode($this->promos));
            }
        }
    }

    public function openNewPromoModal()
    {
        $this->resetValidation();
        $this->editingPromoId = null;
        $this->promoFormLayout = 'standard';
        $this->promoFormActive = true;
        $this->promoFormBadge = 'sayabantu.com 🤝';
        $this->promoFormTitle = '';
        $this->promoFormDesc = '';
        $this->promoFormButtonText = 'Kunjungi';
        $this->promoFormButtonLink = route('customer.helps.create', absolute: false);
        $this->promoFormTargetBlank = false;
        $this->promoFormExistingImage = null;
        $this->promoFormImageUpload = null;
        $this->showPromoModal = true;
    }

    public function editPromo(string $id)
    {
        $this->resetValidation();
        $item = collect($this->promos)->firstWhere('id', $id);
        if (! $item) {
            session()->flash('error', 'Iklan promosi tidak ditemukan.');

            return;
        }

        $this->editingPromoId = $id;
        $this->promoFormLayout = $item['layout'] ?? 'standard';
        $this->promoFormActive = $item['is_active'] ?? true;
        $this->promoFormBadge = $item['badge'] ?? 'sayabantu.com 🤝';
        $this->promoFormTitle = $item['title'] ?? '';
        $this->promoFormDesc = $item['description'] ?? '';
        $this->promoFormButtonText = $item['button_text'] ?? 'Kunjungi';
        $this->promoFormButtonLink = $item['button_link'] ?? route('customer.helps.create', absolute: false);
        $this->promoFormTargetBlank = $item['target_blank'] ?? false;
        $this->promoFormExistingImage = $item['image'] ?? null;
        $this->promoFormImageUpload = null;
        $this->showPromoModal = true;
    }

    public function closePromoModal()
    {
        $this->showPromoModal = false;
        $this->promoFormImageUpload = null;
        $this->editingPromoId = null;
    }

    public function savePromo()
    {
        $rules = [
            'promoFormLayout' => 'required|in:standard,full_image',
            'promoFormButtonLink' => 'required|string|max:500',
            'promoFormImageUpload' => 'nullable|image|max:10240',
        ];

        if ($this->promoFormLayout === 'full_image') {
            $rules['promoFormTitle'] = 'nullable|string|max:255';
            $rules['promoFormDesc'] = 'nullable|string|max:500';
            $rules['promoFormBadge'] = 'nullable|string|max:50';
            $rules['promoFormButtonText'] = 'nullable|string|max:50';

            if (! $this->promoFormExistingImage && ! $this->promoFormImageUpload) {
                $this->addError('promoFormImageUpload', 'Pilih file gambar banner untuk format Full Gambar.');

                return;
            }
        } else {
            $rules['promoFormTitle'] = 'required|string|max:255';
            $rules['promoFormDesc'] = 'nullable|string|max:500';
            $rules['promoFormBadge'] = 'nullable|string|max:50';
            $rules['promoFormButtonText'] = 'required|string|max:50';
        }

        $this->validate($rules, [
            'promoFormTitle.required' => 'Judul promosi wajib diisi.',
            'promoFormButtonText.required' => 'Teks tombol wajib diisi.',
            'promoFormButtonLink.required' => 'Tautan / link tujuan wajib diisi.',
            'promoFormImageUpload.image' => 'File harus berupa gambar (PNG, JPG, WebP, dll).',
            'promoFormImageUpload.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        try {
            $imagePath = $this->promoFormExistingImage;

            if ($this->promoFormImageUpload) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $this->promoFormImageUpload->store('promos', 'public');
                $this->promoFormImageUpload = null;
            }

            if ($this->editingPromoId) {
                foreach ($this->promos as $k => $promo) {
                    if (($promo['id'] ?? '') === $this->editingPromoId) {
                        $this->promos[$k] = [
                            'id' => $this->editingPromoId,
                            'layout' => $this->promoFormLayout,
                            'is_active' => (bool) $this->promoFormActive,
                            'badge' => trim((string) $this->promoFormBadge),
                            'title' => trim((string) $this->promoFormTitle),
                            'description' => trim((string) $this->promoFormDesc),
                            'button_text' => trim((string) $this->promoFormButtonText),
                            'button_link' => trim((string) $this->promoFormButtonLink),
                            'target_blank' => (bool) $this->promoFormTargetBlank,
                            'image' => $imagePath,
                        ];
                        break;
                    }
                }
                session()->flash('message', 'Iklan promosi berhasil diperbarui.');
            } else {
                $newId = 'promo_'.uniqid();
                $this->promos[] = [
                    'id' => $newId,
                    'layout' => $this->promoFormLayout,
                    'is_active' => (bool) $this->promoFormActive,
                    'badge' => trim((string) $this->promoFormBadge),
                    'title' => trim((string) $this->promoFormTitle),
                    'description' => trim((string) $this->promoFormDesc),
                    'button_text' => trim((string) $this->promoFormButtonText),
                    'button_link' => trim((string) $this->promoFormButtonLink),
                    'target_blank' => (bool) $this->promoFormTargetBlank,
                    'image' => $imagePath,
                ];
                session()->flash('message', 'Iklan promosi baru berhasil ditambahkan.');
            }

            $this->promos = array_values($this->promos);
            AppSetting::set('dashboard_promos', json_encode($this->promos));

            $this->showPromoModal = false;
            $this->editingPromoId = null;
        } catch (\Exception $e) {
            Log::error('Error saving dashboard promo: '.$e->getMessage());
            session()->flash('error', 'Gagal menyimpan iklan promosi: '.$e->getMessage());
        }
    }

    public function togglePromoActive(string $id)
    {
        foreach ($this->promos as $k => $promo) {
            if (($promo['id'] ?? '') === $id) {
                $this->promos[$k]['is_active'] = ! ($promo['is_active'] ?? true);
                break;
            }
        }
        AppSetting::set('dashboard_promos', json_encode(array_values($this->promos)));
        session()->flash('message', 'Status iklan promosi berhasil diperbarui.');
    }

    public function deletePromo(string $id)
    {
        foreach ($this->promos as $k => $promo) {
            if (($promo['id'] ?? '') === $id) {
                if (! empty($promo['image']) && Storage::disk('public')->exists($promo['image'])) {
                    Storage::disk('public')->delete($promo['image']);
                }
                unset($this->promos[$k]);
                break;
            }
        }
        $this->promos = array_values($this->promos);
        AppSetting::set('dashboard_promos', json_encode($this->promos));
        session()->flash('message', 'Iklan promosi berhasil dihapus.');
    }

    public function removePromoFormImageUpload()
    {
        $this->promoFormImageUpload = null;
    }

    public function removePromoFormExistingImage()
    {
        if ($this->promoFormExistingImage && Storage::disk('public')->exists($this->promoFormExistingImage)) {
            Storage::disk('public')->delete($this->promoFormExistingImage);
        }
        $this->promoFormExistingImage = null;
    }

    public function saveIcon()
    {
        $this->validate([
            'iconUpload' => 'required|image|mimes:png,jpg,jpeg,svg,webp,ico|max:5120',
        ], [
            'iconUpload.required' => 'Pilih file ikon terlebih dahulu.',
            'iconUpload.image' => 'File harus berupa gambar.',
            'iconUpload.mimes' => 'Format file yang didukung: PNG, JPG, JPEG, SVG, WebP, ICO.',
            'iconUpload.max' => 'Ukuran file ikon maksimal 5MB.',
        ]);

        if (! $this->iconUpload) {
            session()->flash('info', 'Pilih file ikon aplikasi terlebih dahulu.');

            return;
        }

        try {
            if ($this->appIcon && Storage::disk('public')->exists($this->appIcon)) {
                Storage::disk('public')->delete($this->appIcon);
            }

            $path = $this->iconUpload->store('icons', 'public');
            $this->appIcon = $path;
            $this->iconUpload = null;

            AppSetting::set('app_icon', $path);

            session()->flash('message', 'Ikon aplikasi berhasil diperbarui.');
            $this->dispatch('appIconSaved', ['icon' => asset('storage/'.$path)]);
        } catch (\Exception $e) {
            Log::error('Error saving app icon: '.$e->getMessage());
            session()->flash('error', 'Gagal menyimpan ikon aplikasi: '.$e->getMessage());
        }
    }

    public function resetIcon()
    {
        try {
            if ($this->appIcon && Storage::disk('public')->exists($this->appIcon)) {
                Storage::disk('public')->delete($this->appIcon);
            }

            $this->appIcon = null;
            $this->iconUpload = null;
            AppSetting::set('app_icon', '');

            session()->flash('message', 'Ikon aplikasi berhasil dikembalikan ke ikon default sistem.');
            $this->dispatch('appIconReset');
        } catch (\Exception $e) {
            Log::error('Error resetting app icon: '.$e->getMessage());
            session()->flash('error', 'Gagal mereset ikon aplikasi: '.$e->getMessage());
        }
    }

    public function removeIconUpload()
    {
        $this->iconUpload = null;
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
            Log::error('Error saving home banner: '.$e->getMessage());
            session()->flash('error', 'Gagal menyimpan banner beranda: '.$e->getMessage());
        }
    }

    public function removeHomeUpload($index)
    {
        if (isset($this->homeUploads[$index])) {
            unset($this->homeUploads[$index]);
            $this->homeUploads = array_values($this->homeUploads);
        }
    }

    public function removeHome($index)
    {
        if (! isset($this->homeBanners[$index])) {
            return;
        }
        $path = $this->homeBanners[$index];
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        array_splice($this->homeBanners, $index, 1);
        AppSetting::set('banner_home', json_encode(array_values($this->homeBanners)));
        session()->flash('message', 'Banner beranda berhasil dihapus.');
        $this->dispatchSliderUpdate();
    }

    private function dispatchSliderUpdate()
    {
        $this->dispatch('bannersSaved', [
            'home' => array_values($this->homeBanners),
        ]);
    }

    public function render()
    {
        return view('superadmin.banners');
    }
}
