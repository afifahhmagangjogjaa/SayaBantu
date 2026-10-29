<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Concerns\ChangesPassword;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.superadmin')]
class ChangePassword extends Component
{
    use ChangesPassword;

    public function render()
    {
        return view('livewire.super-admin.change-password', [
            'user' => Auth::user(),
        ]);
    }
}
