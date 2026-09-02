<?php

namespace App\Livewire\Admin\Verifications;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Registration;
use App\Models\User;

#[Layout('layouts.admin')]
class IndexNew extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedId = null;
    public $showModal = false;
    public $previewPhoto = null;
    public $rejectReason = '';

    protected $listeners = ['refreshList' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function view($id)
    {
        $this->selectedId = $id;
        $this->showModal = true;
    }

    public function close()
    {
        $this->selectedId = null;
        $this->showModal = false;
        $this->rejectReason = '';
    }

    public function showPhoto($url)
    {
        $this->previewPhoto = $url;
    }

    public function closePreview()
    {
        $this->previewPhoto = null;
    }

    public function approve($id)
    {
        $reg = Registration::find($id);
        if (!$reg) {
            session()->flash('error','Registrasi tidak ditemukan');
            return;
        }
        $user = User::where('email', $reg->email)->first();
        if ($user) {
            $user->update(['status' => 'active', 'verified' => true, 'email_verified_at' => now()]);
        }
        $reg->update(['status' => 'approved']);
        session()->flash('message','Registrasi disetujui');
        $this->emit('refreshList');
        $this->close();
    }

    public function reject($id)
    {
        $reg = Registration::find($id);
        if (!$reg) {
            session()->flash('error','Registrasi tidak ditemukan');
            return;
        }
        $reg->update(['status' => 'rejected', 'reject_reason' => $this->rejectReason ?: null]);
        $user = User::where('email', $reg->email)->first();
        if ($user) {
            $user->update(['status' => 'blocked']);
        }
        session()->flash('message','Registrasi ditolak');
        $this->emit('refreshList');
        $this->close();
    }

    public function render()
    {
        $query = Registration::query();
        
        if ($this->search) {
            $query->where(function($q){
                $q->where('full_name','like','%'.$this->search.'%')
                  ->orWhere('email','like','%'.$this->search.'%')
                  ->orWhere('nik','like','%'.$this->search.'%');
            });
        }

        // Filter by admin's city if user is admin
        if (auth()->user() && auth()->user()->role === 'admin' && auth()->user()->city_id) {
            $query->where('city_id', auth()->user()->city_id);
        }

        $registrations = $query->latest()->paginate($this->perPage);

        return view('admin.verifications_new', compact('registrations'));
    }
}
