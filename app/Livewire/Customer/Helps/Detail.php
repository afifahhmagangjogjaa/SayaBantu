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
    public $showReassignConfirm = false;
    public $selectedCancelReason = '';
    public $customCancelReason = '';
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
        'status-changed' => 'handleStatusChanged',
        'open-cancel-modal' => 'confirmCancel',
    ];

    public function mount($id)
    {
        $this->helpId = $id;
        $this->loadHelp();

        if (request()->query('action') === 'cancel') {
            $this->confirmCancel();
        }
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

        // Tandai peringatan rekan jasa belum berangkat sebagai sudah dilihat sehingga tidak muncul lagi di halaman mana pun
        if ($this->help && $this->help->mitra_id) {
            session()->put('idle_alert_ack_' . $this->help->id . '_' . $this->help->mitra_id, true);
        }

        // Auto-cancel if urgent help is expired
        if ($this->help->help_type === 'urgent' && $this->help->status === 'menunggu_mitra' && !$this->help->mitra_id && $this->help->auto_cancel_at && now()->gte($this->help->auto_cancel_at)) {
            $this->help->update([
                'status' => 'dibatalkan',
                'cancelled_by' => 'system',
                'customer_cancel_reason' => 'Otomatis dibatalkan sistem karena batas waktu tunggu pencarian mitra telah habis.',
                'cancelled_at' => now(),
            ]);
            $this->help->refresh();
        }

        // Auto-confirm jika status waiting_customer_confirmation sudah melewati 24 jam
        if ($this->help->status === 'waiting_customer_confirmation' && $this->help->isCustomerConfirmationExpired()) {
            $this->help->autoConfirmIfExpired();
            $this->help->refresh();
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
        $this->selectedCancelReason = '';
        $this->customCancelReason = '';
        $this->resetErrorBag();
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

    public function getAvailableCancelReasonsProperty(): array
    {
        if ($this->help->mitra_id) {
            return [
                'Rekan Jasa tidak kunjung berangkat / tidak bergerak',
                'Rekan Jasa tidak membalas chat / tidak bisa dihubungi',
                'Waktu kedatangan Rekan Jasa terlalu lama',
                'Rekan Jasa meminta pesanan dibatalkan',
                'Lainnya',
            ];
        }

        return [
            'Terlalu lama menunggu Rekan Jasa ditemukan',
            'Ingin mengubah rincian bantuan / jadwal',
            'Sudah tidak membutuhkan bantuan lagi',
            'Lainnya',
        ];
    }

    public function cancelHelp()
    {
        try {
            // Proteksi pembatalan (wajib memenuhi syarat canCustomerCancel)
            if (!$this->canCustomerCancel) {
                session()->flash('error', 'Pesanan sedang ditangani oleh Rekan Jasa dan belum dapat dibatalkan.');
                $this->showCancelConfirm = false;
                return;
            }

            // Validasi alasan pembatalan
            if (empty($this->selectedCancelReason)) {
                $this->addError('selectedCancelReason', 'Silakan pilih salah satu alasan pembatalan.');
                return;
            }

            $finalReason = $this->selectedCancelReason;
            if ($this->selectedCancelReason === 'Lainnya') {
                $customText = trim($this->customCancelReason);
                if (empty($customText)) {
                    $this->addError('customCancelReason', 'Silakan tuliskan alasan pembatalan Anda.');
                    return;
                }
                $finalReason = $customText;
            }

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
                'customer_cancel_reason' => $finalReason,
                'cancelled_by' => 'customer',
                'cancelled_at' => now(),
            ]);

            // Log activity
            Log::info('Help cancelled by customer', [
                'help_id' => $this->help->id,
                'user_id' => auth()->id(),
                'mitra_id' => $assignedMitra?->id,
                'previous_status' => $prevStatus,
                'reason' => $finalReason,
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
        $this->showReassignConfirm = false;
        $this->selectedCancelReason = '';
        $this->customCancelReason = '';
        $this->resetErrorBag();
    }

    public function confirmReassign()
    {
        $this->showReassignConfirm = true;
    }

    public function closeReassignModal()
    {
        $this->showReassignConfirm = false;
    }

    public function reassignPartner()
    {
        try {
            if (!$this->canCustomerCancel) {
                session()->flash('error', 'Pesanan sedang ditangani dan belum dapat dialihkan.');
                $this->showReassignConfirm = false;
                return;
            }

            if (!$this->help->mitra_id) {
                session()->flash('error', 'Pesanan belum memiliki rekan jasa.');
                $this->showReassignConfirm = false;
                return;
            }

            $reason = ($this->help->status === 'partner_on_the_way')
                ? 'Customer mencari rekan jasa lain karena keterlambatan keberangkatan (>30 menit)'
                : 'Customer mencari rekan jasa lain karena belum ada tanda keberangkatan';

            $success = $this->help->reassignToNewPartner($reason);

            if ($success) {
                session()->flash('success', 'Rekan Jasa berhasil diganti! Sistem sedang mencarikan Rekan Jasa baru untuk pesanan Anda.');
                $this->showReassignConfirm = false;
                $this->loadHelp();
            } else {
                session()->flash('error', 'Gagal mengalihkan rekan jasa.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reassigning partner: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengalihkan rekan jasa.');
        }
    }

    // Computed property untuk mengecek apakah customer berhak membatalkan pesanan
    public function getCanCustomerCancelProperty()
    {
        // 1. Jika pesanan sudah selesai, dibatalkan, sedang aktif dikerjakan, atau rekan jasa sudah tiba di lokasi: TIDAK BISA dibatalkan
        if (in_array($this->help->status, [
            'selesai', 
            'completed', 
            'dibatalkan', 
            'cancelled', 
            'in_progress', 
            'sedang_diproses', 
            'partner_arrived', 
            'waiting_customer_confirmation'
        ]) || !empty($this->help->partner_arrived_at)) {
            return false;
        }

        // 2. Jika belum ada mitra / status masih mencari / menunggu mitra / menunggu pembayaran: BISA dibatalkan kapan saja
        if (!$this->help->mitra_id && in_array($this->help->status, ['menunggu_mitra', 'mencari_mitra', 'menunggu_pembayaran'])) {
            return true;
        }

        // 3. Mitra sudah mengambil pesanan: Tombol pengalihan/pembatalan terbuka jika mitra terlambat berangkat >= 30 menit (dan tetap terbuka sampai tiba di lokasi)
        if ($this->help->mitra_id || in_array($this->help->status, ['memperoleh_mitra', 'taken', 'partner_on_the_way'])) {
            return $this->help->canCustomerCancelDueToDelay();
        }

        return false;
    }

    // Pesan keterangan dinamis pembatas pembatalan untuk tampilan customer
    public function getCancelRestrictionMessageProperty(): string
    {
        if ($this->canCustomerCancel) {
            return '';
        }

        // Jika mitra sudah tiba di lokasi
        if (in_array($this->help->status, ['partner_arrived']) || !empty($this->help->partner_arrived_at)) {
            return 'Rekan Jasa sudah tiba di lokasi Anda. Pesanan tidak dapat dialihkan atau dibatalkan.';
        }

        // Jika mitra sedang mengerjakan atau selesai
        if (in_array($this->help->status, ['in_progress', 'sedang_diproses', 'waiting_customer_confirmation', 'selesai', 'completed'])) {
            return 'Pesanan sedang dikerjakan atau sudah selesai.';
        }

        // Jika mitra sedang menuju lokasi dan berangkat tepat waktu (< 30 menit)
        if ($this->help->status === 'partner_on_the_way') {
            return 'Rekan Jasa sedang dalam perjalanan menuju lokasi Anda (berangkat tepat waktu).';
        }

        if ($this->help->mitra_id || in_array($this->help->status, ['memperoleh_mitra', 'taken'])) {
            if ($this->help->isScheduled() && $this->help->scheduled_at) {
                $scheduledAt = \Carbon\Carbon::parse($this->help->scheduled_at);
                $journeyAvailableAt = $this->help->partnerJourneyAvailableAt() ?? $scheduledAt->copy()->subHour();

                // Jika belum masuk jam persiapan/keberangkatan (H-1 jam sebelum jadwal)
                if (now()->lt($journeyAvailableAt)) {
                    return "Pesanan dijadwalkan pada " . $scheduledAt->format('d M Y, H:i') . ". Rekan Jasa akan mulai bersiap dan berangkat menjelang waktu jadwal (mulai pukul " . $journeyAvailableAt->format('H:i') . ").";
                }

                // Jika tombol sudah nyala, hitung sisa menit dari toleransi 30 menit
                $effectiveStartTime = $this->help->getExpectedJourneyStartTime() ?? $journeyAvailableAt;
                $diffMinutes = (int) $effectiveStartTime->diffInMinutes(now());
                $remaining = max(1, 30 - $diffMinutes);
                return "Tombol pengalihan/pembatalan akan muncul jika Rekan Jasa belum berangkat setelah 30 menit (tersisa {$remaining} menit lagi).";
            }

            $maxWaitMinutes = 30;
            $takenTime = $this->help->getExpectedJourneyStartTime();
            $diffMinutes = $takenTime ? (int) $takenTime->diffInMinutes(now()) : 0;
            $remaining = (int) max(1, ceil($maxWaitMinutes - $diffMinutes));

            return "Tombol pembatalan/pengalihan akan muncul setelah 30 menit sejak bantuan diambil oleh Rekan Jasa jika belum berangkat (tersisa {$remaining} menit lagi).";
        }

        return 'Pesanan sedang ditangani oleh Rekan Jasa.';
    }
    
    public function showTrackingMap()
    {
        // Reload help data to get latest coordinates
        $this->loadHelp();

        // Check if partner is on the way or at nearby statuses
        if (!in_array($this->help->status, ['taken', 'partner_on_the_way', 'partner_arrived']) || ($this->help->status === 'taken' && !$this->help->canPartnerStartJourney())) {
            session()->flash('error', 'Tracking tersedia saat mitra mulai menuju lokasi (mulai 1 jam sebelum jadwal pelaksanaan).');
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
            $cityAdmins = \App\Models\User::getAdminsForCity($cityId);

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

        \Illuminate\Support\Facades\DB::transaction(function () {
            // 1. Update status bantuan menjadi selesai
            // HelpObserver secara otomatis mencairkan dana ke mitra dan mencatat riwayat transaksi
            $this->help->update([
                'status' => 'selesai',
                'completed_at' => now(),
            ]);

            // 2. Safety fallback: Jika belum dicairkan oleh HelpObserver
            if ($this->help->mitra_id) {
                $already = \App\Models\BalanceTransaction::where('user_id', $this->help->mitra_id)
                    ->where(function ($q) {
                        $q->where('reference_id', $this->help->id)
                          ->orWhere(function ($sq) {
                              if (!empty($this->help->order_id)) {
                                  $sq->where('order_id', $this->help->order_id);
                              }
                          })
                          ->orWhere('description', 'like', 'Pendapatan Bantuan #' . $this->help->id . '%')
                          ->orWhere('description', 'like', 'Auto-confirm Bantuan #' . $this->help->id . '%');
                    })
                    ->exists();

                if (!$already) {
                    $payoutAmount = $this->help->getMitraPayoutAmount();

                    if ($payoutAmount > 0) {
                        $mitraBalance = \App\Models\UserBalance::firstOrCreate(
                            ['user_id' => $this->help->mitra_id],
                            ['balance' => 0]
                        );

                        $feeText = ($this->help->mitra_fee_amount > 0)
                            ? " (Potongan Platform {$this->help->mitra_fee_percent}%: Rp " . number_format($this->help->mitra_fee_amount, 0, ',', '.') . ")"
                            : "";

                        $mitraBalance->addBalance($payoutAmount, "Pendapatan Bantuan #{$this->help->id}" . $feeText, $this->help->id);
                    }
                }

                // Notifikasi ke mitra
                $mitra = \App\Models\User::find($this->help->mitra_id);
                if ($mitra) {
                    try {
                        $mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, 'waiting_customer_confirmation', 'selesai', $mitra));
                    } catch (\Exception $e) {
                        Log::warning('Gagal kirim notifikasi selesai ke mitra: ' . $e->getMessage());
                    }
                }
            }
        });

        $this->loadHelp();

        session()->flash('success', 'Pesanan telah dikonfirmasi selesai dan saldo telah dicairkan ke rekan jasa!');
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
        return view('livewire.customer.helps.detail', array_merge(get_object_vars($this), [
            'help' => $this->help,
            'statusColor' => $this->statusColor,
            'statusText' => $this->statusText,
            'canCustomerCancel' => $this->canCustomerCancel,
        ]))->layout('layouts.app', ['title' => 'Detail Pesanan']);
    }
}
