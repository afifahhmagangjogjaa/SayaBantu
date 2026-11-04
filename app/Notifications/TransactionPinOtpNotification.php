<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TransactionPinOtpNotification extends Notification
{
    use Queueable;

    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode OTP Reset PIN Transaksi - SayaBantu')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Pengguna SayaBantu') . '!')
            ->line('Kami menerima permintaan untuk mereset PIN transaksi akun SayaBantu Anda.')
            ->line('Gunakan 6 digit kode OTP di bawah ini untuk memverifikasi permintaan Anda:')
            ->line(new HtmlString(
                '<div style="text-align: center; margin: 25px 0;">' .
                '<span style="display: inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0066FF; background: #eff6ff; padding: 12px 24px; border-radius: 12px; border: 2px dashed #bfdbfe;">' .
                $this->otp .
                '</span>' .
                '</div>'
            ))
            ->line('Kode ini berlaku selama 10 menit. Jangan pernah membagikan kode ini kepada siapa pun, termasuk pihak SayaBantu.')
            ->line('Jika Anda tidak merasa melakukan permintaan ini, silakan amankan akun Anda segera.')
            ->salutation('Salam hormat, Tim SayaBantu');
    }
}

