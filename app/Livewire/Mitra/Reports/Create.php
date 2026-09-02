<?php

namespace App\Livewire\Mitra\Reports;

use App\Models\Help;
use App\Models\PartnerReport;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.mitra')]
class Create extends Component
{
    public $title = '';
    public $message = '';
    public $report_type = '';
    public $reported_user_id = null;
    public $reported_help_id = null;
    public $help_id = null; // For selecting help from dropdown
    // free-text fields
    public $reported_help_text = null;
    public $reported_user_text = null;
    public $selected_help_type = '';
    public $custom_help_type = '';
    public $custom_report_type = '';

    public $helpTypes = [
        '' => 'Pilih Jenis Bantuan (Opsional)',
        'pangan' => 'Pangan / Bahan Pokok',
        'obat' => 'Obat',
        'perbaikan' => 'Perbaikan Rumah',
        'uang' => 'Bantuan Uang',
        'lainnya' => 'Lainnya',
    ];

    public $reportTypes = [
        'pengguna_spam' => 'Pengguna Spam',
        'pengguna_kasar' => 'Pengguna Kasar',
        'data_tidak_valid' => 'Data Tidak Valid',
        'penipuan' => 'Penipuan',
        'pelanggaran_aturan' => 'Pelanggaran Aturan',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'required|string|min:10|max:2000',
        'report_type' => 'required|string',
        'custom_report_type' => 'nullable|string|max:255',
        'reported_user_id' => 'nullable|exists:users,id',
        'reported_help_id' => 'nullable|exists:helps,id',
        'reported_help_text' => 'nullable|string|max:255',
        'reported_user_text' => 'nullable|string|max:255',
        'selected_help_type' => 'nullable|string',
        'custom_help_type' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'title.required' => 'Judul laporan harus diisi',
        'message.required' => 'Pesan laporan harus diisi',
        'message.min' => 'Pesan minimal 10 karakter',
        'report_type.required' => 'Jenis laporan harus dipilih',
    ];

    public function mount($user_id = null, $help_id = null)
    {
        $targetUserId = request()->route('user_id') ?? request()->query('user_id') ?? $user_id;
        $targetHelpId = request()->route('help_id') ?? request()->query('help_id') ?? $help_id;

        if ($targetUserId) {
            $this->reported_user_id = $targetUserId;
            $user = \App\Models\User::find($targetUserId);
            if ($user && empty($this->reported_user_text)) {
                $this->reported_user_text = $user->name;
            }
        }

        if ($targetHelpId) {
            $this->reported_help_id = $targetHelpId;
            $this->help_id = $targetHelpId;
            $help = Help::find($targetHelpId);
            if ($help) {
                if ($help->user_id && !$this->reported_user_id) {
                    $this->reported_user_id = $help->user_id;
                }
                if ($help->user && empty($this->reported_user_text)) {
                    $this->reported_user_text = $help->user->name;
                }
                if (empty($this->reported_help_text)) {
                    $this->reported_help_text = $help->title;
                }
            }
        }

        // Cek apakah chat / bantuan / customer ini sudah pernah dilaporkan dan masih aktif
        if (auth()->check() && request()->query('new') !== '1') {
            $existingQuery = PartnerReport::where('reporter_id', auth()->id());
            
            if ($targetHelpId) {
                $existingQuery->where('reported_help_id', $targetHelpId);
            } elseif ($targetUserId) {
                $existingQuery->where('reported_user_id', $targetUserId);
            }

            $existing = $existingQuery->latest()->first();
            // Hanya redirect ke status jika laporan untuk target ini masih pending / in_progress (belum selesai)
            if ($existing && !in_array($existing->status, ['resolved', 'closed', 'rejected', 'dismissed'])) {
                return redirect()->route('mitra.reports.show', ['report' => $existing->id]);
            }
        }
    }

    public function updatedHelpId($value)
    {
        if ($value) {
            $help = Help::find($value);
            if ($help && $help->user_id) {
                $this->reported_user_id = $help->user_id;
            }
        }
    }

    public function submit()
    {
        $this->validate();

        // If help_id is selected, set reported_help_id
        if ($this->help_id) {
            $help = Help::find($this->help_id);
            if ($help) {
                $this->reported_help_id = $help->id;
                if ($help->user_id && !$this->reported_user_id) {
                    $this->reported_user_id = $help->user_id;
                }
                if (!$this->reported_help_text) {
                    $this->reported_help_text = $help->title;
                }
            }
        }

        if ($this->report_type === 'lainnya') {
            if (!empty(trim($this->custom_report_type))) {
                $this->report_type = trim($this->custom_report_type);
            }

            if (!empty(trim($this->custom_help_type))) {
                $this->reported_help_text = $this->custom_help_type;
            } elseif (empty(trim($this->reported_help_text))) {
                $this->addError('custom_help_type', 'Silakan isi jenis bantuan yang tidak tersedia pada pilihan.');
                return;
            }
        }

        $report = PartnerReport::create([
            'user_id' => $this->reported_user_id, // legacy column
            'reporter_id' => auth()->id(),
            'reported_user_id' => $this->reported_user_id,
            'reported_help_id' => $this->reported_help_id,
            'reported_help_text' => $this->reported_help_text,
            'reported_user_text' => $this->reported_user_text,
            'title' => $this->title,
            'message' => $this->message,
            'report_type' => $this->report_type,
            'category' => 'dari_mitra',
            'status' => 'pending',
        ]);

        session()->flash('message', 'Laporan aduan berhasil dikirim. Admin akan meninjau laporan Anda.');
        return redirect()->route('mitra.reports.show', ['report' => $report->id]);
    }

    public function render()
    {
        $helps = auth()->user()->takenHelps()->latest()->get();
        $customers = User::whereIn('role', ['customer', 'kustomer'])->get();

        return view('livewire.mitra.reports.create', [
            'helps' => $helps,
            'customers' => $customers,
        ]);
    }
}
