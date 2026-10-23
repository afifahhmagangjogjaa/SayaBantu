<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen
     */
    public function redirectToGoogle(Request $request)
    {
        // Hanya simpan intended role jika EKSPLISIT dikirim via query param
        // (artinya user klik dari halaman choose-role, bukan dari halaman login)
        $role = $request->query('role'); // null jika tidak ada param

        if ($role && in_array($role, ['customer', 'mitra'])) {
            session(['oauth_intended_role' => $role]);
        } else {
            // Klik dari halaman login — hapus intended role agar callback
            // bisa mendeteksi user baru dan redirect ke choose-role
            session()->forget('oauth_intended_role');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                return redirect()->route('login')->with('error', 'Gagal mendapatkan data akun dari Google.');
            }

            // Find existing user by google_id OR email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // If banned or blocked
                if ($user->status === 'banned' || $user->is_banned) {
                    return redirect()->route('login')->with('error', 'Akun ini telah dinonaktifkan / diblokir.');
                }

                // Update google_id and avatar if not yet set
                $updates = [];
                if (empty($user->google_id)) {
                    $updates['google_id'] = $googleUser->getId();
                }
                if (empty($user->email_verified_at)) {
                    $updates['email_verified_at'] = now();
                }
                if (!empty($googleUser->getAvatar()) && empty($user->avatar)) {
                    $updates['avatar'] = $googleUser->getAvatar();
                }
                if (!empty($updates)) {
                    $user->update($updates);
                }
            } else {
                // --- NEW USER ---
                $intendedRole = session('oauth_intended_role');

                // Jika user baru tapi tidak ada intended role (masuk dari halaman login,
                // bukan dari choose-role), simpan data Google di session dan
                // arahkan ke halaman choose-role terlebih dahulu.
                if (empty($intendedRole)) {
                    session([
                        'google_pending' => [
                            'id'     => $googleUser->getId(),
                            'name'   => $googleUser->getName(),
                            'email'  => $googleUser->getEmail(),
                            'avatar' => $googleUser->getAvatar(),
                        ]
                    ]);
                    return redirect()->route('register.choose-role')
                        ->with('info', 'Akun Google Anda belum terdaftar. Silakan pilih jenis akun terlebih dahulu.');
                }

                if (!in_array($intendedRole, ['customer', 'mitra'])) {
                    $intendedRole = 'customer';
                }

                // Cek apakah ada pending google data dari session sebelumnya
                $googlePending = session('google_pending');

                $user = User::create([
                    'name'              => $googleUser->getName() ?: ($googlePending['name'] ?? 'Pengguna Google'),
                    'email'             => $googleUser->getEmail() ?: ($googlePending['email'] ?? ''),
                    'google_id'         => $googleUser->getId() ?: ($googlePending['id'] ?? null),
                    'avatar'            => $googleUser->getAvatar() ?: ($googlePending['avatar'] ?? null),
                    'role'              => $intendedRole,
                    'email_verified_at' => now(),
                    'password'          => bcrypt(Str::random(24)),
                    'status'            => 'active',
                ]);

                // Initialize balance record
                try {
                    UserBalance::firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0]
                    );
                } catch (\Throwable $e) {}
            }

            // Login and regenerate session
            Auth::login($user, remember: true);
            request()->session()->regenerate();
            session()->forget(['oauth_intended_role', 'google_pending']);

            // Redirect based on role
            if ($user->role === 'mitra') {
                return redirect()->intended(route('mitra.dashboard'));
            } elseif ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'super_admin') {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            return redirect()->intended(route('customer.dashboard'));

        } catch (\Throwable $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login via Google: ' . $e->getMessage());
        }
    }
}
