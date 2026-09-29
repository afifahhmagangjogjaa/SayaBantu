<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsIndex extends Component
{
    use WithPagination;

    public $filter = 'all'; // 'all' or 'unread'
    public $selected = [];

    public function setFilter($mode)
    {
        $this->filter = in_array($mode, ['all', 'unread']) ? $mode : 'all';
        $this->resetPage();
        $this->selected = [];
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

    public function readAndRedirect($notificationId, $url)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            $data = $notification->data ?? [];
            if (($data['type'] ?? '') === 'help_status' && ($data['new_status'] ?? '') === 'partner_cancelled_direct') {
                $mitraId = $data['mitra_id'] ?? null;
                if (!$mitraId && !empty($data['help_id'])) {
                    $help = \App\Models\Help::find($data['help_id']);
                    $mitraId = $help?->mitra_id;
                    if (!$mitraId) {
                        $activity = \App\Models\PartnerActivity::where('activity_type', 'help_cancelled')->where('description', 'like', "%#{$data['help_id']}%")->latest()->first();
                        $mitraId = $activity?->user_id;
                    }
                }
                if ($mitraId) {
                    $url = route('admin.mitra.show', $mitraId);
                }
            }
        }

        if (str_contains($url, 'ngrok-free.app')) {
            $url = preg_replace('/https?:\/\/[^\/]+/', config('app.url', 'http://127.0.0.1:8000'), $url);
        }

        return redirect()->to($url);
    }

    public function render()
    {
        $user = auth()->user();
        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $adminCityIds = $user->getAdminCityIds();
        $rawNotifs = $query->latest()->get();

        // City scoping filter
        $filtered = $rawNotifs->filter(function ($notif) use ($adminCityIds) {
            $data = $notif->data ?? [];
            if (!empty($data['url']) && str_contains($data['url'], '/superadmin/')) {
                return false;
            }
            if (!empty($data['city_id'])) {
                return in_array((int)$data['city_id'], $adminCityIds);
            }
            if (!empty($data['help_id'])) {
                $help = \App\Models\Help::find($data['help_id']);
                if ($help && $help->city_id) {
                    return in_array((int)$help->city_id, $adminCityIds);
                }
            }
            $targetUserId = $data['mitra_id'] ?? $data['user_id'] ?? null;
            if ($targetUserId) {
                $targetUser = \App\Models\User::find($targetUserId);
                if ($targetUser && $targetUser->city_id) {
                    return in_array((int)$targetUser->city_id, $adminCityIds);
                }
            }
            return true;
        });

        // Manual pagination on collection to preserve city filter
        $perPage = 20;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $unreadCount = $filtered->whereNull('read_at')->count();
        $totalCount  = $filtered->count();

        return view('livewire.admin.notifications-index', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'totalCount'    => $totalCount,
        ])->layout('layouts.admin', ['pageTitle' => 'Semua Notifikasi']);
    }
}
