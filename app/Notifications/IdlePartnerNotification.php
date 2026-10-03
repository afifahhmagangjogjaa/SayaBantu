<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IdlePartnerNotification extends Notification
{
    use Queueable;

    protected $help;

    public function __construct($help)
    {
        $this->help = $help;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $mitraName = $this->help->mitra?->name ?? 'Rekan Jasa';
        $helpTitle = $this->help->title ?? ('Bantuan #' . $this->help->id);

        return [
            'type' => 'idle_partner_alert',
            'title' => '⏳ Rekan Jasa Belum Berangkat',
            'message' => "Pesanan '{$helpTitle}' sudah diambil oleh {$mitraName} lebih dari 30 menit yang lalu, namun belum menuju lokasi Anda.",
            'help_id' => $this->help->id,
            'mitra_id' => $this->help->mitra_id,
            'mitra_name' => $mitraName,
            'amount' => $this->help->amount,
            'url' => route('customer.helps.detail', $this->help->id),
        ];
    }
}
