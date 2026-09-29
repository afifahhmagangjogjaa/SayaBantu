<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
use App\Models\User;
use App\Models\AppSetting;
use App\Notifications\HelpTakenNotification;
use Livewire\Component;
use App\Services\LocationTrackingService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Schema;

#[Layout('layouts.mitra')]
class AllHelps extends Component
{
    use WithPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
        'sortBy' => ['except' => 'latest'],
    ];

    public $search = '';
    public $filterStatus = 'all'; // all, menunggu_mitra
    public $sortBy = 'latest'; // latest, oldest, nearby, price_high, price_low
    public $userLat = null;
    public $userLng = null;

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $city = $user->city_id ? \App\Models\City::find($user->city_id) : null;
            $this->userLat = $user->latitude ? (float) $user->latitude : ($city ? (float) $city->latitude : null);
            $this->userLng = $user->longitude ? (float) $user->longitude : ($city ? (float) $city->longitude : null);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function setCoordinates($lat, $lng)
    {
        $this->userLat = (float) $lat;
        $this->userLng = (float) $lng;

        if (auth()->check()) {
            auth()->user()->update([
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
        }

        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        \App\Services\KtpVerificationNoticeService::ensurePromptNotification($user);

        try {
            Help::cancelExpiredUrgentHelps();
        } catch (\Throwable $e) {}

        $isMitraShadowBanned = $user ? $user->isShadowBanned() : false;

        $userCity = ($user && $user->city_id) ? \App\Models\City::find($user->city_id) : null;
        $effectiveLat = $this->userLat ?: ($user && $user->latitude ? (float) $user->latitude : ($userCity ? (float) $userCity->latitude : null));
        $effectiveLng = $this->userLng ?: ($user && $user->longitude ? (float) $user->longitude : ($userCity ? (float) $userCity->longitude : null));

        $query = Help::query()
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            });

        $maxRadius = (float) \App\Models\AppSetting::get('mitra_max_distance_km', \App\Models\AppSetting::get('max_help_radius_km', 10));

        if ($effectiveLat && $effectiveLng) {
            $lat = (float) $effectiveLat;
            $lng = (float) $effectiveLng;
            $haversine = "(6371 * acos(least(1.0, greatest(-1.0, cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude))))))";
            
            $query->select('helps.*', \Illuminate\Support\Facades\DB::raw("$haversine AS distance"))
                ->whereNotNull('helps.latitude')
                ->whereNotNull('helps.longitude')
                ->whereRaw("$haversine <= ?", [$maxRadius]);
        } else {
            $query->select('helps.*');
        }
        
        $needsCity = false;
        if ($isMitraShadowBanned) {
            $query->whereRaw('1 = 0');
        } elseif ($user) {
            if (! empty($user->city_id)) {
                $query->inMitraCity($user);
            } else {
                // Mitra belum memilih kota — return empty result set and notify view
                $needsCity = true;
            }
        }

        // Filter berdasarkan status
        if ($this->filterStatus === 'menunggu_mitra') {
            $query->where('status', 'menunggu_mitra')->whereNull('mitra_id');
        } else {
            // Tampilkan semua bantuan dari semua customer
            $query->where('status', 'menunggu_mitra')->whereNull('mitra_id');
        }

        // Search berdasarkan judul, deskripsi, lokasi, alamat, nama pelanggan, kota, atau kategori
        if (!empty($this->search)) {
            $keyword = trim($this->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('helps.title', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.description', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.location', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.full_address', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('city', function ($cityQuery) use ($keyword) {
                        $cityQuery->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('category', function ($catQuery) use ($keyword) {
                        $catQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // Sort
        if ($this->sortBy === 'nearby') {
            if ($effectiveLat && $effectiveLng) {
                $lat = (float) $effectiveLat;
                $lng = (float) $effectiveLng;
                $haversine = "(6371 * acos(least(1.0, greatest(-1.0, cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude))))))";
                $query->orderByRaw("CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 0 ELSE 1 END")
                      ->orderByRaw("$haversine ASC")
                      ->latest();
            } else {
                $userCityId = optional($user)->city_id;
                if ($userCityId) {
                    $query->orderByRaw("(city_id = ?) DESC", [$userCityId])->latest();
                } else {
                    $query->latest();
                }
            }
        } elseif ($this->sortBy === 'latest') {
            $query->latest();
        } elseif ($this->sortBy === 'oldest') {
            $query->oldest();
        } elseif ($this->sortBy === 'price_high') {
            $query->orderByRaw('COALESCE(total_amount, amount, 0) DESC')->latest();
        } elseif ($this->sortBy === 'price_low') {
            $query->orderByRaw('COALESCE(total_amount, amount, 0) ASC')->latest();
        }

        if ($needsCity) {
            // empty paginator
            $helps = Help::whereRaw('0 = 1')->paginate(15);
            return view('livewire.mitra.helps.all-helps', [
                'helps' => $helps,
                'needsCity' => true,
                'maxRadius' => $maxRadius,
            ]);
        }

        $helps = $query->with(['user', 'city', 'category'])
            ->paginate(15);

        return view('livewire.mitra.helps.all-helps', [
            'helps' => $helps,
            'needsCity' => false,
            'maxRadius' => $maxRadius,
        ]);
    }

    public function takeHelp($helpId, $latitude = null, $longitude = null)
    {
        $user = auth()->user();

        // Cek apakah mitra sedang terkena shadow ban
        if ($user && $user->isShadowBanned()) {
            session()->flash('error', 'Tidak dapat mengambil bantuan saat ini.');
            return;
        }

        // Cek kuota order mitra (belum verifikasi email = maks 2 order, sudah verifikasi = unlimited)
        if (!$user->canTakeMoreOrders()) {
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', 'Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 bantuan. Silakan verifikasi email Anda terlebih dahulu untuk mengambil bantuan lagi.');
            } else {
                session()->flash('error', 'Anda telah mencapai batas maksimal order yang dapat diambil.');
            }
            return;
        }

        // Cek kelengkapan biodata
        if (!$user->isProfileComplete()) {
            $missing = implode(', ', optional($user)->getMissingProfileFields() ?? []);
            session()->flash('error', "Harap lengkapi biodata profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.");
            return $this->redirectRoute('mitra.profile.edit', navigate: true);
        }

        // Cek verifikasi KTP oleh admin
        if (!$user->verified) {
            session()->flash('error', 'Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi disetujui sebelum dapat mengambil bantuan.');
            return;
        }

        $help = Help::with('user')->findOrFail($helpId);

        if ($help->isExpired() || $help->status !== 'menunggu_mitra') {
            if ($help->isExpired()) {
                Help::cancelExpiredUrgentHelps();
            }
            session()->flash('error', 'Maaf, batas waktu pencarian mitra untuk bantuan ini sudah habis atau bantuan sudah tidak tersedia.');
            return;
        }

        if ($help->wasCancelledByMitra(auth()->id())) {
            session()->flash('error', 'Anda tidak bisa mengambil bantuan ini karena sudah pernah dibatalkan.');
            return;
        }

        if ($help->user && $help->user->isShadowBanned()) {
            session()->flash('error', 'Bantuan ini sudah tidak tersedia.');
            return;
        }

        if ($help->mitra_id) {
            session()->flash('error', 'Bantuan ini sudah diambil oleh mitra lain.');
            return;
        }

        // Cek apakah jarak melebihi batas maksimal radius mitra
        $maxRadius = (float) AppSetting::get('mitra_max_distance_km', AppSetting::get('max_help_radius_km', 10));
        $userCity = ($user && $user->city_id) ? \App\Models\City::find($user->city_id) : null;
        $checkLat = $latitude ?: (($user && \Illuminate\Support\Facades\Schema::hasColumn('users', 'latitude') && $user->latitude) ? (float) $user->latitude : ($userCity ? (float) $userCity->latitude : null));
        $checkLng = $longitude ?: (($user && \Illuminate\Support\Facades\Schema::hasColumn('users', 'longitude') && $user->longitude) ? (float) $user->longitude : ($userCity ? (float) $userCity->longitude : null));

        if ($checkLat && $checkLng && $help->latitude && $help->longitude) {
            $dist = 6371 * acos(min(1.0, max(-1.0, cos(deg2rad((float)$checkLat)) * cos(deg2rad((float)$help->latitude)) * cos(deg2rad((float)$help->longitude) - deg2rad((float)$checkLng)) + sin(deg2rad((float)$checkLat)) * sin(deg2rad((float)$help->latitude)))));
            if ($dist > $maxRadius) {
                session()->flash('error', "Jarak bantuan ini (" . number_format($dist, 1) . " km) melebihi batas maksimal radius {$maxRadius} km.");
                return;
            }
        }

        $help->update([
            'mitra_id' => auth()->id(),
            'status' => 'taken',
            'taken_at' => now(),
        ]);

        // Set lokasi awal mitra jika GPS tersedia dari parameter
        if ($latitude && $longitude) {
            try {
                $locationService = app(LocationTrackingService::class);
                $locationService->setInitialLocation($help, (float) $latitude, (float) $longitude);
                \Log::info('Set initial partner location from GPS on takeHelp', [
                    'help_id' => $help->id, 
                    'mitra_id' => auth()->id(),
                    'lat' => $latitude,
                    'lng' => $longitude
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to set initial location from GPS: ' . $e->getMessage(), ['help_id' => $help->id]);
            }
        } else {
            // Fallback: Jika GPS tidak tersedia, coba gunakan koordinat yang tersimpan pada profil mitra
            try {
                $mitra = auth()->user();
                if (!empty($mitra->latitude) && !empty($mitra->longitude)) {
                    try {
                        $locationService = app(LocationTrackingService::class);
                        $locationService->setInitialLocation($help, (float) $mitra->latitude, (float) $mitra->longitude);
                        \Log::info('Set initial partner location from profile on takeHelp', ['help_id' => $help->id, 'mitra_id' => $mitra->id]);
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to set initial location from profile: ' . $e->getMessage(), ['help_id' => $help->id]);
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        session()->flash('message', 'Bantuan berhasil diambil. Silakan hubungi pengguna.');

        // Create a notification for the customer so it appears in their notifications page
        try {
            $customer = User::find($help->user_id);
            if ($customer) {
                $customer->notify(new HelpTakenNotification($helpId, auth()->id(), optional(auth()->user())->name));
            }
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        // Emit event untuk redirect ke detail page
        $this->dispatch('help-taken', helpId: $helpId);

        // refresh pagination and query so the help disappears from the list
        $this->resetPage();
    }

}