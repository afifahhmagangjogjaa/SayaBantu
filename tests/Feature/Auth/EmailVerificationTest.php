<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function unverifiedUser(): User
    {
        return User::factory()->unverified()->create([
            'role' => 'customer',
            'status' => 'active',
            'verified' => false,
            'is_completed' => true,
        ]);
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = $this->unverifiedUser();

        $response = $this->actingAs($user)->get('/email/verify');

        $response
            ->assertOk()
            ->assertViewIs('auth.verify-email');
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->unverifiedUser();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // Setelah verifikasi, user otomatis login dan diarahkan ke dashboard role-nya.
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->unverifiedUser();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
