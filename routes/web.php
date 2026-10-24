<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SimpleRegisterController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Public landing page
Route::view('/', 'welcome')->name('home');
Route::view('/welcome', 'welcome')->name('welcome');

// Rejected registration page (public)
Route::get('/rejected/{registration}', [\App\Http\Controllers\Auth\RejectedController::class, 'show'])->name('auth.rejected');

// Public page with the existing helps listing
Route::view('/bantuan', 'home')->name('public.helps');

// =========================================================================
// 1. REGISTRASI AWAL (HANYA INPUT EMAIL)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [SimpleRegisterController::class, 'show'])->name('register');
    Route::post('/register', [SimpleRegisterController::class, 'store']);
});

// =========================================================================
// 2. PROSES VERIFIKASI EMAIL
// =========================================================================
// Link verifikasi yang diklik user dari inbox email (Signed URL - auto login & redirect ke dashboard)
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Tautan verifikasi tidak valid atau telah kedaluwarsa.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    // Login-kan user secara otomatis
    \Illuminate\Support\Facades\Auth::login($user);

    if ($user->role === 'mitra') {
        return redirect()->route('mitra.dashboard')->with('success', 'Email berhasil diverifikasi! Kuota order Anda kini tanpa batas.');
    }

    return redirect()->route('customer.dashboard')->with('success', 'Email berhasil diverifikasi!');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::middleware('auth')->group(function () {
    // Halaman pemberitahuan cek email
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Kirim ulang link verifikasi
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        $email = $request->user()->email;
        $msg = "Tautan verifikasi berhasil dikirim ke $email! Silakan periksa kotak masuk atau folder spam Anda.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()
            ->with('success', $msg)
            ->with('message', $msg);
    })->middleware('throttle:6,1')->name('verification.send');

    // Cek status verifikasi secara realtime (polling background)
    Route::get('/email/check-verification-status', function () {
        $user = auth()->user();
        if ($user && $user->hasVerifiedEmail()) {
            $redirectUrl = ($user->role === 'mitra') ? route('mitra.dashboard') : route('customer.dashboard');
            return response()->json([
                'verified' => true,
                'redirect' => $redirectUrl
            ]);
        }
        return response()->json(['verified' => false]);
    })->name('verification.check-status');
});

// Helper Public API Wilayah Proxy
Route::get('/api/wilayah/districts', [\App\Http\Controllers\Api\RegionController::class, 'getDistricts'])->name('api.wilayah.districts');
Route::get('/api/wilayah/villages', [\App\Http\Controllers\Api\RegionController::class, 'getVillages'])->name('api.wilayah.villages');

// =========================================================================
// 3. ONBOARDING (4 STEP DATA DIRI & DOKUMEN KTP)
// =========================================================================
Route::group([], function () {
    // Batalkan Onboarding & Kembali ke Register Awal
    Route::post('/onboarding/cancel', [OnboardingController::class, 'cancel'])->name('onboarding.cancel');

    // Buat Password
    Route::get('/onboarding/password', [OnboardingController::class, 'showPasswordForm'])->name('onboarding.password');
    Route::post('/onboarding/password', [OnboardingController::class, 'storePassword'])->name('onboarding.password.store');

    // Step 1: Data Diri KTP
    Route::get('/onboarding/step1', [OnboardingController::class, 'showStep1'])->name('onboarding.step1');
    Route::post('/onboarding/step1', [OnboardingController::class, 'storeStep1'])->name('onboarding.step1.store');
    Route::get('/onboarding/biodata', [OnboardingController::class, 'showStep1'])->name('onboarding.biodata');
    Route::post('/onboarding/biodata', [OnboardingController::class, 'storeStep1'])->name('onboarding.biodata.store');

    // Step 2: Upload Foto KTP
    Route::get('/onboarding/step2', [OnboardingController::class, 'showStep2'])->name('onboarding.step2');
    Route::post('/onboarding/step2', [OnboardingController::class, 'storeStep2'])->name('onboarding.step2.store');

    // Step 3: Upload Foto Selfie
    Route::get('/onboarding/step3', [OnboardingController::class, 'showStep3'])->name('onboarding.step3');
    Route::post('/onboarding/step3', [OnboardingController::class, 'storeStep3'])->name('onboarding.step3.store');

    // Step 4: Review & Konfirmasi
    Route::get('/onboarding/step4', [OnboardingController::class, 'showStep4'])->name('onboarding.step4');
    Route::post('/onboarding/step4', [OnboardingController::class, 'storeStep4'])->name('onboarding.step4.store');
});

