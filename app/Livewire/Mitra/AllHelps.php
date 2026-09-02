<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
use App\Models\User;
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
    public $sortBy = 'latest'; // latest, oldest, price_high, price_low

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $user = auth()->user();
        $isMitraShadowBanned = $user ? $user->isShadowBanned() : false;

        $query = Help::query()
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            });

        // Default behavior: show helps from the same city as the authenticated mitra
        // if the mitra has a city set. If mitra belum menetapkan kota, don't show any helps
        // and instruct them to set their city in profile.
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

        // Search berdasarkan nama, deskripsi, atau lokasi
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('city', function ($cityQuery) {
                        $cityQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Sort
        if ($this->sortBy === 'nearby') {
            // Try to prioritize helps from the same city as the authenticated user.
            $userCityId = optional($user)->city_id;
            if ($userCityId) {
                // Order by a boolean match first, then by latest
                $query->orderByRaw("(city_id = ?) DESC", [$userCityId])->latest();
            } else {
                // fallback to latest if we don't have user's city
                $query->latest();
            }
        } elseif ($this->sortBy === 'latest') {
            $query->latest();
        } elseif ($this->sortBy === 'oldest') {
            $query->oldest();
        } elseif ($this->sortBy === 'price_high') {
            $query->orderByDesc('estimated_price');
        } elseif ($this->sortBy === 'price_low') {
            $query->orderBy('estimated_price');
        }

        if ($needsCity) {
            // empty paginator
            $helps = Help::whereRaw('0 = 1')->paginate(15);
            return view('livewire.mitra.helps.all-helps', [
                'helps' => $helps,
                'needsCity' => true,
            ]);
        }

        $helps = $query->with(['user', 'city', 'category'])
            ->paginate(15);

        return view('livewire.mitra.helps.all-helps', [
            'helps' => $helps,
            'needsCity' => false,
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

        // Cek apakah mitra sudah terverifikasi KTP
        if (!$user || !$user->verified) {
            session()->flash('error', 'Akun Anda belum terverifikasi. Silakan upload foto KTP dan Selfie terlebih dahulu, lalu tunggu verifikasi dari admin.');
            return $this->redirectRoute('mitra.profile.edit', navigate: true);
        }

        if (!$user->isProfileComplete()) {
            $missing = implode(', ', optional($user)->getMissingProfileFields() ?? []);
            session()->flash('error', "Harap lengkapi data profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.");
            return $this->redirectRoute('mitra.profile.edit', navigate: true);
        }

        $help = Help::with('user')->findOrFail($helpId);

        if ($help->user && $help->user->isShadowBanned()) {
            session()->flash('error', 'Bantuan ini sudah tidak tersedia.');
            return;
        }

        if ($help->mitra_id) {
            session()->flash('error', 'Bantuan ini sudah diambil oleh mitra lain.');
            return;
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