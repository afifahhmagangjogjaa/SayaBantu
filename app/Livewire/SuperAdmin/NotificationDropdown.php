<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

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
            return;
        }
        
        // Load all notifications (limit to 5 most recent for compact dropdown)
        $this->notifications = $user->notifications()
            ->take(5)
            ->get();
        
        // Get unread count
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();
        $this->dispatch('notification-updated');
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

    public function openNotification($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        if (!$notification) return;

        // Mark as read
        if (!$notification->read_at) {
            $notification->markAsRead();
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
