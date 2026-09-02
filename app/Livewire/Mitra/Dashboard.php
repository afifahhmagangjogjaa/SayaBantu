<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
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

    public function mount()
    {
        // Check if tab parameter is in the query string
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['tersedia', 'semua', 'diproses', 'selesai'])) {
            $this->activeTab = $tab;
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
        if (!$user || !$user->isProfileComplete()) {
            $missing = implode(', ', optional($user)->getMissingProfileFields() ?? []);
            session()->flash('error', "Harap lengkapi data profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.");
            return $this->redirectRoute('mitra.profile.edit', navigate: true);
        }

        $help = Help::findOrFail($helpId);

        if ($help->mitra_id) {
            session()->flash('error', 'Bantuan ini sudah diambil oleh mitra lain.');
            return;
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

        $inProgressCount = Help::where('mitra_id', $user->id)
            ->where('status', 'memperoleh_mitra')
            ->count();

        $completedCount = Help::where('mitra_id', $user->id)
            ->where('status', 'selesai')
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
                ->where('status', 'memperoleh_mitra')
                ->with(['user', 'city', 'category'])
                ->latest()
                ->paginate(10);
        } else { // selesai
            $helps = Help::where('mitra_id', $user->id)
                ->where('status', 'selesai')
                ->with(['user', 'city', 'category'])
                ->latest()
                ->paginate(10);
        }

        // Additional curated lists for dashboard sections
        // Rekomendasi: prefer `priority` then `rating` when column exists,
        // otherwise fallback to `rating` then `created_at`.
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
        if ($isMitraShadowBanned) {
            $recommendedQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $recommendedQuery->inMitraCity($user);
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

        // Terbaru: order by created_at desc
        $latestQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            })
            ->with($relations);
        if ($isMitraShadowBanned) {
            $latestQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $latestQuery->inMitraCity($user);
        }
        $latestHelps = $latestQuery->orderByDesc('created_at')
            ->take(6)
            ->get();

        // Terdekat: simple city match fallback to latest if no city
        $nearbyQuery = Help::where('status', 'menunggu_mitra')
            ->whereNull('mitra_id')
            ->whereDoesntHave('user', function ($q) {
                $q->where('is_shadow_banned', true);
            })
            ->with($relations);

        if ($isMitraShadowBanned) {
            $nearbyQuery->whereRaw('1 = 0');
        } elseif ($user && !empty($user->city_id)) {
            $nearbyQuery->inMitraCity($user);
        }

        $nearbyHelps = $nearbyQuery->orderByDesc('created_at')
            ->take(6)
            ->get();

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
        ]);
    }
}
