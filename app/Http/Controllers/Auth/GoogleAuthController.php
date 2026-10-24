<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /** Role yang boleh dipilih saat pendaftaran lewat Google. */
    private const ALLOWED_ROLES = ['customer', 'mitra'];

    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirectToGoogle(Request $request)
    {
        // Simpan intended role hanya jika eksplisit dikirim (klik dari halaman
        // daftar). Dari halaman login tidak ada role, sehingga callback akan
        // mengarahkan user baru ke halaman pilih jenis akun.
        $role = $request->query('role');

        if ($role && in_array($role, self::ALLOWED_ROLES, true)) {
            session(['oauth_intended_role' => $role]);
        } else {
            session()->forget('oauth_intended_role');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (! $googleUser || ! $googleUser->getEmail()) {
                return redirect()->route('login')->with('error', 'Gagal mendapatkan data akun dari Google.');
            }

            $profile = $this->googleProfile($googleUser);
            $user = $this->findUserByGoogleProfile($profile);

            if ($user) {
                // Gerbang status: samakan dengan login manual (LoginForm), agar akun
                // nonaktif/diblokir/ditolak tidak bisa masuk lewat jalur Google.
                if ($redirect = $this->blockedLoginRedirect($user)) {
                    return $redirect;
                }

                $this->syncGoogleProfile($user, $profile);
            } else {
                // Email yang pendaftarannya sudah ditolak admin tidak boleh membuat
                // akun baru lewat Google; tampilkan alasan penolakannya.
                if ($redirect = $this->rejectedRegistrationRedirect($profile['email'])) {
                    return $redirect;
                }

                $intendedRole = session('oauth_intended_role');

                // Jenis akun belum dipilih: simpan profil Google di session dan
                // minta user memilih role. Tidak perlu consent Google ulang.
                if (empty($intendedRole)) {
                    session(['google_pending' => $profile]);

                    return redirect()->route('register.choose-role')
                        ->with('info', 'Akun Google Anda belum terdaftar. Silakan pilih jenis akun terlebih dahulu.');
                }

                $user = $this->createUserFromGoogle($profile, $this->normalizeRole($intendedRole));
            }

            return $this->loginAndRedirect($user);
        } catch (\Throwable $e) {
            Log::error('Google OAuth Error: '.$e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal login via Google: '.$e->getMessage());
        }
    }

    /**
     * Selesaikan pendaftaran Google setelah user memilih jenis akun di halaman
     * choose-role. Profil Google sudah tersimpan di session('google_pending'),
     * jadi tidak perlu round-trip consent Google kedua kalinya.
     */
    public function completeGoogleRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:customer,mitra'],
        ]);

        $profile = session('google_pending');

        if (empty($profile['id']) || empty($profile['email'])) {
            return redirect()->route('register.choose-role')
                ->with('error', 'Sesi pendaftaran Google Anda sudah berakhir. Silakan ulangi dengan Google.');
        }

        if ($redirect = $this->rejectedRegistrationRedirect((string) $profile['email'])) {
            return $redirect;
        }

        // Bisa terjadi bila pendaftaran juga diselesaikan di tab lain.
        $user = $this->findUserByGoogleProfile($profile);

        if ($user) {
            if ($redirect = $this->blockedLoginRedirect($user)) {
                return $redirect;
            }

            $this->syncGoogleProfile($user, $profile);
        } else {
            $user = $this->createUserFromGoogle($profile, $this->normalizeRole($validated['role']));
        }

        return $this->loginAndRedirect($user);
    }

    /**
     * Ambil data yang dibutuhkan dari objek Socialite menjadi array biasa,
     * supaya aman disimpan di session dan bisa dipakai ulang.
     *
     * @return array{id: string|null, name: string|null, email: string, avatar: string|null}
     */
    private function googleProfile($googleUser): array
    {
        return [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => (string) $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ];
    }

    /**
     * @param  array{id: string|null, name: string|null, email: string, avatar: string|null}  $profile
     */
    private function findUserByGoogleProfile(array $profile): ?User
    {
        $query = User::query();

        if (! empty($profile['id'])) {
            $query->where('google_id', $profile['id']);

            if (! empty($profile['email'])) {
                $query->orWhere('email', $profile['email']);
            }
        } else {
            $query->where('email', $profile['email']);
        }

        return $query->first();
    }

    private function normalizeRole(?string $role): string
    {
        return in_array($role, self::ALLOWED_ROLES, true) ? $role : 'customer';
    }

    /**
     * Lengkapi data user lama dengan informasi dari Google.
     *
     * @param  array{id: string|null, name: string|null, email: string, avatar: string|null}  $profile
     */
    private function syncGoogleProfile(User $user, array $profile): void
    {
        $updates = [];

        if (empty($user->google_id) && ! empty($profile['id'])) {
            $updates['google_id'] = $profile['id'];
        }

        if (empty($user->email_verified_at)) {
            $updates['email_verified_at'] = now();
        }

        if (! empty($profile['avatar']) && empty($user->avatar)) {
            $updates['avatar'] = $profile['avatar'];
        }

        if ($updates !== []) {
            $user->update($updates);
        }
    }

    /**
     * @param  array{id: string|null, name: string|null, email: string, avatar: string|null}  $profile
     */
    private function createUserFromGoogle(array $profile, string $role): User
    {
        $user = User::create([
            'name' => $profile['name'] ?: 'Pengguna Google',
            'email' => $profile['email'],
            'google_id' => $profile['id'],
            'avatar' => $profile['avatar'],
            'role' => $role,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(24)),
            'status' => 'active',
        ]);

        // Saldo awal; kegagalan di sini tidak boleh menggagalkan login.
        try {
            UserBalance::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );
        } catch (\Throwable $e) {
        }

        return $user;
    }

    private function loginAndRedirect(User $user): RedirectResponse
    {
        Auth::login($user, remember: true);
        request()->session()->regenerate();
        session()->forget(['oauth_intended_role', 'google_pending']);

        return match ($user->role) {
            'mitra' => redirect()->intended(route('mitra.dashboard')),
            'admin' => redirect()->intended(route('admin.dashboard')),
            'super_admin' => redirect()->intended(route('superadmin.dashboard')),
            default => redirect()->intended(route('customer.dashboard')),
        };
    }

    /**
     * Gerbang status akun untuk jalur login Google.
     *
     * Aturannya disamakan dengan login email/kata sandi di
     * App\Livewire\Forms\LoginForm: registrasi yang ditolak diprioritaskan,
     * lalu pending, inactive, dan blocked/banned.
     */
    private function blockedLoginRedirect(User $user): ?RedirectResponse
    {
        if ($redirect = $this->rejectedRegistrationRedirect((string) $user->email)) {
            return $redirect;
        }

        if ($user->status === 'pending') {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda masih menunggu verifikasi dari admin. Silakan tunggu hingga akun Anda disetujui.',
            ]);
        }

        if ($user->status === 'inactive') {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service untuk informasi lebih lanjut.',
            ]);
        }

        if ($user->status === 'blocked' || $user->isBanned()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah diblokir. Silakan hubungi administrator.',
            ]);
        }

        return null;
    }

    /**
     * Bila pendaftaran dengan email tersebut sudah ditolak admin, arahkan ke
     * halaman alasan penolakan (route auth.rejected).
     */
    private function rejectedRegistrationRedirect(string $email): ?RedirectResponse
    {
        if ($email === '') {
            return null;
        }

        $registration = Registration::where('email', $email)->latest()->first();

        if ($registration && $registration->status === 'rejected') {
            return redirect()->route('auth.rejected', ['registration' => $registration->id]);
        }

        return null;
    }
}
