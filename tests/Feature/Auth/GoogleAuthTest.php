<?php

namespace Tests\Feature\Auth;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    private function fakeGoogle(string $id = 'google-123', string $email = 'google@example.com', string $name = 'Pengguna Google'): void
    {
        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn($id);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getName')->andReturn($name);
        $googleUser->shouldReceive('getAvatar')->andReturn(null);

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
        $this->fakeGoogle('google-2', 'mitra-baru@example.com', 'Mitra Baru');

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
}
