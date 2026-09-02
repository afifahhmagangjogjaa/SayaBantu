<?php

namespace App\Livewire\Mitra\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Notifications\DatabaseNotification;

#[Layout('layouts.mitra')]
class Index extends Component
{
    use WithPagination;

    public $filter = 'all'; // 'all' or 'unread'
    public $selected = [];

    public function setFilter($mode)
    {
        $this->filter = in_array($mode, ['all', 'unread']) ? $mode : 'all';
        $this->resetPage();
    }

    public function selectAllOnPage($ids = [])
    {
        $this->selected = is_array($ids) ? $ids : [];
    }

    public function clearSelection()
    {
        $this->selected = [];
    }

    public function bulkMarkAsRead()
    {
        if (empty($this->selected)) return;

        DatabaseNotification::whereIn('id', $this->selected)
            ->where('notifiable_id', auth()->id())
            ->get()
            ->each
            ->markAsRead();

        $this->selected = [];
        session()->flash('message', 'Notifikasi terpilih ditandai sebagai dibaca');
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) return;

        DatabaseNotification::whereIn('id', $this->selected)
            ->where('notifiable_id', auth()->id())
            ->delete();

        $this->selected = [];
        session()->flash('message', 'Notifikasi terpilih telah dihapus');
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
        }
    }

    public function readAndRedirect($notificationId, $url)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
        }
        
        return redirect()->to($url);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        session()->flash('message', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function deleteNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
        }
    }

    public function render()
    {
        $query = auth()->user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(15);
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $totalCount = auth()->user()->notifications()->count();

        return view('livewire.mitra.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
        ]);
    }
}
