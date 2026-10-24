<?php

namespace Tests\Feature;

use App\Livewire\Admin\ChangePassword as AdminChangePassword;
use App\Livewire\Customer\UpdateProfilePhoto as CustomerUpdateProfilePhoto;
use App\Livewire\Mitra\UpdateProfilePhoto as MitraUpdateProfilePhoto;
use App\Livewire\SuperAdmin\ChangePassword as SuperAdminChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Menjaga perilaku komponen bersama di App\Livewire\Concerns
 * (ChangesPassword & UpdatesProfilePhoto) yang dipakai lintas peran.
 */
class SharedAccountComponentsTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
        ]);
    }

    public function test_admin_dan_super_admin_bisa_mengubah_kata_sandi(): void
    {
        foreach ([
            'admin' => AdminChangePassword::class,
            'super_admin' => SuperAdminChangePassword::class,
        ] as $role => $component) {
            $user = $this->user($role);

            $this->actingAs($user);

            Livewire::test($component)
                ->set('current_password', 'password')
                ->set('password', 'Password#123')
                ->set('password_confirmation', 'Password#123')
                ->call('updatePassword')
                ->assertHasNoErrors()
                ->assertSet('saved', true);

            $this->assertTrue(Hash::check('Password#123', $user->refresh()->password));
        }
    }

    public function test_kata_sandi_saat_ini_harus_benar(): void
    {
        $user = $this->user('admin');
        $this->actingAs($user);

        Livewire::test(AdminChangePassword::class)
            ->set('current_password', 'salah')
            ->set('password', 'Password#123')
            ->set('password_confirmation', 'Password#123')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }

    public function test_hapus_foto_customer_mengosongkan_profile_photo_tanpa_menyentuh_selfie_photo(): void
    {
        Storage::fake('public');

        $user = $this->user('customer');
        $user->update([
            'profile_photo' => 'profile-photos/avatar.jpg',
            'selfie_photo' => 'selfie-photos/ktp.jpg',
        ]);
        Storage::disk('public')->put('profile-photos/avatar.jpg', 'x');
        Storage::disk('public')->put('selfie-photos/ktp.jpg', 'x');

        $this->actingAs($user);

        Livewire::test(CustomerUpdateProfilePhoto::class)
            ->call('removePhoto')
            ->assertRedirect(route('profile'));

        $user->refresh();

        $this->assertNull($user->profile_photo);
        $this->assertSame('selfie-photos/ktp.jpg', $user->selfie_photo);
        Storage::disk('public')->assertMissing('profile-photos/avatar.jpg');
        Storage::disk('public')->assertExists('selfie-photos/ktp.jpg');
    }

    public function test_hapus_foto_mitra_mengosongkan_profile_photo_tanpa_menyentuh_selfie_photo(): void
    {
        Storage::fake('public');

        $user = $this->user('mitra');
        $user->update([
            'profile_photo' => 'profile-photos/avatar.jpg',
            'selfie_photo' => 'selfie-photos/ktp.jpg',
        ]);
        Storage::disk('public')->put('profile-photos/avatar.jpg', 'x');
        Storage::disk('public')->put('selfie-photos/ktp.jpg', 'x');

        $this->actingAs($user);

        Livewire::test(MitraUpdateProfilePhoto::class)
            ->call('removePhoto')
            ->assertRedirect(route('mitra.profile'));

        $user->refresh();

        $this->assertNull($user->profile_photo);
        $this->assertSame('selfie-photos/ktp.jpg', $user->selfie_photo);
    }

    public function test_unggah_foto_baru_menyimpan_path_dan_mengarahkan_ke_profil(): void
    {
        Storage::fake('public');

        $user = $this->user('mitra');
        $this->actingAs($user);

        Livewire::test(MitraUpdateProfilePhoto::class)
            ->set('photo', UploadedFile::fake()->image('baru.jpg', 200, 200))
            ->call('updatePhoto')
            ->assertHasNoErrors()
            ->assertRedirect(route('mitra.profile'));

        $path = $user->refresh()->profile_photo;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }
}
