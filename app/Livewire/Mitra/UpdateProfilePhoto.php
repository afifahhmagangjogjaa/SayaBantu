<?php

namespace App\Livewire\Mitra;

use App\Livewire\Concerns\UpdatesProfilePhoto;
use Livewire\Component;

class UpdateProfilePhoto extends Component
{
    use UpdatesProfilePhoto;

    protected function profileRouteName(): string
    {
        return 'mitra.profile';
    }

    public function render()
    {
        return view('livewire.mitra.update-profile-photo');
    }
}
