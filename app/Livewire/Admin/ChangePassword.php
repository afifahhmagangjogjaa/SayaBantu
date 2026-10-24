<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\ChangesPassword;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ChangePassword extends Component
{
    use ChangesPassword;

    public function render()
    {
        return view('livewire.admin.change-password', [
            'user' => Auth::user(),
        ]);
    }
}
