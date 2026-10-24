<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PartnerActivity;
use App\Models\Registration;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /** Role yang boleh dipilih saat pendaftaran lewat Google. */
    private const ALLOWED_ROLES = ['customer', 'mitra'];

    /** Pesan untuk user — detail teknis hanya masuk log. */
    private const ERROR_GENERIC = 'Gagal masuk dengan Google. Silakan coba lagi.';

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

        // Pilihan "ingat saya" ikut melewati round-trip OAuth lewat session.
        session(['oauth_remember' => $request->boolean('remember')]);

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            Log::error('Google OAuth redirect error', ['error' => $e->getMessage(), 'ip' => $request->ip()]);

            return redirect()->route('login')->with('error', self::ERROR_GENERIC);
        }
    }

    /**
     * Handle callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (! $googleUser || ! $googleUser->getEmail()) {
                Log::warning('Google OAuth callback tanpa email', ['ip' => request()->ip()]);

                return redirect()->route('login')->with('error', 'Gagal mendapatkan data akun dari Google.');
            }

            $profile = $this->googleProfile($googleUser);
            $user = $this->findUserByGoogleProfile($profile);
            $isNewAccount = false;

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
                $isNewAccount = true;
            }

            return $this->loginAndRedirect($user, $isNewAccount);
        } catch (\Throwable $e) {
            Log::error('Google OAuth Error', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            // Jangan bocorkan detail OAuth (client_id, redirect_uri, dsb) ke user.
            $this->recordActivity(null, 'login_failed', 'Login via Google gagal: '.$e::class);

            return redirect()->route('login')
                ->with('error', 'Gagal login lewat Google. Silakan coba lagi atau masuk dengan email dan kata sandi.');
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
        $isNewAccount = false;

        if ($user) {
            if ($redirect = $this->blockedLoginRedirect($user)) {
                return $redirect;
            }

            $this->syncGoogleProfile($user, $profile);
        } else {
            $user = $this->createUserFromGoogle($profile, $this->normalizeRole($validated['role']));
            $isNewAccount = true;
        }

        return $this->loginAndRedirect($user, $isNewAccount);
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
            // Password sengaja null: onboarding akan meminta user membuat kata
            // sandi sendiri, sehingga akun tetap bisa dipakai tanpa Google dan
            // halaman "Ubah Kata Sandi" tidak meminta sandi yang tidak diketahui.
            'password' => null,
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

    private function loginAndRedirect(User $user, bool $isNewAccount = false): RedirectResponse
    {
        // Tidak memaksa "remember me": samakan dengan default checkbox di
        // form login (LoginForm::$remember = false). Login Google tetap satu klik.
        Auth::login($user, remember: false);
        request()->session()->regenerate();
        session()->forget(['oauth_intended_role', 'google_pending']);

        $this->recordActivity(
            $user,
            'login',
            $isNewAccount ? 'Pendaftaran akun via Google berhasil' : 'Login via Google berhasil'
        );

        return match ($user->role) {
            'mitra' => redirect()->intended(route('mitra.dashboard')),
            'admin' => redirect()->intended(route('admin.dashboard')),
            'super_admin' => redirect()->intended(route('superadmin.dashboard')),
            default => redirect()->intended(route('customer.dashboard')),
        };
    }

    /**
     * Catat aktivitas login/gagal login seperti yang dilakukan LoginForm,
     * supaya laporan mitra di panel admin tidak bolong untuk jalur Google.
     */
    private function recordActivity(?User $user, string $type, string $description): void
    {
        try {
            PartnerActivity::create([
                'user_id' => $user?->id,
                'activity_type' => $type,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat PartnerActivity (Google): '.$e->getMessage());
        }
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
            $this->recordActivity($user, 'login_failed', 'Login via Google ditolak: pendaftaran ditolak admin');

            return $redirect;
        }

        if ($user->status === 'pending') {
            $this->recordActivity($user, 'login_failed', 'Login via Google ditolak: menunggu verifikasi admin');

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda masih menunggu verifikasi dari admin. Silakan tunggu hingga akun Anda disetujui.',
            ]);
        }

        if ($user->status === 'inactive') {
            $this->recordActivity($user, 'login_failed', 'Login via Google ditolak: akun nonaktif');

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service untuk informasi lebih lanjut.',
            ]);
        }

        if ($user->status === 'blocked' || $user->isBanned()) {
            $this->recordActivity($user, 'login_failed', 'Login via Google ditolak: akun diblokir');

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
