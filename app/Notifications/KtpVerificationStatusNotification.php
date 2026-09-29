<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KtpVerificationStatusNotification extends Notification
{
    use Queueable;

    public string $status; // 'approved' or 'rejected'
    public ?string $reason;

    public function __construct(string $status, ?string $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'approved';
        $role = $notifiable->role ?? 'user';

        if ($isApproved) {
            $title = '✅ Verifikasi Identitas Disetujui';
            $message = 'Selamat! Dokumen KTP dan foto selfie Anda telah disetujui oleh Admin. Akun Anda kini telah terverifikasi penuh dan siap digunakan.';
            $url = ($role === 'mitra') ? route('mitra.dashboard') : route('customer.dashboard');
        } else {
            $title = '❌ Verifikasi Identitas Ditolak';
            $reasonText = $this->reason ? " Alasan: {$this->reason}." : '';
            $message = "Pengajuan verifikasi identitas (KTP & Selfie) Anda ditolak oleh Admin.{$reasonText} Silakan periksa kembali dan unggah dokumen yang valid.";
            $url = route('profile.settings.verification');
        }

        return [
            'type' => $isApproved ? 'ktp_approved' : 'ktp_rejected',
            'title' => $title,
            'status' => $this->status,
            'reason' => $this->reason,
            'message' => $message,
            'url' => $url,
        ];
    }
}
