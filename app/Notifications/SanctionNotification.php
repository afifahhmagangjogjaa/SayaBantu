<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SanctionNotification extends Notification
{
    use Queueable;

    public int $warningLevel;
    public ?string $reason;
    public ?int $reportId;

    public function __construct(int $warningLevel, ?string $reason = null, ?int $reportId = null)
    {
        $this->warningLevel = $warningLevel;
        $this->reason = $reason;
        $this->reportId = $reportId;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $title = match ($this->warningLevel) {
            1 => '⚠️ Surat Peringatan 1',
            2 => '⚠️ Surat Peringatan 2',
            3 => '⛔ Surat Peringatan 3 - Akun Diblokir',
            default => '✅ Surat Peringatan Dicabut',
        };

        $reasonText = !empty($this->reason) ? ' Alasan: ' . $this->reason . '.' : '';

        $message = match ($this->warningLevel) {
            1 => 'Anda menerima Surat Peringatan 1.' . $reasonText . ' Harap patuhi syarat & ketentuan layanan SayaBantu.',
            2 => 'PERINGATAN KERAS: Anda menerima Surat Peringatan 2.' . $reasonText . ' Akun Anda terancam diblokir permanen jika mencapai Surat Peringatan 3.',
            3 => 'Akun Anda telah dinonaktifkan/diblokir dari sistem karena menerima Surat Peringatan 3.' . $reasonText,
            default => 'Surat Peringatan pada akun Anda telah resmi dicabut oleh Admin. Status akun Anda kini kembali normal.',
        };

        $url = ($notifiable->role === 'mitra') ? route('mitra.profile') : route('profile');

        return [
            'type' => 'sanction_warning',
            'warning_level' => $this->warningLevel,
            'reason' => $this->reason,
            'report_id' => $this->reportId,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ];
    }
}
