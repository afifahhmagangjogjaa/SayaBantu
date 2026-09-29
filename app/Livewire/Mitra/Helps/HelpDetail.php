<?php

namespace App\Livewire\Mitra\Helps;

use App\Models\Help;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.mitra')]
class HelpDetail extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'closePartnerCancelStatusModal' => 'closePartnerCancelStatusModal',
        'status-changed' => 'handleStatusChanged',
    ];

    public function handleStatusChanged($data = [])
    {
        if ($this->help) {
            $this->help->refresh();
            $this->help->load(['user', 'city', 'rating', 'category']);
            $this->currentStatus = $this->help->status;
        }
    }

    public $helpId;
    public $help;
    public $currentStatus;
    public $rating = 0;
    public $review = '';
    public $showPartnerCancelModal = false;
    public $partnerCancelReason = '';
    // UI for showing the live status modal after partner cancel request
    public $showPartnerCancelStatusModal = false;
    public $partnerCancelStatus = null; // 'pending' | 'accepted' | 'rejected'

    // Completion proof fields
    public $completion_photo;
    public $completion_notes = '';
    public $showCompletionModal = false;

    public function mount($id)
    {
        $this->helpId = $id;
        $this->help = Help::with(['user', 'city', 'rating', 'category'])->findOrFail($id);

        // Verify this help belongs to the authenticated mitra.
        // If not assigned anymore, allow access only when there is a recent
        // notification that confirms the customer's acceptance of the
        // partner-cancellation request (so the mitra can see the confirmation modal after refresh).
        if ($this->help->mitra_id !== auth()->id()) {
            $allowed = false;

            // Look up recent notifications for this mitra related to this help
            $recent = DB::table('notifications')
                ->where('notifiable_id', auth()->id())
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();

            foreach ($recent as $n) {
                $data = json_decode($n->data, true);
                if (!is_array($data)) continue;
                if (isset($data['type']) && $data['type'] === 'help_status'
                    && isset($data['help_id']) && $data['help_id'] == $id
                    && isset($data['new_status']) && $data['new_status'] === 'cancel_accepted') {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                abort(403, 'Anda tidak memiliki akses ke bantuan ini.');
            }

            // If allowed because of a recent cancel_accepted notification,
            // show the partner-cancel accepted modal on mount.
            $this->showPartnerCancelStatusModal = true;
            $this->partnerCancelStatus = 'accepted';
        }

        $this->currentStatus = $this->help->status;

        // Auto-confirm jika customer tidak merespon dalam 24 jam
        if ($this->help->status === 'waiting_customer_confirmation' && $this->help->isCustomerConfirmationExpired()) {
            $this->help->autoConfirmIfExpired();
            $this->help->refresh();
            $this->currentStatus = $this->help->status;
        }

        // Tidak perlu session flash lagi, gunakan flag di database
    }

    public function loadHelp()
    {
        // Reload help data dari database untuk mendeteksi perubahan
        $oldStatus = $this->help->status;
        $oldFlag = $this->help->partner_cancel_prev_status;
        
        $this->help->refresh();
        $this->help->load(['user', 'city', 'rating']);
        
        $newStatus = $this->help->status;
        $newFlag = $this->help->partner_cancel_prev_status;
        
        // Detect status change untuk trigger notifikasi
        if ($oldStatus !== $newStatus || $oldFlag !== $newFlag) {
            if ($newFlag === 'cancel_accepted') {
                $this->dispatch('show-status-notification', message: 'Customer menerima pembatalan!');
            }

            if ($newFlag === 'cancel_rejected') {
                $this->dispatch('show-status-notification', message: 'Pembatalan ditolak customer!');
            }
        }
        
        $this->currentStatus = $newStatus;
    }

    public function copyOrderId()
    {
        $this->dispatch('show-status-notification', message: 'ID Pesanan disalin ke clipboard');
        $this->js('navigator.clipboard.writeText("' . $this->help->order_id . '")');
    }

    public function updateStatus($status, $timestampField = null)
    {
        $this->help->update([
            'status' => $status,
        ]);

        // Update timestamp field if provided
        if ($timestampField && !$this->help->$timestampField) {
            $this->help->update([
                $timestampField => now(),
            ]);
        }

        $this->currentStatus = $status;
        $this->help->refresh();

        // Dispatch notifikasi ke Alpine.js
        $this->dispatch('show-status-notification', message: 'Status berhasil diperbarui!');
        
        session()->flash('message', 'Status berhasil diperbarui!');
    }

    public function openPartnerCancelModal()
    {
        $this->partnerCancelReason = '';
        $this->showPartnerCancelModal = true;
    }

    public function requestPartnerCancel()
    {
        // Only allow partner assigned mitra to cancel and only for certain statuses
        if ($this->help->mitra_id !== auth()->id()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membatalkan bantuan ini.');
            return;
        }

        if (!in_array($this->help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way', 'partner_arrived'])) {
            session()->flash('error', 'Pembatalan tidak dapat dilakukan pada status ini.');
            return;
        }

        $oldStatus = $this->help->status;
        $mitra = auth()->user();
        $mitraId = auth()->id();
        $reason = $this->partnerCancelReason ?: 'Kendala di jalan / darurat';

        // 1. Catat ke log riwayat aktivitas mitra (PartnerActivity)
        try {
            \App\Models\PartnerActivity::create([
                'user_id' => $mitraId,
                'activity_type' => 'help_cancelled',
                'description' => 'Membatalkan Bantuan #' . $this->help->id . ' - Alasan: ' . $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to record PartnerActivity on help cancelled: ' . $e->getMessage());
        }

        // 2. Lepaskan mitra & kembalikan pesanan ke antrean menunggu mitra baru
        $this->help->update([
            'partner_cancel_prev_status' => $oldStatus,
            'partner_cancel_requested_at' => now(),
            'partner_cancel_reason' => $reason,
            'status' => 'menunggu_mitra',
            'mitra_id' => null,
            'partner_current_lat' => null,
            'partner_current_lng' => null,
            'partner_initial_lat' => null,
            'partner_initial_lng' => null,
        ]);

        // 3. Kirim notifikasi instan ke customer
        try {
            $customer = $this->help->user ?? $this->help->customer;
            if ($customer) {
                $customer->notify(new \App\Notifications\HelpStatusNotification(
                    $this->help, 
                    $oldStatus, 
                    'partner_cancelled_direct', 
                    $mitra
                ));
            }
        } catch (\Exception $e) {
            // silent fail for customer notification
        }

        // 4. Kirim notifikasi ke Admin yang mengelola wilayah/kota pesanan ini
        try {
            $cityId = $this->help->city_id;

            if ($cityId) {
                $cityAdmins = \App\Models\User::getAdminsForCity($cityId);

                foreach ($cityAdmins as $adminUser) {
                    try {
                        $adminUser->notify(new \App\Notifications\HelpStatusNotification(
                            $this->help,
                            $oldStatus,
                            'partner_cancelled_direct',
                            $mitra
                        ));
                    } catch (\Exception $e) {
                        // silent fail per admin
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed notifying admins on partner cancel: ' . $e->getMessage());
        }

        $this->showPartnerCancelModal = false;
        session()->flash('warning', 'Pesanan telah dibatalkan dan dialihkan kembali untuk mencari Rekan Jasa lain. Pembatalan dicatat dalam riwayat akun Anda.');
        return redirect()->route('mitra.dashboard');
    }

    public function closePartnerCancelStatusModal()
    {
        $this->showPartnerCancelStatusModal = false;
        $this->partnerCancelStatus = null;
    }

    public function acknowledgeAcceptedCancellation()
    {
        // Clear flag setelah mitra acknowledge modal
        if ($this->help->partner_cancel_prev_status === 'cancel_accepted') {
            $this->help->update([
                'partner_cancel_prev_status' => null,
            ]);
        }
        
        // Mark session untuk tidak tampilkan lagi
        session()->put('cancel_accepted_modal_shown_' . $this->helpId, true);
        
        $this->help->refresh();
    }

    public function acknowledgeRejectedCancellation()
    {
        // Clear flag setelah mitra acknowledge modal
        if ($this->help->partner_cancel_prev_status === 'cancel_rejected') {
            $this->help->update([
                'partner_cancel_prev_status' => null,
            ]);
        }
        
        // Mark session untuk tidak tampilkan lagi
        session()->put('cancel_rejected_modal_shown_' . $this->helpId, true);
        
        $this->help->refresh();
    }

    public function markPartnerStarted()
    {
        $oldStatus = $this->help->status;
        $this->help->update([
            'status' => 'partner_on_the_way',
            'partner_started_at' => now(),
            'partner_started_moving_at' => now(),
        ]);

        $this->currentStatus = 'partner_on_the_way';
        $this->help->refresh();
        $this->help->load(['user', 'mitra']);

        // Kirim notifikasi database ke customer
        try {
            if ($this->help->user) {
                $this->help->user->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'partner_on_the_way', $this->help->mitra));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi partner_on_the_way: ' . $e->getMessage());
        }

        // Dispatch notifikasi status changed
        $this->dispatch('status-changed', [
            'helpId' => $this->help->id,
            'oldStatus' => $oldStatus,
            'newStatus' => 'partner_on_the_way'
        ]);

        // Dispatch notifikasi ke Alpine.js
        $this->dispatch('show-status-notification', message: 'Perjalanan dimulai!');

        session()->flash('message', 'Perjalanan dimulai! Jangan lupa update lokasi Anda.');
    }

    public function markPartnerArrived()
    {
        $oldStatus = $this->help->status;
        $this->help->update([
            'status' => 'partner_arrived',
            'partner_arrived_at' => now(),
        ]);

        $this->currentStatus = 'partner_arrived';
        $this->help->refresh();
        $this->help->load(['user', 'mitra']);

        // Kirim notifikasi database ke customer
        try {
            if ($this->help->user) {
                $this->help->user->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'partner_arrived', $this->help->mitra));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi partner_arrived: ' . $e->getMessage());
        }

        // Dispatch notifikasi status changed
        $this->dispatch('status-changed', [
            'helpId' => $this->help->id,
            'oldStatus' => $oldStatus,
            'newStatus' => 'partner_arrived'
        ]);

        // Dispatch notifikasi ke Alpine.js
        $this->dispatch('show-status-notification', message: 'Anda sudah tiba di lokasi!');

        session()->flash('message', 'Anda sudah tiba di lokasi! Silakan mulai pekerjaan.');
    }

    public function markServiceStarted()
    {
        $this->updateStatus('sedang_diproses', 'service_started_at');
    }

    public function markServiceCompleted()
    {
        $this->updateStatus('sedang_diproses', 'service_completed_at');
    }

    public function startService()
    {
        $oldStatus = $this->help->status;
        // Ubah status ke in_progress dan set service_started_at
        $this->help->update([
            'status' => 'in_progress',
            'service_started_at' => now(),
        ]);

        $this->currentStatus = 'in_progress';
        $this->help->refresh();
        $this->help->load(['user', 'mitra']);

        // Kirim notifikasi database ke customer
        try {
            if ($this->help->user) {
                $this->help->user->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'in_progress', $this->help->mitra));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi in_progress: ' . $e->getMessage());
        }

        // Dispatch notifikasi status changed
        $this->dispatch('status-changed', [
            'helpId' => $this->help->id,
            'oldStatus' => $oldStatus,
            'newStatus' => 'in_progress'
        ]);

        // Dispatch notifikasi ke Alpine.js
        $this->dispatch('show-status-notification', message: 'Pekerjaan telah dimulai!');

        session()->flash('message', 'Pekerjaan telah dimulai!');
    }

    public function openCompletionModal()
    {
        $this->showCompletionModal = true;
    }

    public function closeCompletionModal()
    {
        $this->showCompletionModal = false;
        $this->completion_photo = null;
        $this->completion_notes = '';
        $this->resetErrorBag();
    }

    public function markCompleted()
    {
        $this->validate([
            'completion_photo' => 'required|image|max:5120',
            'completion_notes' => 'nullable|string|max:500',
        ], [
            'completion_photo.required' => 'Wajib mengunggah foto bukti selesai pekerjaan.',
            'completion_photo.image' => 'File bukti harus berupa gambar (JPG, PNG, WebP).',
            'completion_photo.max' => 'Ukuran foto maksimal 5 MB.',
            'completion_notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $photoPath = $this->completion_photo->store('completion_photos', 'public');

        $this->help->update([
            'status' => 'waiting_customer_confirmation',
            'completion_photo' => $photoPath,
            'completion_notes' => $this->completion_notes,
            'service_completed_at' => now(),
        ]);

        $this->currentStatus = 'waiting_customer_confirmation';
        $this->showCompletionModal = false;
        $this->help->refresh();

        // Kirim notifikasi database ke customer
        try {
            $this->help->user->notify(new \App\Notifications\HelpStatusNotification($this->help, 'in_progress', 'waiting_customer_confirmation', $this->help->mitra));
        } catch (\Exception $e) {
            // silent fail
        }

        // Dispatch notifikasi ke Alpine.js
        $this->dispatch('show-status-notification', message: 'Bukti pekerjaan berhasil dikirim! Menunggu konfirmasi customer.');

        session()->flash('message', 'Bukti pekerjaan berhasil dikirim! Menunggu konfirmasi customer.');
    }

    public function submitCustomerRating()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ], [
            'rating.required' => 'Rating harus diisi',
            'rating.min' => 'Rating minimal 1 bintang',
            'rating.max' => 'Rating maksimal 5 bintang',
            'review.max' => 'Review maksimal 500 karakter',
        ]);

        // Check if already rated
        $existingRating = \App\Models\Rating::where('help_id', $this->help->id)
            ->where('rater_id', auth()->id())
            ->where('type', 'mitra_to_customer')
            ->first();

        if ($existingRating) {
            session()->flash('error', 'Anda sudah memberikan rating untuk customer ini.');
            return;
        }

        // Check if order is completed
        if (!in_array($this->help->status, ['selesai', 'completed'])) {
            session()->flash('error', 'Rating hanya bisa diberikan untuk pesanan yang sudah selesai.');
            return;
        }

        // Create rating
        \App\Models\Rating::create([
            'help_id' => $this->help->id,
            'user_id' => $this->help->user_id, // Legacy: customer being rated
            'mitra_id' => auth()->id(), // Legacy: mitra giving rating
            'rater_id' => auth()->id(), // New: mitra giving rating
            'ratee_id' => $this->help->user_id, // New: customer receiving rating
            'type' => 'mitra_to_customer',
            'rating' => $this->rating,
            'review' => $this->review,
        ]);

        // Reset form
        $this->rating = 0;
        $this->review = '';

        // Reload help
        $this->help->refresh();
        $this->help->load('rating');

        session()->flash('message', 'Terima kasih atas rating Anda!');
    }

    public function setRating($value)
    {
        $this->rating = $value;
    }

    public function render()
    {
        if ($this->help && $this->help->status === 'waiting_customer_confirmation' && $this->help->isCustomerConfirmationExpired()) {
            $this->help->autoConfirmIfExpired();
            $this->help->refresh();
            $this->currentStatus = $this->help->status;
        }

        return view('livewire.mitra.helps.help-detail');
    }
}
