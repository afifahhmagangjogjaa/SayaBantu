<?php

namespace App\Livewire\Customer;

use App\Livewire\Concerns\UpdatesProfilePhoto;
use Livewire\Component;

class UpdateProfilePhoto extends Component
{
    use UpdatesProfilePhoto;

    protected function profileRouteName(): string
    {
        return 'profile';
    }

    public function render()
    {
        return view('livewire.customer.update-profile-photo');
    }
}
