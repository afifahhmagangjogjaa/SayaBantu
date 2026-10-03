<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.blank')]
class AdminForgotPassword extends Component
{
    public string $email = '';

    public function mount(): void
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }
    }

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Verifikasi bahwa email terdaftar dan memiliki akses admin/super_admin
        $user = User::where('email', $this->email)->first();
        if ($user && !in_array($user->role, ['admin', 'super_admin'])) {
            $this->addError('email', 'Email ini tidak terdaftar sebagai akun administrator.');
            return;
        }

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $msg = match ($status) {
                Password::INVALID_USER => 'Alamat email administrator tidak ditemukan.',
                Password::RESET_THROTTLED => 'Mohon tunggu beberapa saat sebelum meminta tautan baru.',
                default => 'Gagal mengirim link reset password. Silakan coba lagi nanti.',
            };
            $this->addError('email', $msg);
            return;
        }

        $this->reset('email');
        session()->flash('status', 'Tautan untuk mengubah password berhasil dikirim ke email Anda. Silakan periksa kotak masuk atau spam email Anda.');
    }

    public function render()
    {
        return view('livewire.auth.admin-forgot-password');
    }
}
