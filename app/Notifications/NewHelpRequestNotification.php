<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Help;

class NewHelpRequestNotification extends Notification
{
    use Queueable;

    public $help;

    public function __construct(Help $help)
    {
        $this->help = $help;
    }

    public function via($notifiable)
    {
        $settings = $notifiable->notification_settings ?? [];
        if (isset($settings['generalNotification']) && !$settings['generalNotification']) {
            return [];
        }
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'help_request',
            'help_id' => $this->help->id,
            'title' => 'Permintaan Bantuan Baru',
            'message' => "Ada permintaan bantuan baru di sekitar Anda: '{$this->help->title}'. Segera ambil sebelum keduluan mitra lain!",
            'help_amount' => $this->help->amount,
            'customer_name' => $this->help->user?->name ?? 'Customer',
        ];
    }
}
