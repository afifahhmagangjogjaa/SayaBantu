<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewKtpVerificationNotification extends Notification
{
    use Queueable;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $roleLabel = match ($this->user->role) {
            'mitra' => 'Mitra',
            'customer', 'kustomer' => 'Customer',
            default => ucfirst($this->user->role),
        };

        $isSuperAdmin = ($notifiable->role ?? null) === 'super_admin';
        $url = $isSuperAdmin ? route('superadmin.users') : route('admin.verifications');

        $cityId = $this->user->city_id;
        if (!$cityId && !empty($this->user->city)) {
            $cityId = \App\Models\City::where('name', 'like', '%' . trim($this->user->city) . '%')->value('id');
        }

        $cityName = null;
        if ($cityId) {
            $cityName = \App\Models\City::find($cityId)?->name;
        } elseif (!empty($this->user->city)) {
            $cityName = $this->user->city;
        }

        $locationText = $cityName ? " di {$cityName}" : '';

        return [
            'type' => 'new_ktp_verification',
            'title' => '🪪 Pengajuan Verifikasi KTP Baru',
            'user_id' => $this->user->id,
            'city_id' => $cityId,
            'city_name' => $cityName,
            'user_name' => $this->user->name,
            'user_role' => $this->user->role,
            'message' => "Pengguna {$this->user->name} ({$roleLabel}){$locationText} telah mengunggah dokumen identitas (KTP & Selfie) untuk diverifikasi.",
            'url' => $url,
        ];
    }
}
