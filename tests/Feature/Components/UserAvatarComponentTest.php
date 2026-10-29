<?php

namespace Tests\Feature\Components;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * Komponen <x-user-avatar>: inisial selalu dirender, foto profil menimpanya,
 * dan foto yang gagal dimuat (URL Google kedaluwarsa/diblokir) dihapus
 * sehingga inisial tampil lagi.
 */
class UserAvatarComponentTest extends TestCase
{
    use RefreshDatabase;

    private function render(?User $user): string
    {
        return Blade::render(
            '<x-user-avatar :user="$user" class="h-full w-full" />',
            ['user' => $user]
        );
    }

    public function test_tanpa_foto_hanya_menampilkan_inisial(): void
    {
        $html = $this->render(User::factory()->make(['name' => 'budi santoso']));

        $this->assertStringContainsString('>B<', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_foto_dirender_beserta_fallback_bila_gagal_dimuat(): void
    {
        $url = 'https://lh3.googleusercontent.com/a/foto';
        $html = $this->render(User::factory()->make(['name' => 'Siti', 'avatar' => $url]));

        $this->assertStringContainsString($url, $html);
        $this->assertStringContainsString('alt="Siti"', $html);
        $this->assertStringContainsString('onerror="this.remove()"', $html);
        $this->assertStringContainsString('>S<', $html, 'Inisial harus tetap ada di belakang foto sebagai fallback');
    }

    public function test_foto_unggahan_dipakai_mendahului_foto_google(): void
    {
        $html = $this->render(User::factory()->make([
            'name' => 'Andi',
            'profile_photo' => 'profile-photos/andi.jpg',
            'avatar' => 'https://lh3.googleusercontent.com/a/foto',
        ]));

        $this->assertStringContainsString('profile-photos/andi.jpg', $html);
        $this->assertStringNotContainsString('lh3.googleusercontent.com', $html);
    }

    public function test_nama_kosong_memakai_tanda_tanya(): void
    {
        $this->assertStringContainsString('>?<', $this->render(User::factory()->make(['name' => ''])));
    }

    public function test_tanpa_user_tidak_error(): void
    {
        $this->assertStringContainsString('>?<', $this->render(null));
    }
}
