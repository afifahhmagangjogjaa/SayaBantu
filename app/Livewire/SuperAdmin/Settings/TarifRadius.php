<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Tarif & Radius Bantuan - Super Admin')]
class TarifRadius extends Component
{
    public $min_help_nominal;

    public $admin_fee;

    public $default_urgent_nominal;

    public $mitra_max_distance_km;

    public $show_whatsapp_button = false;

    protected function rules()
    {
        return [
            'min_help_nominal' => 'required|numeric|min:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'default_urgent_nominal' => 'required|numeric|min:0',
            'mitra_max_distance_km' => 'required|numeric|min:1|max:100',
        ];
    }

    public function mount()
    {
        $this->min_help_nominal = (int) AppSetting::get('min_help_nominal', 10000);
        $this->admin_fee = (float) AppSetting::get('admin_fee', 0);
        $this->default_urgent_nominal = (int) AppSetting::get('default_urgent_nominal', 50000);
        $this->mitra_max_distance_km = (float) AppSetting::get('mitra_max_distance_km', 10);
        $this->show_whatsapp_button = AppSetting::isWhatsappEnabled();
    }

    public function updatedShowWhatsappButton($value)
    {
        AppSetting::set('show_whatsapp_button', $value ? '1' : '0');
        session()->flash('message', 'Visibilitas tombol WhatsApp berhasil diperbarui.');
        $this->dispatch('settingsSaved', message: 'Visibilitas tombol WhatsApp berhasil diperbarui.');
    }

    public function save()
    {
        $nominalFields = ['min_help_nominal', 'admin_fee', 'default_urgent_nominal'];
        foreach ($nominalFields as $f) {
            if (isset($this->$f)) {
                $this->$f = (int) preg_replace('/\D/', '', (string) $this->$f);
            }
        }

        $this->mitra_max_distance_km = (float) preg_replace('/[^0-9.]/', '', (string) $this->mitra_max_distance_km);

        $this->validate();

        AppSetting::set('min_help_nominal', (string) $this->min_help_nominal);
        AppSetting::set('admin_fee', (string) ($this->admin_fee ?? 0));
        AppSetting::set('default_urgent_nominal', (string) $this->default_urgent_nominal);
        AppSetting::set('mitra_max_distance_km', (string) $this->mitra_max_distance_km);
        AppSetting::set('max_help_radius_km', (string) $this->mitra_max_distance_km);
        AppSetting::set('show_whatsapp_button', $this->show_whatsapp_button ? '1' : '0');

        session()->flash('message', 'Tarif, radius, dan pengaturan WhatsApp bantuan berhasil disimpan.');
        $this->dispatch('settingsSaved', message: 'Tarif, radius, dan pengaturan WhatsApp bantuan berhasil disimpan.');
    }

    public function render()
    {
        return view('superadmin.tarif-radius');
    }
}
