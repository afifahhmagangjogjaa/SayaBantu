<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewRegistrationNotification extends Notification
{
    use Queueable;

    public User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $roleLabel = match ($this->user->role) {
            'mitra' => 'Mitra',
            'customer', 'kustomer' => 'Customer',
            'admin' => 'Admin',
            default => ucfirst($this->user->role),
        };

        return [
            'type' => 'new_registration',
            'title' => 'Pendaftaran Pengguna Baru',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_role' => $this->user->role,
            'message' => "Pengguna baru telah mendaftar: {$this->user->name} ({$roleLabel})",
            'url' => route('superadmin.users'),
        ];
    }
}
