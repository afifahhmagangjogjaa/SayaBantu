<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.superadmin')]
#[Title('Pengaturan Bantuan - Super Admin')]
class HelpSettings extends Component
{
    public function render()
    {
        return view('superadmin.help-settings');
    }
}