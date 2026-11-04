<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PasswordResetOtpNotification extends Notification
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
        $recipientName = $notifiable->name ?? 'Pengguna SayaBantu';

        return (new MailMessage)
            ->subject('Kode OTP Reset Kata Sandi - SayaBantu')
            ->greeting('Halo, ' . $recipientName . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun SayaBantu Anda.')
            ->line('Gunakan 6 digit kode verifikasi OTP di bawah ini untuk melanjutkan:')
            ->line(new HtmlString(
                '<div style="text-align: center; margin: 28px 0;">' .
                '<span style="display: inline-block; font-size: 34px; font-weight: 900; letter-spacing: 10px; color: #0066FF; background: #eff6ff; padding: 14px 28px; border-radius: 14px; border: 2px dashed #93c5fd; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">' .
                $this->otp .
                '</span>' .
                '</div>'
            ))
            ->line('Kode OTP ini berlaku selama 10 menit. Jangan pernah membagikan kode ini kepada siapa pun demi keamanan akun Anda.')
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini dan kata sandi Anda akan tetap aman.')
            ->salutation('Salam hormat, Tim SayaBantu');
    }
}

