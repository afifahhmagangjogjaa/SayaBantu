<?php

namespace App\Livewire\SuperAdmin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

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
        if (! $user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->update(['read_at' => now(), 'popped_at' => $notification->popped_at ?? now()]);
            $this->dispatch('notification-updated');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->unreadNotifications()->update(['read_at' => now(), 'popped_at' => now()]);
        $this->dispatch('notification-updated');
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-updated');
        }
    }

    public function deleteAllRead()
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->readNotifications()->delete();
        $this->dispatch('notification-updated');
    }

    public function deleteAllNotifications()
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->notifications()->delete();
        $this->dispatch('notification-updated');
        session()->flash('message', 'Semua notifikasi berhasil dihapus.');
    }

    public function openNotification($notificationId)
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);
        if (! $notification) {
            return;
        }

        if (! $notification->read_at) {
            $notification->markAsRead();
            $this->dispatch('notification-updated');
        }

        $targetUrl = \App\Services\NotificationUrlResolver::resolveUrl($notification, $user);

        return redirect()->to($targetUrl);
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        if (! $user) {
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
