<?php

namespace App\Livewire\Mitra;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RealtimeNotifications extends Component
{
    public $last_chat_id = 0;
    public $last_notification_check = null;
    public $sanctionModalOpen = false;
    public $currentSanction = null;

    public function mount()
    {
        if (auth()->check()) {
            $this->last_chat_id = ChatModel::where('mitra_id', auth()->id())->max('id') ?? 0;
            $this->last_notification_check = now();
            $this->checkUnpoppedSanctions();
            $this->checkUnpoppedReportStatus();
            $this->checkUnpoppedHelpStatus();
        }
    }

    public function poll()
    {
        if (!auth()->check()) {
            return;
        }

        // Check for unpopped sanctions & report status
        if (!$this->sanctionModalOpen) {
            $this->checkUnpoppedSanctions();
            $this->checkUnpoppedReportStatus();
            $this->checkUnpoppedHelpStatus();
        }

        // Check for new chat messages
        $new = ChatModel::where('mitra_id', auth()->id())
            ->where('sender_type', 'customer')
            ->where('id', '>', $this->last_chat_id)
            ->orderBy('id', 'asc')
            ->first();

        if ($new) {
            Log::info('[RealtimeNotifications] found new chat id=' . $new->id . ' help_id=' . $new->help_id . ' message=' . Str::limit($new->message, 120));

            $this->last_chat_id = $new->id;

            // Dispatch with named parameters so Livewire exposes them as event detail in the browser
            $this->dispatch(
                'help-new-message',
                helpId: $new->help_id,
                message: Str::limit($new->message, 150),
                from: optional($new->customer)->name ?? 'Customer'
            );
        }

        // Check for new database notifications for mitra (help status updates, etc.)
        try {
            $newNotifications = auth()->user()->notifications()
                ->where('created_at', '>', $this->last_notification_check)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($newNotifications->count() > 0) {
                $this->last_notification_check = $newNotifications->last()->created_at;
            }

            foreach ($newNotifications as $notification) {
                $data = $notification->data;
                Log::info('[RealtimeNotifications] processing notification', ['id' => $notification->id, 'type' => $notification->type, 'data' => $data]);

                if (isset($data['type']) && $data['type'] === 'help_status') {
                    $notification->update(['popped_at' => now()]);
                    $helpId = $data['help_id'] ?? ($data['helpId'] ?? 0);
                    $newStatus = $data['new_status'] ?? ($data['newStatus'] ?? null);
                    $notifTitle = $data['title'] ?? 'Info Pesanan';
                    $notifMsg = $data['message'] ?? 'Status pesanan telah diperbarui';

                    $toastType = in_array($newStatus, ['dibatalkan', 'cancelled', 'partner_cancelled_direct', 'partner_reassigned']) ? 'error' : 'info';

                    // Dispatch a browser event via inline JS so frontend can react
                    $this->js(sprintf(
                        "console.log('🔔 Mitra help-status notification'); window.dispatchEvent(new CustomEvent('mitra-help-status', { detail: { helpId: %d, newStatus: '%s', message: '%s', title: '%s' } })); if (typeof window.showGlobalFlashToast === 'function') { window.showGlobalFlashToast('%s', '%s'); } if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                        $helpId,
                        addslashes($newStatus ?? ''),
                        addslashes($notifMsg),
                        addslashes($notifTitle),
                        addslashes($notifTitle . ': ' . $notifMsg),
                        $toastType
                    ));

                    Log::info('[RealtimeNotifications] dispatched mitra-help-status', ['help_id' => $helpId, 'new_status' => $newStatus]);
                }

                if (isset($data['type']) && $data['type'] === 'report_status') {
                    $notification->update(['popped_at' => now()]);
                    $this->js(sprintf(
                        "if (typeof window.showGlobalFlashToast === 'function') { window.showGlobalFlashToast('%s', '%s'); } if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                        addslashes(($data['title'] ?? 'Aduan') . ': ' . ($data['message'] ?? '')),
                        match($data['new_status'] ?? '') {
                            'resolved' => 'success',
                            'dismissed' => 'error',
                            default => 'success',
                        }
                    ));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[RealtimeNotifications] failed processing mitra notifications: ' . $e->getMessage());
        }
    }

    public function checkUnpoppedReportStatus()
    {
        if (!auth()->check()) {
            return;
        }

        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->get()
            ->first(function ($n) {
                return ($n->data['type'] ?? '') === 'report_status';
            });

        if ($notif) {
            $data = $notif->data;
            $notif->update(['popped_at' => now()]);

            $this->js(sprintf(
                "setTimeout(() => { if (typeof window.showGlobalFlashToast === 'function') { window.showGlobalFlashToast('%s', '%s'); } if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 600);",
                addslashes(($data['title'] ?? 'Aduan') . ': ' . ($data['message'] ?? '')),
                match($data['new_status'] ?? '') {
                    'resolved' => 'success',
                    'dismissed' => 'error',
                    default => 'success',
                }
            ));
        }
    }

    public function checkUnpoppedHelpStatus()
    {
        if (!auth()->check()) {
            return;
        }

        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->where('created_at', '>=', now()->subHours(2))
            ->latest()
            ->get()
            ->first(function ($n) {
                $type = $n->data['type'] ?? '';
                $newStatus = $n->data['new_status'] ?? '';
                return $type === 'help_status' && in_array($newStatus, ['partner_reassigned', 'dibatalkan', 'cancelled', 'partner_cancelled_direct']);
            });

        if ($notif) {
            $data = $notif->data;
            $notif->update(['popped_at' => now()]);

            $title = $data['title'] ?? 'Info Pesanan';
            $msg = $data['message'] ?? 'Status pesanan Anda telah diperbarui.';
            $this->js(sprintf(
                "setTimeout(() => { if (typeof window.showGlobalFlashToast === 'function') { window.showGlobalFlashToast('%s', 'error'); } if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 600);",
                addslashes($title . ': ' . $msg)
            ));
        }
    }

    public function checkUnpoppedSanctions()
    {
        if (!auth()->check()) {
            return;
        }

        // Cari notifikasi database sanksi/SP yang belum di-pop-up (popped_at null)
        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->latest()
            ->get()
            ->first(function ($n) {
                $type = $n->data['type'] ?? '';
                return $type === 'sanction_warning' || str_contains($type, 'sanction') || $n->type === 'App\Notifications\SanctionNotification';
            });

        if ($notif) {
            $data = $notif->data;
            $level = (int) ($data['warning_level'] ?? 1);

            // Tandai sudah di-pop agar tidak muncul lagi
            $notif->update(['popped_at' => now()]);

            // SP 0 (cabut sanksi): JANGAN tampilkan modal merah, cukup toast/flash hijau
            if ($level === 0) {
                $this->js("setTimeout(() => { if (typeof window.showGlobalFlashToast === 'function') { window.showGlobalFlashToast('\u2705 Surat Peringatan Dicabut: Status SP pada akun Anda telah resmi dicabut oleh Admin. Akun Anda kini kembali normal.', 'success'); } if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 400);");
                return;
            }

            $this->currentSanction = [
                'id' => (string) $notif->id,
                'title' => $data['title'] ?? ('⚠️ Surat Peringatan ' . $level),
                'message' => $data['message'] ?? 'Anda menerima Surat Peringatan dari admin.',
                'reason' => $data['reason'] ?? null,
                'warning_level' => $level,
                'url' => $data['url'] ?? route('mitra.profile'),
            ];
            $this->sanctionModalOpen = true;

            // Trigger notification sound / chime if browser permits
            $this->js("if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }");
        }
    }

    public function dismissSanctionModal($notificationId = null)
    {
        $wasLevel3 = ($this->currentSanction['warning_level'] ?? 0) >= 3;
        $userId = auth()->id();

        $this->sanctionModalOpen = false;
        $this->currentSanction = null;

        // SP 3: Baru blokir akun sekarang (setelah mitra lihat pop-up peringatan terlebih dulu)
        if ($wasLevel3 && $userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->is_banned = true;
                $user->status = 'blocked';
                $user->save();
            }
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Mitra Anda telah dinonaktifkan/diblokir secara permanen dari sistem karena menerima Surat Peringatan 3.');
        }
    }

    public function viewSanctionDetail($notificationId = null)
    {
        $id = $notificationId ?: ($this->currentSanction['id'] ?? null);

        // Tutup modal (jangan trigger ban — ban hanya terjadi dari halaman detail untuk SP 3)
        $this->sanctionModalOpen = false;
        $this->currentSanction = null;

        if ($id && !str_starts_with($id, 'user_warning_level_')) {
            return redirect()->route('notifications.sanction', $id);
        }

        return redirect()->route('mitra.profile');
    }

    public function render()
    {
        return view('livewire.mitra.realtime-notifications', [
            'sanctionModalOpen' => $this->sanctionModalOpen,
            'currentSanction' => $this->currentSanction,
        ]);
    }
}
