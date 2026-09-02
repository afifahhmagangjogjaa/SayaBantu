<?php

namespace App\Http\Livewire\Customer\Ratings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rating;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 10;

    public function mount()
    {
        // Ratings given to customer by mitra are strictly internal and visible only to Super Admin.
        return redirect()->route('profile.show');
    }

    public function render()
    {
        $userId = auth()->id();

        $baseQuery = Rating::with(['rater', 'help'])
            ->forCustomer($userId);

        $ratings = (clone $baseQuery)->latest()->paginate($this->perPage);

        $totalRatings = (clone $baseQuery)->count();
        $averageRating = (clone $baseQuery)->avg('rating') ?: 0;
        $averageRating = round($averageRating, 1);

        return view('livewire.customer.ratings.index', [
            'ratings' => $ratings,
            'totalRatings' => $totalRatings,
            'averageRating' => $averageRating,
        ]);
    }
}
