<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\AdminLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // Volt::route('register', 'pages.auth.register')
    //     ->name('register');

    // Choose role before starting registration
    Volt::route('register/choose-role', 'pages.auth.register-choose-role')
        ->name('register.choose-role');

    // Multi-step Registration Routes
    Volt::route('register/step1', 'pages.auth.register-step1')
        ->name('register.step1');

    Volt::route('register/step2', 'pages.auth.register-step2')
        ->name('register.step2');

    Volt::route('register/step3', 'pages.auth.register-step3')
        ->name('register.step3');

    Volt::route('register/step4', 'pages.auth.register-step4')
        ->name('register.step4');

    // Registration Success Page (masih guest karena baru register)
    Volt::route('registration/success', 'pages.auth.registration-success')
        ->name('registration.success');

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');
});

// Admin Login - accessible directly (handles authenticated/inactive states cleanly in mount())
Route::get('admin/login', AdminLogin::class)
    ->name('admin.login');

// Admin Forgot Password route: accessible directly (auto-logs out any stale session on mount)
Route::get('admin/forgot-password', \App\Livewire\Auth\AdminForgotPassword::class)
    ->name('admin.password.request');

// Password reset route: accessible directly from email link (auto-logs out any stale session on mount)
Volt::route('reset-password/{token}', 'pages.auth.reset-password')
    ->name('password.reset');

Route::middleware('auth')->group(function () {
    // Volt::route('verify-email', 'pages.auth.verify-email')
    //     ->name('verification.notice');

    // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    //     ->middleware(['signed', 'throttle:6,1'])
    //     ->name('verification.verify');


    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');

    // Admin: registration verification page
    Volt::route('admin/verification', 'pages.admin.verification')
        ->name('admin.verification');
});

// Robust Logout routes (supports both GET and POST, and handles admin separately)
Route::match(['get', 'post'], 'logout', function (\Illuminate\Http\Request $request) {
    $user = Auth::user();
    $userRole = $user->role ?? '';
    $status = $user->status ?? null;
    $isBanned = $user->is_banned ?? false;
    $warningLevel = $user->warning_level ?? 0;
    $isAdmin = in_array($userRole, ['admin', 'super_admin']) || $request->is('admin*');

    // If user was logging out under SP 3, ensure banned status is committed in DB
    if ($user && $warningLevel >= 3) {
        $user->is_banned = true;
        $user->status = 'blocked';
        $user->save();
    }

    if (Auth::check()) {
        Auth::logout();
    }
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($isAdmin) {
        $msg = $status === 'inactive' 
            ? 'Akun Admin Anda telah dinonaktifkan oleh Super Admin.' 
            : ($status === 'blocked' ? 'Akun Admin Anda telah diblokir oleh Super Admin.' : null);
        return $msg ? redirect(route('admin.login', absolute: false))->withErrors(['email' => $msg]) : redirect(route('admin.login', absolute: false));
    }

    if ($status === 'inactive') {
        return redirect(route('login', absolute: false))->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh administrator.']);
    } elseif ($status === 'blocked' || $isBanned || $warningLevel >= 3) {
        return redirect(route('login', absolute: false))->withErrors(['email' => 'Akun Anda telah diblokir/dinonaktifkan oleh administrator.']);
    }

    return redirect(route('login', absolute: false));
})->name('logout');

Route::match(['get', 'post'], 'admin/logout', function (\Illuminate\Http\Request $request) {
    $user = Auth::user();
    $status = $user->status ?? null;

    if (Auth::check()) {
        Auth::logout();
    }
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($status === 'inactive') {
        return redirect(route('admin.login', absolute: false))->withErrors(['email' => 'Akun Admin Anda telah dinonaktifkan oleh Super Admin.']);
    } elseif ($status === 'blocked') {
        return redirect(route('admin.login', absolute: false))->withErrors(['email' => 'Akun Admin Anda telah diblokir oleh Super Admin.']);
    }

    return redirect(route('admin.login', absolute: false));
})->name('admin.logout');