// =========================================================================
// 4. AUTHENTICATED ROUTES (DIKUNCI DENGAN MIDDLEWARE ONBOARDED)
// =========================================================================
Route::middleware(['auth', 'onboarded'])->group(function () {
    // Main Dashboard route - redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'mitra') {
            return redirect()->route('mitra.dashboard');
        } elseif ($user->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default: redirect to customer dashboard
        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Chat shortcut routes (open a specific conversation)
    Route::get('/chat/start', [\App\Http\Controllers\ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/{help}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');

    // Lightweight AJAX endpoints for authenticated users
    Route::get('/ajax/cities', [\App\Http\Controllers\Api\CityController::class, 'search'])->name('ajax.cities');
    Route::get('/check-account-status', function () {
        $user = auth()->user()?->fresh() ?? auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 'logged_out',
                'is_blocked' => false,
                'is_inactive' => false,
                'should_logout' => true,
            ]);
        }

        $isBlocked = $user->isBanned() || ($user->status === 'blocked');
        $isInactive = ($user->status === 'inactive');

        if ($isBlocked || $isInactive) {
            // Hapus total seluruh sesi aktif pengguna dari database dan file sesi
            $user->purgeSessions();
            \Illuminate\Support\Facades\Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return response()->json([
            'status' => $user->status,
            'is_blocked' => $isBlocked,
            'is_inactive' => $isInactive,
            'should_logout' => ($isBlocked || $isInactive),
        ]);
    })->name('account.status.check');

    // Halaman Tersendiri Detail Surat Peringatan (SP)
    Route::get('/notifications/sanction/{id}', [\App\Http\Controllers\SanctionNotificationController::class, 'show'])->name('notifications.sanction');
    Route::match(['get', 'post'], '/notifications/sanction/{id}/acknowledge', [\App\Http\Controllers\SanctionNotificationController::class, 'acknowledge'])->name('notifications.sanction.acknowledge');

    // ========================================
    // SHARED ROUTES (Profile & Account)
    // ========================================
    Route::view('/profile', 'profile')->name('profile');
    Route::view('/profile/edit', 'profile.edit')->name('profile.edit');
    Route::view('/profile/settings', 'profile.settings')->name('profile.settings');
    Route::view('/profile/settings/notifications', 'profile.settings.notifications')->name('profile.settings.notifications');
    Route::view('/profile/settings/password', 'profile.settings.password')->name('profile.settings.password');
    Route::view('/profile/settings/verification', 'profile.settings.verification')->name('profile.settings.verification');

    Route::put('/profile/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed', 'different:current_password', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai / salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru harus mengandung huruf besar, angka, dan karakter khusus / simbol.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui! Silakan gunakan kata sandi baru saat login.'
            ]);
        }

        return back()->with('status', 'Kata sandi berhasil diperbarui! Silakan gunakan kata sandi baru saat login.');
    })->name('profile.password.update');

    Route::delete('/profile', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();

        return redirect('/')->with('status', 'Account deleted successfully!');
    })->name('profile.delete');
});

// Rute per role dipisah ke file masing-masing
require __DIR__ . '/customer.php';
require __DIR__ . '/mitra.php';
require __DIR__ . '/superadmin.php';
require __DIR__ . '/admin.php';

// Smart Address Autocomplete API (OpenStreetMap & Photon)
Route::get('/api/address/autocomplete', [\App\Http\Controllers\Api\AddressController::class, 'search'])->name('api.address.autocomplete');

// Public callback endpoint for withdraw disbursements
Route::post('/gateway/callback', [\App\Http\Controllers\WithdrawController::class, 'gatewayCallback'])->name('gateway.callback');

require __DIR__ . '/auth.php';
