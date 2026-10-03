<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.blank')]
class AdminLogin extends Component
{
    public LoginForm $form;

    public function mount()
    {
        if (auth()->check()) {
            $user = auth()->user()->fresh() ?? auth()->user();
            if (in_array($user->status, ['inactive', 'blocked'])) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                return;
            }

            if ($user->role === 'super_admin') {
                return $this->redirect(route('superadmin.dashboard', absolute: false), navigate: false);
            } elseif ($user->role === 'admin') {
                return $this->redirect(route('admin.dashboard', absolute: false), navigate: false);
            }
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        \Log::info('Admin login attempt started');

        $this->validate();

        $this->form->authenticate();

        \Log::info('Authentication successful for user: ' . auth()->user()->email);
        \Log::info('User role: ' . auth()->user()->role);

        // Check if user is admin or super_admin
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            \Log::warning('User is not admin, logging out');
            Auth::logout();
            $this->addError('email', 'Akses ditolak. Halaman ini khusus Administrator.');
            $this->addError('form.email', 'Akses ditolak. Halaman ini khusus Administrator.');
            return;
        }

        \Log::info('Admin login successful, redirecting to dashboard');
        \Log::info('Current user role for redirect: ' . auth()->user()->role);

        Session::regenerate();

        // Redirect based on role with relative path
        if (auth()->user()->role === 'super_admin') {
            \Log::info('Redirecting to superadmin dashboard');
            return $this->redirect(route('superadmin.dashboard', absolute: false), navigate: false);
        } else {
            \Log::info('Redirecting to admin dashboard');
            return $this->redirect(route('admin.dashboard', absolute: false), navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.auth.admin-login');
    }
}
