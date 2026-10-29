<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'customer',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->onboardedUser();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            // Aplikasi ini mengarahkan per role, bukan ke /dashboard generik.
            ->assertRedirect(route('customer.dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_users_can_authenticate_using_phone_number(): void
    {
        $user = $this->onboardedUser(['phone' => '081234567890']);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', '081234567890')
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_dashboard_redirects_to_role_specific_dashboard(): void
    {
        $customer = $this->onboardedUser();
        $mitra = $this->onboardedUser(['role' => 'mitra', 'email' => 'mitra@example.test']);

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertRedirect(route('customer.dashboard'));

        $this->actingAs($mitra)
            ->get('/dashboard')
            ->assertRedirect(route('mitra.dashboard'));
    }

    public function test_users_can_logout(): void
    {
        $user = $this->onboardedUser();

        $this->actingAs($user);

        $component = Volt::test('layout.navigation');

        $component->call('logout');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_customer_logging_in_ignores_superadmin_intended_url(): void
    {
        $user = $this->onboardedUser();

        session(['url.intended' => 'http://localhost/superadmin/settings/banners']);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('customer.dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertNull(session('url.intended'));
    }

    public function test_customer_accessing_superadmin_routes_is_redirected_to_customer_dashboard(): void
    {
        $customer = $this->onboardedUser();

        $response = $this->actingAs($customer)->get('/superadmin/settings/banners');

        $response->assertRedirect(route('customer.dashboard'));
    }
}
