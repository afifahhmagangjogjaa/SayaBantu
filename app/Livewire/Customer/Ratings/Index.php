<?php

namespace App\Livewire\Customer\Ratings;

use App\Models\Rating;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $activeTab = 'given'; // given = rating yg saya beri, received = rating yg saya terima

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $givenRatings = Rating::where('rater_id', $user->id)
            ->where('type', 'customer_to_mitra')
            ->with(['ratee', 'help'])
            ->latest()
            ->paginate(10, ['*'], 'givenPage');

        $receivedRatings = Rating::where('ratee_id', $user->id)
            ->where('type', 'mitra_to_customer')
            ->with(['rater', 'help'])
            ->latest()
            ->paginate(10, ['*'], 'receivedPage');

        return view('livewire.customer.ratings.index', [
            'givenRatings'    => $givenRatings,
            'receivedRatings' => $receivedRatings,
            'averageRating'   => $user->customer_average_rating,
            'ratingCount'     => $user->customer_rating_count,
        ]);
    }
}