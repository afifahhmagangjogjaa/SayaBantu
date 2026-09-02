<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount = 0;

    protected $listeners = [
        'topupRequestCreated' => 'refreshNotifications',
        'notificationRead' => 'refreshNotifications'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
        
        $this->unreadCount = auth()->user()
            ->unreadNotifications()
            ->count();
    }

    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            $this->loadNotifications();
            
            // Redirect based on notification type or explicit url
            $data = $notification->data;
            if (!empty($data['url'])) {
                return redirect()->to($data['url']);
            }
            if (isset($data['type'])) {
                switch ($data['type']) {
                    case 'new_topup_request':
                        return redirect()->route('admin.topup.approvals');
                    case 'new_withdraw_request':
                        return redirect()->route('admin.withdraws.index');
                    case 'new_registration':
                        return redirect()->route('admin.users.index');
                    case 'help_status':
                        if (!empty($data['help_id'])) {
                            return redirect()->route('admin.helps.show', $data['help_id']);
                        }
                        return redirect()->route('admin.helps');
                    default:
                        break;
                }
            }
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
        session()->flash('notification-success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function deleteNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.admin.notifications');
    }
}
