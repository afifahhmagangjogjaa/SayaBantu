<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KtpVerificationPromptNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $hasKtpUploaded = !empty($notifiable->ktp_photo);
        $role = $notifiable->role ?? 'user';

        if ($hasKtpUploaded) {
            $title = '⏳ Verifikasi KTP Sedang Diproses';
            $message = 'Dokumen KTP Anda telah berhasil dikirim dan sedang dalam proses peninjauan oleh tim admin.';
        } else {
            $title = '🪪 Verifikasi KTP Diperlukan';
            $actionText = ($role === 'mitra') ? 'mengambil pesanan bantuan' : 'membuat permintaan bantuan';
            $message = "Akun Anda belum terverifikasi KTP. Silakan unggah foto KTP dan selfie untuk menyelesaikan verifikasi identitas agar dapat {$actionText}.";
        }

        return [
            'type' => 'ktp_verification_prompt',
            'title' => $title,
            'message' => $message,
            'url' => route('profile.settings.verification'),
            'has_uploaded' => $hasKtpUploaded,
        ];
    }
}
