<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.superadmin')]
class ChangePassword extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'different:current_password', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }

    protected $messages = [
        'current_password.required' => 'Kata sandi saat ini wajib diisi.',
        'current_password.current_password' => 'Kata sandi saat ini tidak sesuai / salah.',
        'password.required' => 'Kata sandi baru wajib diisi.',
        'password.min' => 'Kata sandi baru minimal 8 karakter.',
        'password.regex' => 'Kata sandi baru harus mengandung huruf besar, angka, dan karakter khusus / simbol.',
        'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
        'password_confirmation.required' => 'Konfirmasi kata sandi baru wajib diisi.',
        'password_confirmation.same' => 'Konfirmasi kata sandi baru tidak cocok.',
    ];

    public $saved = false;

    public function updated($propertyName)
    {
        $this->saved = false;

        if ($propertyName === 'password') {
            $this->validateOnly('password');
            if (!empty($this->password_confirmation)) {
                $this->validateOnly('password_confirmation');
            }
        } elseif ($propertyName === 'password_confirmation') {
            $this->validateOnly('password_confirmation');
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function updatePassword()
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->saved = true;

        session()->flash('success', 'Password baru tersimpan! Silakan gunakan kata sandi baru untuk login berikutnya.');
    }

    public function render()
    {
        return view('livewire.super-admin.change-password', [
            'user' => Auth::user(),
        ]);
    }
}
