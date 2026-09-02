<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Help;
use App\Notifications\HelpStatusNotification;

#[Layout('layouts.superadmin')]
class HelpsApproved extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Modal reject
    public $showRejectModal = false;
    public $rejectingHelpId = null;
    public $rejectingHelpTitle = '';
    public $rejectionReason = '';

    // Modal detail
    public $showDetailModal = false;
    public $detailHelp = null;

    public function openDetailModal($id)
    {
        $this->detailHelp = Help::with(['customer', 'mitra', 'category', 'city'])->find($id);
        if ($this->detailHelp) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailHelp = null;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function approveHelp($id)
    {
        $help = Help::findOrFail($id);
        $help->update(['status' => 'active']);
        session()->flash('message', 'Bantuan berhasil disetujui');
    }

    public function openRejectModal($id)
    {
        $help = Help::findOrFail($id);
        if (in_array($help->status, ['selesai', 'completed', 'dibatalkan', 'cancelled', 'rejected'])) {
            session()->flash('error', 'Bantuan tidak bisa ditolak karena sudah selesai atau dibatalkan.');
            return;
        }
        $this->rejectingHelpId = $id;
        $this->rejectingHelpTitle = $help->title;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function openRejectModalFromDetail($id)
    {
        $this->showDetailModal = false;
        $this->detailHelp = null;
        $this->openRejectModal($id);
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectingHelpId = null;
        $this->rejectingHelpTitle = '';
        $this->rejectionReason = '';
    }

    public function confirmReject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:5|max:500',
        ], [
            'rejectionReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectionReason.min' => 'Alasan minimal 5 karakter.',
        ]);

        $help = Help::with('customer')->findOrFail($this->rejectingHelpId);

        if (in_array($help->status, ['selesai', 'completed', 'dibatalkan', 'cancelled', 'rejected'])) {
            session()->flash('error', 'Bantuan tidak bisa ditolak karena sudah selesai atau dibatalkan.');
            $this->closeRejectModal();
            return;
        }

        $oldStatus = $help->status;

        $help->update([
            'status' => 'rejected',
            'admin_notes' => $this->rejectionReason,
        ]);

        // Send notification to customer user
        $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
        if ($customer) {
            try {
                $customer->notify(new HelpStatusNotification($help, $oldStatus, 'rejected'));
                \Log::info('HelpsApproved sent rejection notification to customer id=' . $customer->id . ' for help_id=' . $help->id);
            } catch (\Exception $e) {
                \Log::warning('Failed to send rejection notification: ' . $e->getMessage());
            }
        }

        session()->flash('message', 'Bantuan "' . $help->title . '" berhasil ditolak.');
        $this->closeRejectModal();
        $this->closeDetailModal();
    }

    public function approveRefund($id)
    {
        $help = Help::with(['customer', 'mitra'])->findOrFail($id);
        $oldStatus = $help->status;

        $help->update([
            'status' => 'dibatalkan',
            'complaint_resolved_at' => now(),
            'complaint_resolution' => 'refunded',
            'complaint_admin_notes' => 'Disetujui oleh Super Admin',
        ]);

        // Notify customer
        try {
            $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
            if ($customer) {
                $customer->notify(new HelpStatusNotification($help, $oldStatus, 'dibatalkan', $customer));
            }
        } catch (\Throwable $e) {}

        // Notify mitra
        try {
            if ($help->mitra) {
                $help->mitra->notify(new HelpStatusNotification($help, $oldStatus, 'dibatalkan', $help->mitra));
            }
        } catch (\Throwable $e) {}

        session()->flash('message', 'Komplain bantuan #' . $help->id . ' disetujui. Dana sebesar Rp ' . number_format($help->amount + ($help->admin_fee ?? 0), 0, ',', '.') . ' telah dikembalikan ke saldo customer.');
        $this->closeDetailModal();
    }

    public function rejectComplaint($id)
    {
        $help = Help::with(['customer', 'mitra'])->findOrFail($id);
        $oldStatus = $help->status;

        $help->update([
            'status' => 'selesai',
            'completed_at' => $help->completed_at ?? now(),
            'complaint_resolved_at' => now(),
            'complaint_resolution' => 'rejected',
            'complaint_admin_notes' => 'Ditolak oleh Super Admin (Pekerjaan disahkan selesai)',
        ]);

        // Notify customer
        try {
            $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
            if ($customer) {
                $customer->notify(new HelpStatusNotification($help, $oldStatus, 'selesai', $customer));
            }
        } catch (\Throwable $e) {}

        // Notify mitra
        try {
            if ($help->mitra) {
                $help->mitra->notify(new HelpStatusNotification($help, $oldStatus, 'selesai', $help->mitra));
            }
        } catch (\Throwable $e) {}

        session()->flash('message', 'Komplain bantuan #' . $help->id . ' ditolak. Pesanan dinyatakan selesai dan dana sebesar Rp ' . number_format($help->amount, 0, ',', '.') . ' telah dicairkan ke saldo mitra.');
        $this->closeDetailModal();
    }

    public function render()
    {
        $helps = Help::query()
            ->with(['customer', 'city', 'category'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('superadmin.helps-approved', compact('helps'));
    }
}
