<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
use App\Models\AppSetting;
use App\Models\UserBalance;
use App\Services\LocationTrackingService;
use App\Notifications\HelpTakenNotification;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.mitra')]
class Dashboard extends Component
{
    use WithPagination;

    public $activeTab = 'tersedia'; // tersedia, semua, diproses, selesai
    public $userLat = null;
    public $userLng = null;

    public function mount()
    {
        // Check if tab parameter is in the query string
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['tersedia', 'semua', 'diproses', 'selesai'])) {
            $this->activeTab = $tab;
        }

        $user = auth()->user();
        if ($user) {
            $city = $user->city_id ? \App\Models\City::find($user->city_id) : null;
            $this->userLat = $user->latitude ? (float) $user->latitude : ($city ? (float) $city->latitude : null);
            $this->userLng = $user->longitude ? (float) $user->longitude : ($city ? (float) $city->longitude : null);
        }
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
    }

    #[On('balance-updated')]
    public function refreshBalance()
    {
        $this->dispatch('$refresh');
    }

    #[On('help-new-message')]
    public function refreshChatCount()
    {
        $this->dispatch('$refresh');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
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
        if (!$user || !$user->canTakeMoreOrders()) {
            if ($user && !$user->hasVerifiedEmail()) {
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

        $help = Help::findOrFail($helpId);

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

        // Set lokasi awal mitra jika GPS tersedia
        if ($latitude && $longitude) {
            $locationService = app(LocationTrackingService::class);
            $locationService->setInitialLocation($help, $latitude, $longitude);
        }

        // Send notification to customer that their help has been taken
        try {
            if ($help->user) {
                $help->user->notify(new HelpTakenNotification($help, auth()->user()));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send HelpTakenNotification', ['error' => $e->getMessage()]);
        }

        session()->flash('message', 'Bantuan berhasil diambil! GPS tracking aktif. Segera menuju lokasi customer.');
        
        // Emit event untuk mulai GPS tracking
        $this->dispatch('start-gps-tracking', helpId: $helpId);
        
        $this->setTab('diproses');

        // Redirect mitra to the help detail page so they can see full info and navigation
        // Use Livewire helper to redirect to named route
        return $this->redirectRoute('mitra.helps.detail', ['id' => $helpId]);
    }

    public function completeHelp($helpId)
    {
        $help = Help::where('id', $helpId)
            ->where('mitra_id', auth()->id())
            ->firstOrFail();

        $help->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);

        session()->flash('message', 'Bantuan berhasil diselesaikan! Terima kasih atas kebaikan Anda.');
        $this->setTab('selesai');
    }

    public function render()
    {
        $user = auth()->user();
        \App\Services\KtpVerificationNoticeService::ensurePromptNotification($user);

        try {
            Help::cancelExpiredUrgentHelps();
            Help::autoConfirmExpiredCustomerHelps();
        } catch (\Throwable $e) {}

        $userBalance = UserBalance::where('user_id', $user->id)->first();
        $balance = $userBalance ? $userBalance->balance : 0;

        // Statistik bantuan
        $isMitraShadowBanned = $user ? $user->isShadowBanned() : false;

        $availableHelpsQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            });

        if ($isMitraShadowBanned) {
            $availableHelpsQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $availableHelpsQuery->inMitraCity($user);
        }
        $availableHelpsCount = $availableHelpsQuery->count();

        $inProgressStatuses = [
            'memperoleh_mitra',
            'taken',
            'partner_on_the_way',
            'partner_arrived',
            'in_progress',
            'sedang_diproses',
            'partner_cancel_requested',
            'diproses_mitra',
            'waiting_customer_confirmation'
        ];

        $inProgressCount = Help::where('mitra_id', $user->id)
            ->whereIn('status', $inProgressStatuses)
            ->count();

        $completedCount = Help::where('mitra_id', $user->id)
            ->whereIn('status', ['selesai', 'completed'])
            ->count();

        // Data berdasarkan tab
        if ($this->activeTab === 'tersedia') {
            $helpsQuery = Help::where('status', 'menunggu_mitra')
                ->whereNull('mitra_id')
                ->whereDoesntHave('user', function ($q) {
                    $q->where('is_shadow_banned', true);
                })
                ->with(['user', 'city', 'category']);

            if ($isMitraShadowBanned) {
                $helpsQuery->whereRaw('1 = 0');
            } elseif ($user && !empty($user->city_id)) {
                $helpsQuery->inMitraCity($user);
            }

            $helps = $helpsQuery->latest()->paginate(10);
        } elseif ($this->activeTab === 'semua') {
            // Tampilkan SEMUA bantuan dari semua customer (status menunggu_mitra yang belum diambil)
            $helpsQuery = Help::where('status', 'menunggu_mitra')
                ->whereNull('mitra_id')
                ->whereDoesntHave('user', function ($q) {
                    $q->where('is_shadow_banned', true);
                })
                ->with(['user', 'city', 'category']);

            if ($isMitraShadowBanned) {
                $helpsQuery->whereRaw('1 = 0');
            } elseif ($user && !empty($user->city_id)) {
                // For the dashboard, prefer showing helps in the same city by default
                $helpsQuery->inMitraCity($user);
            }

            $helps = $helpsQuery->latest()->paginate(10);
        } elseif ($this->activeTab === 'diproses') {
            $helps = Help::where('mitra_id', $user->id)
                ->whereIn('status', $inProgressStatuses)
                ->with(['user', 'city', 'category'])
                ->latest()
                ->paginate(10);
        } else { // selesai
            $helps = Help::where('mitra_id', $user->id)
                ->whereIn('status', ['selesai', 'completed'])
                ->with(['user', 'city', 'category'])
                ->latest()
                ->paginate(10);
        }

        $userCity = ($user && $user->city_id) ? \App\Models\City::find($user->city_id) : null;
        $effectiveLat = $this->userLat ?: (($user && $user->latitude) ? (float) $user->latitude : ($userCity ? (float) $userCity->latitude : null));
        $effectiveLng = $this->userLng ?: (($user && $user->longitude) ? (float) $user->longitude : ($userCity ? (float) $userCity->longitude : null));

        // Additional curated lists for dashboard sections
        $relations = ['user', 'city'];
        if (Schema::hasColumn('helps', 'category_id')) {
            $relations[] = 'category';
        }

        $recommendedQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            })
            ->with($relations);

        // Terbaru: order by created_at desc
        $latestQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            })
            ->with($relations);

        // Terdekat: order by haversine distance
        $nearbyQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            })
            ->with($relations);

        $maxRadius = (float) \App\Models\AppSetting::get('mitra_max_distance_km', \App\Models\AppSetting::get('max_help_radius_km', 10));

        if ($effectiveLat && $effectiveLng) {
            $haversine = "(6371 * acos(least(1.0, greatest(-1.0, cos(radians($effectiveLat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($effectiveLng)) + sin(radians($effectiveLat)) * sin(radians(latitude))))))";
            $recommendedQuery->select('helps.*', \Illuminate\Support\Facades\DB::raw("$haversine AS distance"))
                ->whereNotNull('helps.latitude')
                ->whereNotNull('helps.longitude')
                ->whereRaw("$haversine <= ?", [$maxRadius]);

            $latestQuery->select('helps.*', \Illuminate\Support\Facades\DB::raw("$haversine AS distance"))
                ->whereNotNull('helps.latitude')
                ->whereNotNull('helps.longitude')
                ->whereRaw("$haversine <= ?", [$maxRadius]);

            $nearbyQuery->select('helps.*', \Illuminate\Support\Facades\DB::raw("$haversine AS distance"))
                ->whereNotNull('helps.latitude')
                ->whereNotNull('helps.longitude')
                ->whereRaw("$haversine <= ?", [$maxRadius])
                ->orderByRaw("$haversine ASC");
        } else {
            $recommendedQuery->select('helps.*');
            $latestQuery->select('helps.*');
            $nearbyQuery->select('helps.*')->orderByDesc('created_at');
        }

        if ($isMitraShadowBanned) {
            $recommendedQuery->whereRaw('1 = 0');
            $latestQuery->whereRaw('1 = 0');
            $nearbyQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $recommendedQuery->inMitraCity($user);
            $latestQuery->inMitraCity($user);
            $nearbyQuery->inMitraCity($user);
        }

        // Determine safe ordering depending on which columns exist
        if (Schema::hasColumn('helps', 'priority')) {
            if (Schema::hasColumn('helps', 'rating')) {
                $recommendedQuery->orderByDesc('priority')->orderByDesc('rating');
            } else {
                $recommendedQuery->orderByDesc('priority')->orderByDesc('created_at');
            }
        } elseif (Schema::hasColumn('helps', 'rating')) {
            $recommendedQuery->orderByDesc('rating')->orderByDesc('created_at');
        } else {
            $recommendedQuery->orderByDesc('created_at');
        }

        $recommendedHelps = $recommendedQuery->take(6)->get();

        if ($isMitraShadowBanned) {
            $latestQuery->whereRaw('1 = 0');
            $nearbyQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $latestQuery->inMitraCity($user);
            $nearbyQuery->inMitraCity($user);
        }

        $latestHelps = $latestQuery->orderByDesc('created_at')
            ->take(6)
            ->get();

        $nearbyHelps = $nearbyQuery->take(6)->get();

        // Unread chat count for mitra (messages sent by customers not yet read)
        $unreadChatCount = 0;
        try {
            $unreadChatCount = \App\Models\Chat::where('mitra_id', $user->id)
                ->whereNull('read_at')
                ->where('sender_type', 'customer')
                ->count();
        } catch (\Exception $e) {
            // ignore if Chat model or columns missing
        }

        return view('livewire.mitra.dashboard.index', [
            'helps' => $helps,
            'balance' => $balance,
            'availableHelpsCount' => $availableHelpsCount,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'user' => $user,
            'recommendedHelps' => $recommendedHelps,
            'latestHelps' => $latestHelps,
            'nearbyHelps' => $nearbyHelps,
            'unreadChatCount' => $unreadChatCount,
            'maxRadius' => $maxRadius,
        ]);
    }
}
