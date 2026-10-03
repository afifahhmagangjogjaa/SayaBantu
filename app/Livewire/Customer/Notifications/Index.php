<?php

namespace App\Livewire\Customer\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Notifications\DatabaseNotification;

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

        $notifications = DatabaseNotification::whereIn('id', $this->selected)
            ->where('notifiable_id', auth()->id())
            ->get();

        $deletedCount = 0;
        foreach ($notifications as $n) {
            $type = $n->data['type'] ?? '';
            if ($type !== 'sanction_warning' && !str_contains($type, 'sanction')) {
                $n->delete();
                $deletedCount++;
            }
        }

        $this->selected = [];
        if ($deletedCount < $notifications->count()) {
            session()->flash('message', 'Notifikasi berhasil dihapus (Surat Peringatan tidak dapat dihapus).');
        } else {
            session()->flash('message', 'Notifikasi terpilih telah dihapus');
        }
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
            $type = $notification->data['type'] ?? '';
            if ($type === 'sanction_warning' || str_contains($type, 'sanction')) {
                session()->flash('error', 'Surat Peringatan adalah sanksi resmi dan tidak dapat dihapus.');
                return;
            }
            $notification->delete();
        }
    }

    public function render()
    {
        if (auth()->check()) {
            \App\Services\KtpVerificationNoticeService::ensurePromptNotification(auth()->user());

            // Pastikan jika ada rekan jasa yang idle >= 30 menit sesuai kondisi riil, notifikasi masuk ke inbox
            $candidates = \App\Models\Help::where('user_id', auth()->id())
                ->whereIn('status', ['taken', 'memperoleh_mitra'])
                ->whereNotNull('mitra_id')
                ->whereNull('partner_started_moving_at')
                ->whereNull('partner_started_at')
                ->get();

            foreach ($candidates as $idleHelp) {
                if (!$idleHelp->isPartnerIdleOver30Minutes()) {
                    continue;
                }

                $alreadyNotified = \Illuminate\Support\Facades\DB::table('notifications')
                    ->where('notifiable_id', auth()->id())
                    ->where('data->type', 'idle_partner_alert')
                    ->where('data->help_id', $idleHelp->id)
                    ->where('data->mitra_id', $idleHelp->mitra_id)
                    ->exists();

                if (!$alreadyNotified) {
                    try {
                        auth()->user()->notify(new \App\Notifications\IdlePartnerNotification($idleHelp));
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        }

        $query = auth()->user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(15);
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $totalCount = auth()->user()->notifications()->count();

        return view('livewire.customer.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
            'filter' => $this->filter,
            'selected' => $this->selected ?? [],
        ]);
    }
}
