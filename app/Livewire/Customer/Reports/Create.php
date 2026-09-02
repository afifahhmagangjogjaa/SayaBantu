<?php

namespace App\Livewire\Customer\Reports;

use App\Models\Help;
use App\Models\PartnerReport;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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

    public $helpTypes = [
        '' => 'Pilih Jenis Bantuan (Opsional)',
        'pangan' => 'Pangan / Bahan Pokok',
        'obat' => 'Obat',
        'perbaikan' => 'Perbaikan Rumah',
        'uang' => 'Bantuan Uang',
        'lainnya' => 'Lainnya',
    ];

    public $reportTypes = [
        'mitra_berperilaku_buruk' => 'Mitra Berperilaku Buruk',
        'bantuan_fiktif' => 'Bantuan Fiktif',
        'penipuan' => 'Penipuan',
        'pelanggaran_aturan' => 'Pelanggaran Aturan',
        'konten_tidak_pantas' => 'Konten Tidak Pantas',
        'pelayanan_tidak_sesuai' => 'Pelayanan Tidak Sesuai',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'required|string|min:10|max:2000',
        'report_type' => 'required|string',
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
                if ($help->mitra_id && !$this->reported_user_id) {
                    $this->reported_user_id = $help->mitra_id;
                }
                if ($help->mitra && empty($this->reported_user_text)) {
                    $this->reported_user_text = $help->mitra->name;
                }
                if (empty($this->reported_help_text)) {
                    $this->reported_help_text = $help->title;
                }
            }
        }

        // Cek apakah chat / bantuan / mitra ini sudah pernah dilaporkan dan masih aktif
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
                return redirect()->route('customer.reports.show', ['report' => $existing->id]);
            }
        }
    }

    public function updatedHelpId($value)
    {
        if ($value) {
            $help = Help::find($value);
            if ($help && $help->mitra_id) {
                $this->reported_user_id = $help->mitra_id;
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
                if ($help->mitra_id && !$this->reported_user_id) {
                    $this->reported_user_id = $help->mitra_id;
                }
                if (!$this->reported_help_text) {
                    $this->reported_help_text = $help->title;
                }
            }
        }

        // If report_type == 'lainnya', accept custom_help_type as the help description
        if ($this->report_type === 'lainnya') {
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
            'category' => 'dari_customer',
            'status' => 'pending',
        ]);

        session()->flash('message', 'Laporan aduan berhasil dikirim. Admin akan meninjau laporan Anda.');
        return redirect()->route('customer.reports.show', ['report' => $report->id]);
    }

    public function render()
    {
        $user = auth()->user();
        $customerCityId = $user ? $user->city_id : null;

        // Bantuan milik customer
        $helps = $user ? $user->helps()->with('mitra')->latest()->get() : collect();

        // Filter mitra khusus di kota customer tersebut
        $mitrasQuery = User::where('role', 'mitra');
        if ($customerCityId) {
            $mitrasQuery->where('city_id', $customerCityId);
        }
        $mitras = $mitrasQuery->orderBy('name')->get();

        return view('livewire.customer.reports.create', [
            'helps' => $helps,
            'mitras' => $mitras,
            'customerCity' => $user?->city?->name ?? null,
        ]);
    }
}
