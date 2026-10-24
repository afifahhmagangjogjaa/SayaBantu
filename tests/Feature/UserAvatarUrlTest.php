<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Foto profil: foto unggahan sendiri (profile_photo) diprioritaskan, lalu
 * foto akun Google (avatar berisi URL absolut). Sebelumnya kolom avatar tidak
 * pernah dipakai UI sehingga foto Google tidak pernah tampil.
 */
class UserAvatarUrlTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'customer',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    public function test_tanpa_foto_avatar_url_kosong(): void
    {
        $this->assertNull($this->user()->avatar_url);
    }

    public function test_foto_unggahan_memakai_url_storage(): void
    {
        $user = $this->user(['profile_photo' => 'profile-photos/saya.jpg']);

        $this->assertSame(asset('storage/profile-photos/saya.jpg'), $user->avatar_url);
    }

    public function test_foto_google_dipakai_bila_belum_ada_foto_unggahan(): void
    {
        $url = 'https://lh3.googleusercontent.com/a/foto-google';
        $user = $this->user(['avatar' => $url]);

        $this->assertSame($url, $user->avatar_url);
    }

    public function test_foto_unggahan_menang_atas_foto_google(): void
    {
        $user = $this->user([
            'profile_photo' => 'profile-photos/saya.jpg',
            'avatar' => 'https://lh3.googleusercontent.com/a/foto-google',
        ]);

        $this->assertSame(asset('storage/profile-photos/saya.jpg'), $user->avatar_url);
    }

    public function test_halaman_profil_menampilkan_foto_google(): void
    {
        $url = 'https://lh3.googleusercontent.com/a/foto-google';
        $user = $this->user(['avatar' => $url]);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee($url, false);
    }
}
