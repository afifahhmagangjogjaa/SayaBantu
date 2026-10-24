<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use App\Models\PartnerActivity;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Registration;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $input = trim($this->email);
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'phone';

        $foundUser = User::where($field, $input)->first();

        // If phone not found directly, try normalizing phone format (08xx vs 62xx)
        if (!$foundUser && !$isEmail) {
            $digits = preg_replace('/[^0-9]/', '', $input);
            $altPhone = str_starts_with($digits, '62') 
                ? '0' . substr($digits, 2) 
                : (str_starts_with($digits, '0') ? '62' . substr($digits, 1) : null);

            if ($altPhone) {
                $foundUser = User::where('phone', $altPhone)->first();
                if ($foundUser) {
                    $input = $foundUser->phone;
                }
            }
        }

        // Check if user is inactive, blocked, or banned BEFORE logging into session.
        if ($foundUser && (in_array($foundUser->status, ['inactive', 'blocked']) || $foundUser->isBanned())) {
            if (\Illuminate\Support\Facades\Hash::check($this->password, $foundUser->password)) {
                $status = $foundUser->status;
                $errorMessage = ($status === 'inactive')
                    ? 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service untuk informasi lebih lanjut.'
                    : 'Akun Anda telah diblokir/banned. Seluruh sesi login telah dihapus secara total. Silakan hubungi administrator.';

                throw ValidationException::withMessages([
                    'form.email' => $errorMessage,
                ]);
            }
        }

        $attemptSuccess = Auth::attempt([$field => $input, 'password' => $this->password], $this->remember);

        // Demo password fallback: allow Sayabantu123! or password for testing convenience.
        // Hanya aktif jika DEMO_LOGIN=true (default false), sehingga tidak pernah
        // ikut terbawa ke production.
        if (!$attemptSuccess && $foundUser && config('app.demo_login')) {
            $isDemoPassword = in_array(strtolower($this->password), ['sayabantu123!', 'sayabantu123', 'password']);
            $hasDemoHash = \Illuminate\Support\Facades\Hash::check('Sayabantu123!', $foundUser->password) 
                        || \Illuminate\Support\Facades\Hash::check('password', $foundUser->password);

            if ($isDemoPassword && $hasDemoHash) {
                Auth::login($foundUser, $this->remember);
                $attemptSuccess = true;
            }
        }

        if (!$attemptSuccess) {
            RateLimiter::hit($this->throttleKey());

            // Log failed login attempt for partner reporting (if user exists)
            try {
                PartnerActivity::create([
                    'user_id' => $foundUser?->id,
                    'activity_type' => 'login_failed',
                    'description' => 'Gagal login',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to record login_failed PartnerActivity: ' . $e->getMessage());
            }

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        // Check user status after successful authentication
        $user = Auth::user();

        // Allow admin and super_admin to login regardless of registration pending flag
        $isPrivileged = in_array($user->role, ['admin', 'super_admin']);

        // If user's registration was rejected, redirect to a page showing rejection reason
        try {
            $reg = Registration::where('email', $user->email)->latest()->first();
            if ($reg && ($reg->status === 'rejected')) {
                Auth::logout();
                // redirect to rejected page showing reason
                redirect()->route('auth.rejected', ['registration' => $reg->id])->send();
                return;
            }
        } catch (\Throwable $e) {
            // ignore lookup errors
        }

        // Only block non-privileged users who are pending
        if (!$isPrivileged && $user->status === 'pending') {
            Auth::logout();
            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda masih menunggu verifikasi dari admin. Silakan tunggu hingga akun Anda disetujui.',
            ]);
        }

        // Fallback block for inactive/blocked in case status changed concurrently
        if ($user->status === 'inactive') {
            Auth::logout();
            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin atau customer service untuk informasi lebih lanjut.',
            ]);
        }

        if ($user->status === 'blocked' || $user->isBanned()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'form.email' => 'Akun Anda telah diblokir/banned. Seluruh sesi login telah dihapus secara total. Silakan hubungi administrator.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Record successful login activity for all users
        try {
            if ($user) {
                PartnerActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => 'login',
                    'description' => 'Login berhasil',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            }
        } catch (\Throwable $e) {
            // allow login to proceed even if activity logging fails
            \Log::warning('Failed to record PartnerActivity on login: ' . $e->getMessage());
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}
