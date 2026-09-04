<?php

namespace App\Livewire\Mitra;

use App\Models\Rating;
use Livewire\WithPagination;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.mitra')]
class Ratings extends Component
{
    use WithPagination;
    // Do not store paginator or complex objects as public properties in Livewire.
    // We'll fetch paginated ratings in render() and pass them to the view.

    public function render()
    {
        $userId = auth()->id();
        $baseQuery = Rating::forMitra($userId)->with(['rater', 'user', 'help.user', 'help.city']);

        $ratings = (clone $baseQuery)->latest()->paginate(10);
        $totalRatings = (clone $baseQuery)->count();
        $averageRating = $totalRatings > 0 ? round((float)(clone $baseQuery)->avg('rating'), 1) : 0;

        return view('livewire.mitra.ratings.index', compact('ratings', 'totalRatings', 'averageRating'));
    }
}
