<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Registrasi di aplikasi ini multi-step: /register hanya gerbang awal,
 * data akun disimpan di session lalu dilanjutkan ke onboarding.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_redirects_to_role_selection_by_default(): void
    {
        $this->get('/register')->assertRedirect(route('register.choose-role'));
    }

    public function test_role_selection_screen_can_be_rendered(): void
    {
        $this->get('/register/choose-role')
            ->assertOk()
            ->assertSeeVolt('pages.auth.register-choose-role');
    }

    public function test_email_registration_form_can_be_rendered_for_selected_role(): void
    {
        $this->get('/register?role=customer')
            ->assertOk()
            ->assertViewIs('auth.register-email');
    }

    public function test_registration_step_pages_are_reachable(): void
    {
        // Regresi: dulu halaman step1 fatal karena method selectCityByName() terdeklarasi dua kali.
        $this->get('/register/step1')->assertOk();

        // step2 tanpa data sesi registrasi akan diarahkan, bukan error 500.
        $this->get('/register/step2')->assertStatus(302);
    }

    public function test_account_data_is_stored_in_session_and_forwarded_to_onboarding(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('onboarding.step1'));
        $response->assertSessionHas('reg_account');

        $this->assertSame('test@example.com', session('reg_account')['email']);
        $this->assertSame('customer', session('reg_account')['role']);

        // Akun baru dibuat di step terakhir onboarding, bukan di sini.
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertGuest();
    }

    public function test_weak_password_is_rejected(): void
    {
        $response = $this->from('/register?role=customer')->post('/register', [
            'name' => 'Test User',
            'email' => 'weak@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->from('/register?role=customer')->post('/register', [
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_duplicate_phone_is_rejected_on_onboarding_step1(): void
    {
        $city = \App\Models\City::create(['name' => 'Kota Yogyakarta', 'is_active' => true]);
        User::factory()->create([
            'phone' => '081234567890',
            'city_id' => $city->id,
        ]);

        $this->withSession([
            'reg_account' => [
                'name' => 'Budi Baru',
                'email' => 'budibaru@example.com',
                'password' => bcrypt('Password123!'),
                'role' => 'customer',
            ],
        ]);

        $response = $this->post(route('onboarding.step1.store'), [
            'nik' => '3402090909930001',
            'name' => 'Budi Baru',
            'gender' => 'Laki-laki',
            'phone' => '081234567890',
            'city_id' => $city->id,
            'address' => 'Jl. Malioboro No. 1',
        ]);

        $response->assertSessionHasErrors('phone');
    }
}
