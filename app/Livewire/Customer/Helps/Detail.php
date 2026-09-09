<?php

namespace App\Livewire\Customer\Helps;

use App\Models\Help;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class Detail extends Component
{
    use WithFileUploads;

    public $help;
    public $helpId;
    public $showCancelConfirm = false;
    public $showMapModal = false;
    public $showRatingForm = false;
    public $rating = 0;
    public $review = '';
    public $is_anonymous = false;

    // Complaint / Dispute modal state
    public $showComplaintModal = false;
    public $complaint_reason = '';
    public $complaint_photo;

    protected $listeners = [
        'refreshHelp' => '$refresh',
        'status-changed' => 'handleStatusChanged'
    ];

    public function mount($id)
    {
        $this->helpId = $id;
        $this->loadHelp();
    }

    public function loadHelp()
    {
        $this->help = Help::with([
            'user',
            'mitra',
            'city',
            'category',
            'ratings'
        ])->findOrFail($this->helpId);

        // Check authorization
        if ($this->help->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Dispatch event untuk update tracking data
        if ($this->showMapModal && in_array($this->help->status, ['taken', 'partner_on_the_way', 'partner_arrived'])) {
            $customerLat = !empty($this->help->latitude) ? (float)$this->help->latitude : -6.2088;
            $customerLng = !empty($this->help->longitude) ? (float)$this->help->longitude : 106.8456;
            $partnerLat = !empty($this->help->partner_current_lat) ? (float)$this->help->partner_current_lat : (!empty($this->help->mitra?->latitude) ? (float)$this->help->mitra->latitude : $customerLat);
            $partnerLng = !empty($this->help->partner_current_lng) ? (float)$this->help->partner_current_lng : (!empty($this->help->mitra?->longitude) ? (float)$this->help->mitra->longitude : $customerLng);

            $this->dispatch('tracking-data-updated', [
                'partnerLat' => $partnerLat,
                'partnerLng' => $partnerLng,
                'customerLat' => $customerLat,
                'customerLng' => $customerLng,
            ]);
        }

        // Log untuk debug
        Log::info('Customer Help Detail - Data Refreshed', [
            'help_id' => $this->help->id,
            'status' => $this->help->status,
            'partner_current_lat' => $this->help->partner_current_lat,
            'partner_current_lng' => $this->help->partner_current_lng,
            'partner_on_the_way' => $this->help->status === 'partner_on_the_way',
            'partner_started_moving_at' => $this->help->partner_started_moving_at,
            'partner_arrived_at' => $this->help->partner_arrived_at
        ]);
    }

    public function copyOrderId()
    {
        $this->dispatch('copied', orderId: $this->help->order_id);
    }

    public function confirmCancel()
    {
        $this->showCancelConfirm = true;
    }

    public function acceptPartnerCancellation()
    {
        if ($this->help->status !== 'partner_cancel_requested') {
            session()->flash('error', 'Tidak ada permintaan pembatalan dari mitra.');
            return;
        }

        // Capture mitra reference before unassigning
        $assignedMitra = $this->help->mitra;
        $assignedMitraId = $this->help->mitra_id;

        // Make help available again for other mitra
        // Set partner_cancel_prev_status sebagai flag bahwa pembatalan diterima
        $this->help->update([
            'status' => 'menunggu_mitra', // Ubah ke menunggu_mitra agar bisa diambil mitra lain
            'mitra_id' => null,
            'partner_cancel_prev_status' => 'cancel_accepted', // Flag untuk modal
            // Jangan hapus partner_cancel_requested_at dan reason untuk history
        ]);

        // Notify mitra (if exists)
        try {
            if ($assignedMitra) {
                $assignedMitra->notify(new \App\Notifications\HelpStatusNotification($this->help, 'partner_cancel_requested', 'cancel_accepted', $assignedMitra));
            }
        } catch (\Exception $e) {
            // ignore notification failures
        }

        $this->loadHelp();
        session()->flash('success', 'Permintaan pembatalan diterima. Pesanan kembali menunggu Rekan Jasa lain.');
    }

    public function rejectPartnerCancellation()
    {
        if ($this->help->status !== 'partner_cancel_requested') {
            session()->flash('error', 'Tidak ada permintaan pembatalan dari mitra.');
            return;
        }

        $prev = $this->help->partner_cancel_prev_status ?: 'taken';

        // Set partner_cancel_prev_status sebagai flag bahwa pembatalan ditolak
        $this->help->update([
            'status' => $prev,
            'partner_cancel_prev_status' => 'cancel_rejected', // Flag untuk modal
            // Simpan timestamp untuk tracking
        ]);

        // Notify mitra that cancellation was rejected
        try {
            if ($this->help->mitra) {
                $this->help->mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, 'partner_cancel_requested', 'cancel_rejected', $this->help->mitra));
            }
        } catch (\Exception $e) {
            // ignore
        }

        $this->loadHelp();
        session()->flash('success', 'Permintaan pembatalan ditolak. Silakan lanjutkan pekerjaan.');
    }

    public function cancelHelp()
    {
        try {
            // Allow cancel if status is before completion/in-progress
            $cancellableStatuses = [
                'menunggu_pembayaran', 
                'menunggu_mitra', 
                'mencari_mitra', 
                'memperoleh_mitra', 
                'taken', 
                'partner_on_the_way', 
                'partner_arrived'
            ];

            if (!in_array($this->help->status, $cancellableStatuses)) {
                session()->flash('error', 'Bantuan tidak dapat dibatalkan pada status ini.');
                return;
            }

            $prevStatus = $this->help->status;
            $assignedMitra = $this->help->mitra;

            $this->help->update([
                'status' => 'dibatalkan',
            ]);

            // Log activity
            Log::info('Help cancelled by customer', [
                'help_id' => $this->help->id,
                'user_id' => auth()->id(),
                'mitra_id' => $assignedMitra?->id,
                'previous_status' => $prevStatus,
            ]);

            session()->flash('success', 'Permintaan bantuan berhasil dibatalkan. Saldo pembayaran telah dikembalikan ke dompet Anda.');
            $this->showCancelConfirm = false;
            
            // Redirect to helps index
            return redirect()->route('customer.helps.index');
        } catch (\Exception $e) {
            Log::error('Error cancelling help: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat membatalkan bantuan.');
        }
    }

    public function closeModal()
    {
        $this->showCancelConfirm = false;
    }

    public function showTrackingMap()
    {
        // Reload help data to get latest coordinates
        $this->loadHelp();

        // Check if partner is on the way or at nearby statuses
        if (!in_array($this->help->status, ['taken', 'partner_on_the_way', 'partner_arrived'])) {
            session()->flash('error', 'Tracking hanya tersedia saat mitra sedang menuju lokasi.');
            return;
        }

        // Fallback coordinates if customer latitude/longitude is missing
        if (!$this->help->latitude || !$this->help->longitude) {
            $this->help->latitude = $this->help->latitude ?: -6.2088;
            $this->help->longitude = $this->help->longitude ?: 106.8456;
        }

        $this->showMapModal = true;
        
        // Dispatch event to frontend to initialize map
        $this->dispatch('mapModalOpened');
    }

    public function closeMapModal()
    {
        $this->showMapModal = false;
    }

    public function submitRating()
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

        // Check if this user already rated this help (prevent duplicate from same user)
        if (\App\Models\Rating::hasRated($this->help->id, auth()->id(), 'customer_to_mitra')) {
            session()->flash('error', 'Anda sudah memberikan rating untuk pesanan ini.');
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
            'user_id' => auth()->id(), // Legacy field
            'mitra_id' => $this->help->mitra_id, // Legacy field
            'rater_id' => auth()->id(), // New field: who gives rating (customer)
            'ratee_id' => $this->help->mitra_id, // New field: who receives rating (mitra)
            'type' => 'customer_to_mitra',
            'rating' => $this->rating,
            'review' => $this->review,
            'is_anonymous' => $this->is_anonymous,
        ]);

        // Reset form
        $this->rating = 0;
        $this->review = '';
        $this->is_anonymous = false;
        $this->showRatingForm = false;

        // Reload help
        $this->loadHelp();

        session()->flash('success', 'Terima kasih atas rating Anda!');
    }

    public function setRating($value)
    {
        $this->rating = $value;
    }

    public function openComplaintModal()
    {
        $this->complaint_reason = '';
        $this->complaint_photo = null;
        $this->resetErrorBag();
        $this->showComplaintModal = true;
    }

    public function closeComplaintModal()
    {
        $this->showComplaintModal = false;
        $this->complaint_reason = '';
        $this->complaint_photo = null;
        $this->resetErrorBag();
    }

    public function submitComplaint()
    {
        if ($this->help->status !== 'waiting_customer_confirmation') {
            session()->flash('error', 'Komplain hanya dapat diajukan saat pesanan menunggu konfirmasi penyelesaian.');
            return;
        }

        $this->validate([
            'complaint_photo' => 'required|image|max:5120',
            'complaint_reason' => 'required|string|min:10|max:1000',
        ], [
            'complaint_photo.required' => 'Wajib mengunggah foto bukti ketidaksesuaian hasil kerja.',
            'complaint_photo.image' => 'File bukti harus berupa gambar (JPG, PNG, WebP).',
            'complaint_photo.max' => 'Ukuran foto maksimal 5 MB.',
            'complaint_reason.required' => 'Wajib mengisi alasan/penjelasan komplain.',
            'complaint_reason.min' => 'Alasan komplain minimal 10 karakter.',
            'complaint_reason.max' => 'Alasan komplain maksimal 1000 karakter.',
        ]);

        $photoPath = $this->complaint_photo->store('complaint_photos', 'public');

        $this->help->update([
            'status' => 'komplain',
            'complaint_photo' => $photoPath,
            'complaint_reason' => $this->complaint_reason,
            'complaint_submitted_at' => now(),
        ]);

        $this->showComplaintModal = false;
        $this->loadHelp();

        // 1. Notifikasi ke Mitra bahwa pesanan sedang dikomplain customer
        if ($this->help->mitra_id) {
            $mitra = \App\Models\User::find($this->help->mitra_id);
            if ($mitra) {
                try {
                    $mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, 'waiting_customer_confirmation', 'komplain', $mitra));
                } catch (\Exception $e) {}
            }
        }

        // 2. Notifikasi ke Admin Kota yang mengelola kota bantuan ini
        try {
            $cityId = $this->help->city_id;
            $cityAdmins = \App\Models\User::where('role', 'admin')
                ->where('status', 'active')
                ->where(function ($query) use ($cityId) {
                    if ($cityId) {
                        $query->whereHas('managedCities', function ($q) use ($cityId) {
                            $q->where('cities.id', $cityId);
                        })->orWhere('city_id', $cityId);
                    }
                })
                ->get();

            // 3. Notifikasi ke seluruh Super Admin
            $superAdmins = \App\Models\User::where('role', 'super_admin')
                ->where('status', 'active')
                ->get();

            $allAdminsToNotify = $cityAdmins->merge($superAdmins)->unique('id');

            foreach ($allAdminsToNotify as $adminUser) {
                try {
                    $adminUser->notify(new \App\Notifications\HelpStatusNotification($this->help, 'waiting_customer_confirmation', 'komplain', $this->help->mitra));
                } catch (\Exception $e) {}
            }
        } catch (\Exception $e) {
            Log::warning('Failed sending complaint notification to admins: ' . $e->getMessage());
        }

        session()->flash('message', 'Komplain berhasil diajukan! Pesanan Anda sedang ditinjau oleh Admin.');
        $this->dispatch('show-status-notification', message: 'Komplain diajukan! Notifikasi terkirim ke Admin & Super Admin.');
    }

    public function confirmCompletion()
    {
        // Only allow confirmation if status is waiting_customer_confirmation
        if ($this->help->status !== 'waiting_customer_confirmation') {
            session()->flash('error', 'Status pesanan tidak valid untuk konfirmasi.');
            return;
        }

        $this->help->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);

        if ($this->help->mitra_id) {
            $mitra = \App\Models\User::find($this->help->mitra_id);
            if ($mitra) {
                $mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, 'waiting_customer_confirmation', 'selesai', $mitra));
            }
        }

        $this->loadHelp();

        session()->flash('success', 'Pesanan telah dikonfirmasi selesai!');
    }

    public function handleStatusChanged($data)
    {
        // Reload help data saat status berubah dari GPS tracking
        if (isset($data['helpId']) && $data['helpId'] == $this->helpId) {
            $this->loadHelp();
            
            // Dispatch notification ke frontend
            $this->dispatch('show-status-notification', [
                'message' => $this->getStatusNotificationMessage($data['newStatus'])
            ]);
        }
    }

    private function getStatusNotificationMessage($status)
    {
        return match($status) {
            'partner_on_the_way' => '🚗 Rekan jasa sedang menuju lokasi Anda',
            'partner_arrived' => '📍 Rekan jasa telah tiba di lokasi',
            'in_progress' => '⚙️ Pekerjaan sedang dikerjakan',
            'waiting_customer_confirmation' => '✋ Menunggu konfirmasi Anda untuk menyelesaikan pesanan',
            'komplain', 'disputed' => '⚠️ Pesanan dalam mediasi komplain',
            'completed', 'selesai' => '✅ Pesanan telah selesai',
            default => 'Status pesanan diperbarui'
        };
    }

    public function getStatusColorProperty()
    {
        return match($this->help->status) {
            'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-700',
            'mencari_mitra', 'menunggu_mitra' => 'bg-blue-100 text-blue-700',
            'taken' => 'bg-blue-100 text-blue-700',
            'partner_on_the_way' => 'bg-blue-100 text-blue-700',
            'partner_arrived' => 'bg-green-100 text-green-700',
            'in_progress', 'sedang_diproses' => 'bg-cyan-100 text-cyan-700',
            'waiting_customer_confirmation' => 'bg-orange-100 text-orange-700',
            'komplain', 'disputed' => 'bg-red-100 text-red-800',
            'completed', 'selesai' => 'bg-green-100 text-green-700',
            'dibatalkan', 'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusTextProperty()
    {
        return match($this->help->status) {
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'mencari_mitra' => 'Mencari Rekan Jasa terdekat',
            'menunggu_mitra', 'memperoleh_mitra' => 'Menunggu Rekan Jasa berangkat',
            'taken' => 'Rekan Jasa mengambil pesanan',
            'partner_on_the_way' => 'Rekan Jasa menuju lokasi',
            'partner_arrived' => 'Rekan Jasa tiba di lokasi',
            'in_progress', 'sedang_diproses' => 'Pelayanan dalam proses',
            'waiting_customer_confirmation' => 'Menunggu konfirmasi customer',
            'komplain', 'disputed' => 'Dalam Mediasi Komplain',
            'completed', 'selesai' => 'Pesanan selesai',
            'dibatalkan', 'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->help->status)),
        };
    }

    public function render()
    {
        return view('livewire.customer.helps.detail')
            ->layout('layouts.app', ['title' => 'Detail Pesanan']);
    }
}
