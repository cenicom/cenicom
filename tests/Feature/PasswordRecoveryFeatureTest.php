<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordRecoveryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_password_recovery_form(): void
    {
        $response = $this->get(route('password.request'));

        $response
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    public function test_reset_link_request_does_not_reveal_if_email_exists(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $knownEmailResponse = $this
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => $user->email,
            ]);

        $knownEmailResponse
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'password-reset-link-sent');

        $unknownEmailResponse = $this
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'unknown-account@example.com',
            ]);

        $unknownEmailResponse
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'password-reset-link-sent');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_guest_can_view_reset_form_with_token_and_email(): void
    {
        $user = User::factory()->create();

        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        $response
            ->assertOk()
            ->assertViewIs('auth.reset-password')
            ->assertViewHas('token', $token)
            ->assertViewHas('email', $user->email);
    }

    public function test_valid_token_resets_password_without_authenticating_user(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $rememberTokenBefore = $user->remember_token;
        $token = Password::broker()->createToken($user);

        $response = $this
            ->from(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'password-reset');

        $this->assertGuest();

        $user->refresh();

        $this->assertTrue(
            Hash::check('new-password-123', $user->password)
        );

        $this->assertFalse(
            Hash::check('old-password', $user->password)
        );

        $this->assertNotSame(
            $rememberTokenBefore,
            $user->remember_token
        );

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_invalid_token_cannot_reset_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this
            ->from(route('password.reset', [
                'token' => 'invalid-token',
                'email' => $user->email,
            ]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertTrue(
            Hash::check('old-password', $user->fresh()->password)
        );

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_expired_token_cannot_reset_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $token = Password::broker()->createToken($user);

        $this->travel(61)->minutes();

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertTrue(
            Hash::check('old-password', $user->fresh()->password)
        );
    }

    public function test_consumed_token_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'another-password-123',
            'password_confirmation' => 'another-password-123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertTrue(
            Hash::check('new-password-123', $user->fresh()->password)
        );
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'different-password-123',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertTrue(
            Hash::check('old-password', $user->fresh()->password)
        );
    }
}