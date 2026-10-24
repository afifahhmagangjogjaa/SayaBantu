<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Pengaturan Bantuan - Super Admin')]
class HelpSettings extends Component
{
    public function render()
    {
        return view('superadmin.help-settings');
    }
}
