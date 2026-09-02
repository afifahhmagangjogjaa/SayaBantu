<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class NotificationSettings extends Component
{
    public $generalNotification = true;
    public $sound = true;
    public $soundCall = false;
    public $vibrate = true;
    public $transactionUpdate = true;
    public $expenseReminder = false;
    public $budgetNotifications = false;
    public $lowBalanceAlerts = false;

    public function mount()
    {
        $user = auth()->user();
        if ($user && is_array($user->notification_settings)) {
            $settings = $user->notification_settings;
            $this->generalNotification = (bool) ($settings['generalNotification'] ?? true);
            $this->sound = (bool) ($settings['sound'] ?? true);
            $this->soundCall = (bool) ($settings['soundCall'] ?? false);
            $this->vibrate = (bool) ($settings['vibrate'] ?? true);
            $this->transactionUpdate = (bool) ($settings['transactionUpdate'] ?? true);
            $this->expenseReminder = (bool) ($settings['expenseReminder'] ?? false);
            $this->budgetNotifications = (bool) ($settings['budgetNotifications'] ?? false);
            $this->lowBalanceAlerts = (bool) ($settings['lowBalanceAlerts'] ?? false);
        }
    }

    public function updateSetting($setting)
    {
        if (property_exists($this, $setting)) {
            $this->$setting = !$this->$setting;

            $user = auth()->user();
            if ($user) {
                $user->update([
                    'notification_settings' => [
                        'generalNotification' => $this->generalNotification,
                        'sound' => $this->sound,
                        'soundCall' => $this->soundCall,
                        'vibrate' => $this->vibrate,
                        'transactionUpdate' => $this->transactionUpdate,
                        'expenseReminder' => $this->expenseReminder,
                        'budgetNotifications' => $this->budgetNotifications,
                        'lowBalanceAlerts' => $this->lowBalanceAlerts,
                    ]
                ]);
            }

            session()->flash('message', 'Pengaturan notifikasi berhasil diperbarui.');

            if ($this->$setting) {
                $this->dispatch('play-preview', setting: $setting);
            }
        }
    }

    public function render()
    {
        return view('livewire.profile.notification-settings');
    }
}
