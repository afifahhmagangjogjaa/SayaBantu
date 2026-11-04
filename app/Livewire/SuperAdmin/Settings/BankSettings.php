<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Models\BankChannel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Pengaturan Saluran Bank & E-Wallet - Super Admin')]
class BankSettings extends Component
{
    public string $search = '';

    public string $categoryFilter = 'all'; // all, bank, ewallet

    public bool $showModal = false;

    public bool $isEditing = false;

    public ?int $editingId = null;

    public string $code = '';

    public string $name = '';

    public string $category = 'bank';

    public bool $is_active = true;

    public int $order = 0;

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:30|unique:bank_channels,code,' . $this->editingId,
            'name' => 'required|string|max:100',
            'category' => 'required|in:bank,ewallet',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }

    protected $messages = [
        'code.required' => 'Kode bank wajib diisi (contoh: BCA, DANA).',
        'code.unique' => 'Kode bank ini sudah terdaftar.',
        'name.required' => 'Nama lengkap bank wajib diisi.',
    ];

    public function openAddModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetValidation();
        $bank = BankChannel::findOrFail($id);
        $this->editingId = $bank->id;
        $this->code = $bank->code;
        $this->name = $bank->name;
        $this->category = $bank->category;
        $this->is_active = (bool) $bank->is_active;
        $this->order = (int) $bank->order;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->code = '';
        $this->name = '';
        $this->category = 'bank';
        $this->is_active = true;
        $this->order = 0;
    }

    public function save()
    {
        $this->code = strtoupper(trim($this->code));
        $this->name = trim($this->name);
        $this->validate();

        if ($this->isEditing && $this->editingId) {
            $bank = BankChannel::findOrFail($this->editingId);
            $bank->update([
                'code' => $this->code,
                'name' => $this->name,
                'category' => $this->category,
                'is_active' => $this->is_active,
                'order' => $this->order,
            ]);
            session()->flash('success', "Saluran bank '{$this->name}' berhasil diperbarui.");
        } else {
            BankChannel::create([
                'code' => $this->code,
                'name' => $this->name,
                'category' => $this->category,
                'is_active' => $this->is_active,
                'order' => $this->order,
            ]);
            session()->flash('success', "Bank '{$this->name}' berhasil ditambahkan ke daftar saluran transaksi.");
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id)
    {
        $bank = BankChannel::findOrFail($id);
        $bank->is_active = ! $bank->is_active;
        $bank->save();

        $statusText = $bank->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Status bank '{$bank->name}' berhasil {$statusText}.");
    }

    public function confirmDelete(int $id)
    {
        $bank = BankChannel::findOrFail($id);
        $this->deletingId = $bank->id;
        $this->deletingName = $bank->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingName = '';
    }

    public function delete()
    {
        if ($this->deletingId) {
            $bank = BankChannel::find($this->deletingId);
            if ($bank) {
                $name = $bank->name;
                $bank->delete();
                session()->flash('success', "Bank '{$name}' telah dihapus dari daftar saluran.");
            }
        }

        $this->cancelDelete();
    }

    public function render()
    {
        $query = BankChannel::query();

        if (! empty($this->search)) {
            $q = trim($this->search);
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        $channels = $query->orderBy('order')->orderBy('name')->get();

        $activeCount = BankChannel::where('is_active', true)->count();
        $inactiveCount = BankChannel::where('is_active', false)->count();

        return view('livewire.super-admin.settings.bank-settings', [
            'channels' => $channels,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'totalCount' => BankChannel::count(),
        ]);
    }
}

