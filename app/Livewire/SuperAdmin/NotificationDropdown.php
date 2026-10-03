<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $recentUnread = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('refreshNotifications')]
    #[On('notification-updated')]
    public function loadNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->notifications = collect([]);
            $this->unreadCount = 0;
            $this->recentUnread = [];
            return;
        }
        
        // Load all notifications (limit to 10 most recent)
        $this->notifications = $user->notifications()
            ->take(10)
            ->get();
        
        // Get unread count
        $this->unreadCount = $user->unreadNotifications()->count();

        // Ambil notifikasi unread terbaru yang BELUM pernah di-pop-up (popped_at is null)
        $unreads = $user->unreadNotifications()
            ->whereNull('popped_at')
            ->latest()
            ->take(5)
            ->get();

        $this->recentUnread = $unreads->map(function ($notif) {
            $data = $notif->data ?? [];
            $type = $data['type'] ?? 'general';

            $title = $data['title'] ?? match($type) {
                'new_topup_request', 'topup_request_submitted' => '💰 Request Top-Up Saldo Baru',
                'new_withdraw_request', 'withdraw_status' => '💵 Permintaan Tarik Saldo Baru',
                'new_registration', 'new_user' => '👤 Pendaftaran Pengguna Baru',
                'help_taken' => '🤝 Bantuan Diambil',
                'help_status' => (isset($data['new_status']) && in_array($data['new_status'], ['komplain', 'disputed'])) 
                    ? '⚠️ Komplain Bantuan Diajukan' 
                    : '📢 Status Bantuan Diperbarui',
                default => '📢 Pemberitahuan Baru'
            };

            $message = $data['message'] ?? $data['body'] ?? 'Ada pembaruan baru yang memerlukan perhatian Anda.';

            return [
                'id' => (string) $notif->id,
                'type' => (string) $type,
                'title' => (string) $title,
                'message' => (string) $message,
                'url' => (string) $this->resolveNotificationUrl($notif),
                'created_at_human' => $notif->created_at->diffForHumans(),
            ];
        })->values()->toArray();

        // Tandai notifikasi yang dipop-upkan sebagai sudah pernah muncul (popped_at = now())
        if ($unreads->isNotEmpty()) {
            $user->notifications()->whereIn('id', $unreads->pluck('id'))->update(['popped_at' => now()]);
            $this->dispatch('admin-new-notifications', notifs: $this->recentUnread);
        }
    }

    public function resolveNotificationUrl($notification)
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? '';

        if (!empty($data['url'])) {
            return $data['url'];
        }

        return match ($type) {
            'new_topup_request', 'topup_request_submitted' => route('superadmin.topup.approvals'),
            'new_withdraw_request', 'withdraw_status' => route('superadmin.withdraws.index'),
            'new_registration', 'new_user' => route('superadmin.users'),
            'help_taken', 'help_status' => route('superadmin.helps.approved'),
            default => route('superadmin.notifications.index'),
        };
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->update(['read_at' => now(), 'popped_at' => $notification->popped_at ?? now()]);
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->unreadNotifications()->update(['read_at' => now(), 'popped_at' => now()]);
        $this->loadNotifications();
        $this->dispatch('notification-updated');
    }

    public function markAsPopped(array $ids)
    {
        $user = Auth::user();
        if ($user && !empty($ids)) {
            $user->notifications()->whereIn('id', $ids)->update(['popped_at' => now()]);
        }
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function deleteAllNotifications()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->notifications()->delete();
        $this->loadNotifications();
        $this->dispatch('notification-updated');
    }

    public function openNotification($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        if (!$notification) return;

        // Mark as read
        if (!$notification->read_at) {
            $notification->update(['read_at' => now(), 'popped_at' => $notification->popped_at ?? now()]);
        }

        $data = $notification->data ?? [];
        $type = $data['type'] ?? '';
        $url = $data['url'] ?? null;

        // Determine destination url if not directly present in data
        if (!$url) {
            $url = match ($type) {
                'new_topup_request', 'topup_request_submitted' => route('superadmin.topup.approvals'),
                'new_withdraw_request', 'withdraw_status' => route('superadmin.withdraws.index'),
                'new_registration', 'new_user' => route('superadmin.users'),
                'help_taken', 'help_status' => route('superadmin.helps.approved'),
                default => route('superadmin.notifications.index'),
            };
        }

        return redirect()->to($url);
    }

    public function render()
    {
        return view('livewire.super-admin.notification-dropdown');
    }
}
