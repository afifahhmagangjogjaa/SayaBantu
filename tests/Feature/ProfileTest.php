<?php

namespace Tests\Feature;

use App\Livewire\Profile\UpdateProfileInformationForm;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
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

    private function validProfileData(User $user, City $city): array
    {
        return [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'nik' => '3402123456780001',
            'phone' => '081234567890',
            'city_id' => $city->id,
            'address' => 'Jl. Contoh No. 1',
            'gender' => 'Laki-laki',
        ];
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->user(['name' => 'Budi Santoso']);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Budi Santoso');
    }

    public function test_profile_edit_page_renders_the_update_form(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get('/profile/edit')
            ->assertOk()
            ->assertSeeLivewire(UpdateProfileInformationForm::class);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->user(['nik' => '3402123456780001']);
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);

        $this->actingAs($user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('name', 'Nama Baru')
            ->set('phone', '081234567890')
            ->set('city_id', $city->id)
            ->set('address', 'Jl. Contoh No. 1')
            ->set('gender', 'Laki-laki')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('3402123456780001', $user->nik);
        $this->assertSame('081234567890', $user->phone);
        $this->assertSame($city->id, $user->city_id);
    }

    public function test_nik_cannot_be_changed_via_profile_update(): void
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $originalNik = '3402123456780001';
        $user = $this->user(['email' => 'saya@example.test', 'nik' => $originalNik]);
        $this->actingAs($user);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('name', 'Nama Baru')
            ->set('nik', '3402999999999999')
            ->set('phone', '081234567890')
            ->set('city_id', $city->id)
            ->set('address', 'Jl. Contoh No. 1')
            ->set('gender', 'Laki-laki')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        // NIK harus tetap sama dengan NIK awal pengguna (terkunci)
        $this->assertSame($originalNik, $user->fresh()->nik);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->user();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->user();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }
}
