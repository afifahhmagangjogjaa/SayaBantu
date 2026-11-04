<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response
            ->assertSeeVolt('pages.auth.forgot-password')
            ->assertStatus(200);
    }

    public function test_otp_code_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);

        Volt::test('pages.auth.forgot-password')
            ->set('email', 'user@test.com')
            ->call('sendOtp')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        Notification::assertSentTo($user, PasswordResetOtpNotification::class);
    }

    public function test_password_can_be_reset_with_valid_otp(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        // Step 1: Send OTP
        $component = Volt::test('pages.auth.forgot-password')
            ->set('email', 'user@test.com')
            ->call('sendOtp')
            ->assertSet('step', 2);

        $cachedOtp = Cache::get('password_reset_otp_' . md5('user@test.com'));
        $this->assertNotEmpty($cachedOtp);

        // Step 2: Verify OTP
        $component->set('otp_code', $cachedOtp)
            ->call('verifyOtp')
            ->assertHasNoErrors()
            ->assertSet('step', 3);

        // Step 3: Reset password
        $component->set('password', 'NewSecret123!')
            ->set('password_confirmation', 'NewSecret123!')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true);

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecret123!', $user->password));
    }
}
