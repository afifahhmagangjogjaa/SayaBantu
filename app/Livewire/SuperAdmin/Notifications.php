<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.superadmin')]
#[Title('Notifikasi - Super Admin')]
class Notifications extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $perPage = 15;

    public function mount()
    {
        //
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-updated');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->unreadNotifications->markAsRead();
        $this->dispatch('notification-updated');
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-updated');
        }
    }

    public function deleteAllRead()
    {
        $user = Auth::user();
        if (!$user) return;

        $user->readNotifications()->delete();
        $this->dispatch('notification-updated');
    }

    public function openNotification($notificationId)
    {
        $user = Auth::user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        if (!$notification) return;

        if (!$notification->read_at) {
            $notification->markAsRead();
            $this->dispatch('notification-updated');
        }

        $data = $notification->data ?? [];
        $type = $data['type'] ?? '';
        $url = $data['url'] ?? null;

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

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            return view('livewire.super-admin.notifications', [
                'notifications' => collect([]),
                'unreadCount' => 0,
                'totalCount' => 0,
                'readCount' => 0,
            ]);
        }

        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate($this->perPage);
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();
        $readCount = $totalCount - $unreadCount;

        return view('livewire.super-admin.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
            'readCount' => $readCount,
        ]);
    }
}
