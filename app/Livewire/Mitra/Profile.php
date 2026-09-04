<?php

namespace App\Livewire\Mitra;

use App\Models\Help;
use App\Models\Rating;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.mitra')]
class Profile extends Component
{
    public function render()
    {
        $user = auth()->user() ? auth()->user()->fresh() : null;

        // Get mitra statistics
        $totalHelped = $user ? Help::where('mitra_id', $user->id)->count() : 0;
        $completedHelps = $user ? Help::where('mitra_id', $user->id)->where('status', 'selesai')->count() : 0;
        $averageRating = $user ? $user->mitra_average_rating : 0;
        $totalRatings = $user ? $user->mitra_rating_count : 0;

        return view('livewire.mitra.profile.index', [
            'user' => $user,
            'totalHelped' => $totalHelped,
            'completedHelps' => $completedHelps,
            'averageRating' => $averageRating,
            'totalRatings' => $totalRatings,
        ]);
    }
}
