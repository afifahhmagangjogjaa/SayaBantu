<?php

namespace Tests\Feature\Auth;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

/**
 * Alur login/daftar lewat Google.
 *
 * Gerbang status harus sama dengan login email/kata sandi
 * (App\Livewire\Forms\LoginForm): akun nonaktif, diblokir, atau registrasi
 * yang ditolak tidak boleh masuk lewat jalur Google.
 */
class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogle(string $id = 'google-123', string $email = 'google@example.com', string $name = 'Pengguna Google', ?string $avatar = null): void
    {
        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn($id);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getName')->andReturn($name);
        $googleUser->shouldReceive('getAvatar')->andReturn($avatar);

        Socialite::shouldReceive('driver->user')->andReturn($googleUser);
    }

    private function user(string $role = 'customer', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function rejectedRegistration(string $email): Registration
    {
        return Registration::create([
            'uuid' => (string) Str::uuid(),
            'email' => $email,
            'full_name' => 'Pendaftar Ditolak',
            'status' => 'rejected',
            'rejection_reason' => 'Dokumen KTP tidak terbaca.',
        ]);
    }

    public function test_user_baru_dari_halaman_login_diarahkan_pilih_jenis_akun(): void
    {
        $this->fakeGoogle('google-1', 'baru@example.com', 'Baru');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('register.choose-role'));

        $this->assertFalse(User::where('email', 'baru@example.com')->exists());
        $this->assertSame('baru@example.com', session('google_pending.email'));
        $this->assertGuest();
    }

    public function test_user_baru_dengan_role_terpilih_langsung_dibuat_dan_login(): void
    {
        $avatarUrl = 'https://lh3.googleusercontent.com/a/foto-mitra';
        $this->fakeGoogle('google-2', 'mitra-baru@example.com', 'Mitra Baru', $avatarUrl);

        $this->withSession(['oauth_intended_role' => 'mitra'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('mitra.dashboard'));

        $user = User::where('email', 'mitra-baru@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('mitra', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertFalse((bool) $user->is_completed);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('google-2', $user->google_id);
        $this->assertSame($avatarUrl, $user->avatar);
        $this->assertSame($avatarUrl, $user->avatar_url, 'Foto Google harus langsung terpakai sebagai avatar');
        $this->assertNull($user->password, 'Password Google-created user harus kosong agar onboarding meminta kata sandi');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_aktif_lama_bisa_login_dan_google_id_terisi(): void
    {
        $user = $this->user('customer', ['email' => 'lama@example.com']);
        $this->fakeGoogle('google-3', 'lama@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-3', $user->fresh()->google_id);
    }

    public function test_akun_nonaktif_tidak_bisa_login_lewat_google(): void
    {
        $user = $this->user('customer', ['email' => 'nonaktif@example.com', 'status' => 'inactive']);
        $this->fakeGoogle('google-4', 'nonaktif@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_akun_diblokir_tidak_bisa_login_lewat_google(): void
    {
        $user = $this->user('mitra', ['email' => 'blokir@example.com', 'status' => 'blocked']);
        $this->fakeGoogle('google-5', 'blokir@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_registrasi_ditolak_diarahkan_ke_halaman_alasan_penolakan(): void
    {
        $user = $this->user('customer', ['email' => 'ditolak@example.com', 'status' => 'inactive']);
        $registration = $this->rejectedRegistration('ditolak@example.com');
        $this->fakeGoogle('google-6', 'ditolak@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('auth.rejected', ['registration' => $registration->id]));

        $this->assertGuest();
    }

    public function test_email_terdaftar_sebagai_pendaftaran_ditolak_tidak_bisa_membuat_akun_google(): void
    {
        $registration = $this->rejectedRegistration('belum-ada-akun@example.com');
        $this->fakeGoogle('google-7', 'belum-ada-akun@example.com');

        $this->withSession(['oauth_intended_role' => 'customer'])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('auth.rejected', ['registration' => $registration->id]));

        $this->assertFalse(User::where('email', 'belum-ada-akun@example.com')->exists());
        $this->assertGuest();
    }

    public function test_halaman_pilih_role_menampilkan_form_tanpa_consent_ulang(): void
    {
        $this->withSession(['google_pending' => [
            'id' => 'google-8',
            'name' => 'Pendaftar Google',
            'email' => 'pilih@example.com',
            'avatar' => null,
        ]])
            ->get(route('register.choose-role'))
            ->assertOk()
            ->assertSee(route('register.google.complete'))
            ->assertSee('name="role" value="customer"', false)
            ->assertSee('name="role" value="mitra"', false)
            ->assertDontSee(route('auth.google', ['role' => 'customer']));
    }

    public function test_memilih_role_menyelesaikan_pendaftaran_tanpa_google_kedua_kali(): void
    {
        $pending = [
            'id' => 'google-9',
            'name' => 'Pilih Customer',
            'email' => 'pilih-customer@example.com',
            'avatar' => null,
        ];

        $this->withSession(['google_pending' => $pending])
            ->post(route('register.google.complete'), ['role' => 'customer'])
            ->assertRedirect(route('customer.dashboard'));

        $user = User::where('email', 'pilih-customer@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('customer', $user->role);
        $this->assertSame('google-9', $user->google_id);
        $this->assertSame('active', $user->status);
        $this->assertFalse((bool) $user->is_completed);
        $this->assertNull($user->password);
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('google_pending'));
    }

    public function test_memilih_role_mitra_membuat_akun_mitra(): void
    {
        $this->withSession(['google_pending' => [
            'id' => 'google-10',
            'name' => 'Pilih Mitra',
            'email' => 'pilih-mitra@example.com',
            'avatar' => null,
        ]])
            ->post(route('register.google.complete'), ['role' => 'mitra'])
            ->assertRedirect(route('mitra.dashboard'));

        $this->assertSame('mitra', User::where('email', 'pilih-mitra@example.com')->first()?->role);
    }

    public function test_tanpa_sesi_google_pending_tidak_membuat_akun(): void
    {
        $this->post(route('register.google.complete'), ['role' => 'customer'])
            ->assertRedirect(route('register.choose-role'))
            ->assertSessionHas('error');

        $this->assertSame(0, User::count());
        $this->assertGuest();
    }

    public function test_role_tidak_valid_ditolak_validasi(): void
    {
        $this->withSession(['google_pending' => [
            'id' => 'google-11',
            'name' => 'Role Salah',
            'email' => 'role-salah@example.com',
            'avatar' => null,
        ]])
            ->post(route('register.google.complete'), ['role' => 'admin'])
            ->assertSessionHasErrors('role');

        $this->assertFalse(User::where('email', 'role-salah@example.com')->exists());
    }

    public function test_pendaftaran_ditolak_tidak_bisa_menyelesaikan_lewat_pilih_role(): void
    {
        $registration = $this->rejectedRegistration('ditolak-pilih@example.com');

        $this->withSession(['google_pending' => [
            'id' => 'google-12',
            'name' => 'Ditolak',
            'email' => 'ditolak-pilih@example.com',
            'avatar' => null,
        ]])
            ->post(route('register.google.complete'), ['role' => 'customer'])
            ->assertRedirect(route('auth.rejected', ['registration' => $registration->id]));

        $this->assertFalse(User::where('email', 'ditolak-pilih@example.com')->exists());
    }

    public function test_akun_yang_sudah_dibuat_di_tab_lain_tidak_terduplikasi(): void
    {
        $existing = $this->user('customer', [
            'email' => 'sudah-ada@example.com',
            'google_id' => 'google-13',
        ]);

        $this->withSession(['google_pending' => [
            'id' => 'google-13',
            'name' => 'Sudah Ada',
            'email' => 'sudah-ada@example.com',
            'avatar' => null,
        ]])
            ->post(route('register.google.complete'), ['role' => 'customer'])
            ->assertRedirect(route('customer.dashboard'));

        $this->assertSame(1, User::where('email', 'sudah-ada@example.com')->count());
        $this->assertAuthenticatedAs($existing);
    }

    public function test_user_google_diarahkan_membuat_kata_sandi_sebelum_masuk_dashboard(): void
    {
        $this->fakeGoogle('google-14', 'buat-sandi@example.com');

        $this->withSession(['oauth_intended_role' => 'customer'])
            ->get(route('auth.google.callback'));

        $user = User::where('email', 'buat-sandi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->password);

        // Middleware onboarded harus memaksa langkah buat kata sandi lebih dulu.
        $this->get(route('customer.dashboard'))->assertRedirect(route('onboarding.password'));

        $this->get(route('onboarding.password'))
            ->assertOk()
            ->assertSee('name="password_confirmation"', false);
    }

    public function test_setelah_membuat_kata_sandi_akun_bisa_login_tanpa_google(): void
    {
        $this->fakeGoogle('google-15', 'punya-sandi@example.com');

        $this->withSession(['oauth_intended_role' => 'customer'])
            ->get(route('auth.google.callback'));

        $this->post(route('onboarding.password.store'), [
            'password' => 'Rahasia#123',
            'password_confirmation' => 'Rahasia#123',
        ])->assertRedirect(route('onboarding.step1'));

        $user = User::where('email', 'punya-sandi@example.com')->first()->refresh();

        $this->assertNotNull($user->password);
        $this->assertTrue(Hash::check('Rahasia#123', $user->password));
        $this->assertTrue((bool) Auth::validate([
            'email' => 'punya-sandi@example.com',
            'password' => 'Rahasia#123',
        ]));
    }

    public function test_login_google_berikutnya_tetap_berjalan_walau_password_masih_kosong(): void
    {
        $user = $this->user('customer', [
            'email' => 'tanpa-sandi@example.com',
            'password' => null,
            'google_id' => 'google-16',
        ]);

        $this->fakeGoogle('google-16', 'tanpa-sandi@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_google_mencatat_aktivitas_dan_tidak_menyimpan_remember_token(): void
    {
        $user = $this->user('customer', ['email' => 'aktivitas@example.com', 'google_id' => 'google-17']);
        $user->forceFill(['remember_token' => null])->saveQuietly();

        $this->fakeGoogle('google-17', 'aktivitas@example.com');

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('partner_activities', [
            'user_id' => $user->id,
            'activity_type' => 'login',
        ]);

        // Default login manual tidak memakai "remember me"; Google harus sama.
        $this->assertNull($user->fresh()->remember_token);
    }

    public function test_akun_yang_ditolak_mencatat_login_gagal(): void
    {
        $user = $this->user('customer', ['email' => 'catat-gagal@example.com', 'status' => 'inactive']);

        $this->fakeGoogle('google-18', 'catat-gagal@example.com');

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'));

        $this->assertDatabaseHas('partner_activities', [
            'user_id' => $user->id,
            'activity_type' => 'login_failed',
        ]);
    }

    public function test_error_google_tidak_membocorkan_detail_ke_user(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new \RuntimeException('invalid_client: client_id=SECRET123&redirect_uri=https://rahasia'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));

        $error = session('error');

        $this->assertNotNull($error);
        $this->assertStringNotContainsString('SECRET123', (string) $error);
        $this->assertStringNotContainsString('client_id', (string) $error);
        $this->assertStringContainsString('Gagal login lewat Google', (string) $error);
    }

    public function test_halaman_masuk_google_dibatasi_rate_limit(): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        for ($i = 0; $i < 20; $i++) {
            $this->get(route('auth.google'))->assertRedirect();
        }

        $this->get(route('auth.google'))->assertStatus(429);
    }

    public function test_penyelesaian_pendaftaran_google_dibatasi_rate_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('register.google.complete'), ['role' => 'customer']);
        }

        $this->post(route('register.google.complete'), ['role' => 'customer'])
            ->assertStatus(429);
    }
}
