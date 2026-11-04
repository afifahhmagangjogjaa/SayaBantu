<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Pengaturan Bantuan - Super Admin')]
class HelpSettings extends Component
{
    public bool $show_whatsapp_button = false;

    public function mount()
    {
        $this->show_whatsapp_button = AppSetting::isWhatsappEnabled();
    }

    public function toggleWhatsapp()
    {
        $this->show_whatsapp_button = ! $this->show_whatsapp_button;
        AppSetting::set('show_whatsapp_button', $this->show_whatsapp_button ? '1' : '0');

        $statusMsg = $this->show_whatsapp_button ? 'ditampilkan' : 'disembunyikan';
        session()->flash('message', "Tombol WhatsApp pada detail bantuan berhasil {$statusMsg}.");
        $this->dispatch('settingsSaved', message: "Tombol WhatsApp berhasil {$statusMsg}.");
    }

    public function render()
    {
        return view('superadmin.help-settings');
    }
}
