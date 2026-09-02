<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Rating;
use App\Models\City;
use App\Models\User;

#[Layout('layouts.superadmin')]
class Ratings extends Component
{
    use WithPagination;

    // Filters untuk tabel utama Rekap Per Pengguna
    public $search = '';
    public $roleFilter = '';
    public $ratingScoreFilter = '';
    public $cityFilter = '';
    public $perPage = 15;

    // Modal view all ratings of a specific user (Shopee style)
    public $showUserRatingsModal = false;
    public $selectedUserId = null;
    public $modalRatingFilter = 'all'; // 'all', '5', '4', '3', '2', '1', 'with_comment', 'anonymous'

    // Modal delete confirmation
    public $showDeleteModal = false;
    public $deleteRatingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => '', 'as' => 'role'],
        'ratingScoreFilter' => ['except' => '', 'as' => 'rating'],
        'cityFilter' => ['except' => '', 'as' => 'city'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function updatedRatingScoreFilter()
    {
        $this->resetPage();
    }

    public function updatedCityFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function setModalRatingFilter($filter)
    {
        $this->modalRatingFilter = $filter;
    }

    public function viewUserRatings($userId)
    {
        $this->selectedUserId = (int) $userId;
        $this->modalRatingFilter = 'all';
        $user = User::find($this->selectedUserId);
        if ($user) {
            $this->showUserRatingsModal = true;
        }
    }

    public function closeModal()
    {
        $this->showUserRatingsModal = false;
        $this->selectedUserId = null;
        $this->modalRatingFilter = 'all';
        $this->showDeleteModal = false;
        $this->deleteRatingId = null;
    }

    public function confirmDelete($id)
    {
        $this->deleteRatingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRating()
    {
        if (!$this->deleteRatingId) {
            return;
        }

        $rating = Rating::find($this->deleteRatingId);
        if ($rating) {
            $rating->delete();
            session()->flash('message', 'Ulasan berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteRatingId = null;
    }

    public function toggleShadowBan($userId = null)
    {
        $id = $userId ?? $this->selectedUserId;
        if (!$id) {
            return;
        }

        $user = User::find($id);
        if ($user) {
            $user->is_shadow_banned = !$user->is_shadow_banned;
            $user->shadow_banned_at = $user->is_shadow_banned ? now() : null;
            $user->save();

            $statusText = $user->is_shadow_banned ? 'dikenakan Shadow Ban (Senyap).' : 'dibebaskan dari Shadow Ban.';
            session()->flash('message', "Pengguna {$user->name} berhasil {$statusText}");
        }
    }

    public function render()
    {
        // Query Rekap Pengguna (Mitra & Customer)
        $usersQuery = User::whereIn('role', ['mitra', 'customer', 'kustomer'])
            ->with('city');

        if ($this->search) {
            $s = trim($this->search);
            $usersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($this->roleFilter) {
            if ($this->roleFilter === 'mitra') {
                $usersQuery->where('role', 'mitra');
            } elseif (in_array($this->roleFilter, ['customer', 'kustomer'])) {
                $usersQuery->whereIn('role', ['customer', 'kustomer']);
            }
        }

        if ($this->cityFilter) {
            $usersQuery->where('city_id', $this->cityFilter);
        }

        $users = $usersQuery->orderBy('name')->paginate($this->perPage);

        // Stats
        $stats = [
            'total_reviews' => Rating::count(),
            'mitra_avg' => round((float)Rating::where('type', 'customer_to_mitra')->orWhereNull('type')->avg('rating'), 1) ?: 0,
            'customer_avg' => round((float)Rating::where('type', 'mitra_to_customer')->avg('rating'), 1) ?: 0,
            'low_count' => Rating::whereIn('rating', [1, 2])->count(),
        ];

        // Jika modal detail ulasan terbuka, hitung breakdown rating Shopee-style
        $modalRatingsBreakdown = [
            'total' => 0,
            'stars' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
            'with_comment' => 0,
            'anonymous' => 0,
        ];
        $filteredModalRatings = collect();

        $selectedUser = null;
        if ($this->showUserRatingsModal && $this->selectedUserId) {
            $selectedUser = User::with('city')->find($this->selectedUserId);
            if ($selectedUser) {
                $allUserRatings = $selectedUser->isMitra()
                    ? $selectedUser->mitraRatings()->with(['rater', 'user', 'help.city'])->latest()->get()
                    : $selectedUser->customerRatings()->with(['rater', 'user', 'help.city'])->latest()->get();

                $modalRatingsBreakdown['total'] = $allUserRatings->count();
                for ($star = 1; $star <= 5; $star++) {
                    $modalRatingsBreakdown['stars'][$star] = $allUserRatings->where('rating', $star)->count();
                }
                $modalRatingsBreakdown['with_comment'] = $allUserRatings->filter(fn($r) => !empty(trim((string)$r->review)))->count();
                $modalRatingsBreakdown['anonymous'] = $allUserRatings->where('is_anonymous', true)->count();

                // Filter ratings sesuai pill yang dipilih
                if ($this->modalRatingFilter === 'all') {
                    $filteredModalRatings = $allUserRatings;
                } elseif (in_array($this->modalRatingFilter, ['1', '2', '3', '4', '5'])) {
                    $starVal = (int)$this->modalRatingFilter;
                    $filteredModalRatings = $allUserRatings->where('rating', $starVal);
                } elseif ($this->modalRatingFilter === 'with_comment') {
                    $filteredModalRatings = $allUserRatings->filter(fn($r) => !empty(trim((string)$r->review)));
                } elseif ($this->modalRatingFilter === 'anonymous') {
                    $filteredModalRatings = $allUserRatings->where('is_anonymous', true);
                }
            }
        }

        $cities = City::orderBy('name')->get();

        return view('superadmin.ratings', compact('users', 'stats', 'cities', 'modalRatingsBreakdown', 'filteredModalRatings', 'selectedUser'));
    }
}
