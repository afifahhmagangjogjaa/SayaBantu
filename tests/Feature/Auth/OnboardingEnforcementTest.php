<?php

namespace Tests\Feature\Auth;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Middleware 'onboarded' wajib aktif pada rute customer & mitra.
 *
 * Regresi: routes/customer.php dan routes/mitra.php dulu memanggil
 * ->middleware('customer') / ->middleware('mitra') setelah
 * ->middleware(['auth', 'onboarded', ...]), dan panggilan kedua itu menimpa
 * daftar middleware — 'auth' dan 'onboarded' hilang tanpa error.
 */
class OnboardingEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private function city(): City
    {
        return City::create(['name' => 'Kota Uji', 'is_active' => true]);
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    public function test_customer_belum_onboarding_diarahkan_membuat_kata_sandi(): void
    {
        $user = $this->user('customer', [
            'is_completed' => false,
            'password' => null,
            'city_id' => $this->city()->id,
        ]);

        $this->actingAs($user)
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('onboarding.password'));
    }

    public function test_customer_belum_lengkap_diarahkan_ke_step1(): void
    {
        $user = $this->user('customer', [
            'is_completed' => false,
            'nik' => null,
            'city_id' => $this->city()->id,
        ]);

        $this->actingAs($user)
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('onboarding.step1'));
    }

    public function test_mitra_belum_onboarding_diarahkan_ke_onboarding(): void
    {
        $user = $this->user('mitra', [
            'is_completed' => false,
            'nik' => null,
            'city_id' => $this->city()->id,
        ]);

        $this->actingAs($user)
            ->get(route('mitra.dashboard'))
            ->assertRedirect(route('onboarding.step1'));
    }

    public function test_user_yang_sudah_selesai_onboarding_tidak_diarahkan(): void
    {
        $customer = $this->user('customer', ['city_id' => $this->city()->id]);
        $mitra = $this->user('mitra', ['city_id' => $this->city()->id]);

        $this->actingAs($customer)
            ->get(route('customer.dashboard'))
            ->assertOk();

        $this->actingAs($mitra)
            ->get(route('mitra.dashboard'))
            ->assertOk();
    }
}
