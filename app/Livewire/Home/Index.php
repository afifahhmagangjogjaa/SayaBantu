<?php

namespace App\Livewire\Home;

use App\Models\City;
use App\Models\Help;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedCity = null;
    public $search = '';

    public function render()
    {
        $helps = Help::with(['user', 'city', 'mitra'])
            ->where('status', 'approved')
            ->whereNull('mitra_id')
            ->when($this->selectedCity, function ($query) {
                $query->where('city_id', $this->selectedCity);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        $cities = City::where('is_active', true)->get();

        return view('livewire.home.index', [
            'helps' => $helps,
            'cities' => $cities,
        ]);
    }

    public function updatedSelectedCity()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function takeHelp($helpId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isMitra()) {
            session()->flash('error', 'Hanya mitra yang dapat mengambil bantuan.');
            return;
        }

        if (!auth()->user()->isProfileComplete()) {
            $missing = implode(', ', auth()->user()->getMissingProfileFields());
            session()->flash('error', "Harap lengkapi biodata profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.");
            return redirect()->route('mitra.profile.edit');
        }

        if (!auth()->user()->verified) {
            session()->flash('error', 'Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi disetujui sebelum dapat mengambil bantuan.');
            return;
        }

        if (!auth()->user()->canTakeMoreOrders()) {
            if (!auth()->user()->hasVerifiedEmail()) {
                session()->flash('error', 'Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 bantuan. Silakan verifikasi email Anda terlebih dahulu untuk mengambil bantuan lagi.');
            } else {
                session()->flash('error', 'Anda telah mencapai batas maksimal order bantuan.');
            }
            return;
        }

        $help = Help::findOrFail($helpId);

        if ($help->mitra_id) {
            session()->flash('error', 'Bantuan ini sudah diambil oleh mitra lain.');
            return;
        }

        $help->update([
            'mitra_id' => auth()->id(),
            'status' => 'taken',
            'taken_at' => now(),
        ]);

        session()->flash('message', 'Bantuan berhasil diambil! Segera hubungi yang membutuhkan.');
        return redirect()->route('helps.index');
    }
}
