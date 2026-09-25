<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_has_null_email_verified_at(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }

    public function test_verified_user_has_email_verified_at(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);
    }

    public function test_unverified_authenticated_user_can_access_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_verification_notice(): void
    {
        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('login'));
    }

    public function test_verified_user_is_redirected_from_verification_notice(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertRedirect('/');
    }

    public function test_unverified_user_can_authenticate(): void
    {
        $user = User::factory()->unverified()->create([
            'password' => 'correct-password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    public function test_unverified_user_can_request_verification_notification(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('verification.send'));

        $response->assertRedirect();
    }

    public function test_verified_user_cannot_request_unnecessary_verification_notification(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('verification.send'));

        $response->assertRedirect('/');
    }

    public function test_valid_verification_link_verifies_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/');

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
    }

    public function test_invalid_verification_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1('incorrect@example.com'),
            ],
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        $user->refresh();

        $this->assertNull($user->email_verified_at);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        $verificationUrl = route('verification.verify', [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
            'expires' => now()->addMinutes(60)->timestamp,
            'signature' => 'invalid-signature',
        ]);

        $response = $this->get($verificationUrl);

        $response->assertStatus(403);

        $user->refresh();

        $this->assertNull($user->email_verified_at);
    }

    public function test_verification_does_not_modify_roles_or_permissions(): void
    {
        $user = User::factory()->unverified()->create();

        $rolesBefore = $user
            ->roles()
            ->pluck('roles.id')
            ->sort()
            ->values()
            ->all();

        $permissionsBefore = $user
            ->permissions()
            ->pluck('permissions.id')
            ->sort()
            ->values()
            ->all();

        $this->actingAs($user);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $this->get($verificationUrl);

        $user->refresh();

        $rolesAfter = $user
            ->roles()
            ->pluck('roles.id')
            ->sort()
            ->values()
            ->all();

        $permissionsAfter = $user
            ->permissions()
            ->pluck('permissions.id')
            ->sort()
            ->values()
            ->all();

        $this->assertSame($rolesBefore, $rolesAfter);
        $this->assertSame($permissionsBefore, $permissionsAfter);
    }
}
