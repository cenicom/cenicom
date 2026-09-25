<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | AUTH-001
    |--------------------------------------------------------------------------
    */

    public function test_login_page_can_be_displayed(): void
    {
        $response = $this->get(route('login'));

        $response
            ->assertOk()
            ->assertSee('email')
            ->assertSee('password');
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-002
    |--------------------------------------------------------------------------
    */

    public function test_login_form_contains_email_and_password_fields(): void
    {
        $response = $this->get(route('login'));

        $response
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-003
    |--------------------------------------------------------------------------
    */

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-004
    |--------------------------------------------------------------------------
    */

    public function test_valid_credentials_authenticate_user(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-005
    |--------------------------------------------------------------------------
    */

    public function test_successful_login_regenerates_session(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
        ]);

        $sessionIdBeforeLogin = $this->app['session']->getId();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $sessionIdAfterLogin = $this->app['session']->getId();

        $this->assertNotSame(
            $sessionIdBeforeLogin,
            $sessionIdAfterLogin,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-006
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_access_countries(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('countries.index'));

        $response->assertOk();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-007
    |--------------------------------------------------------------------------
    */

    public function test_guest_access_to_countries_redirects_to_login(): void
    {
        $response = $this->get(route('countries.index'));

        $response
            ->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-008
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $this->assertGuest();

        $response->assertRedirect();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-009
    |--------------------------------------------------------------------------
    */

    public function test_countries_requires_authentication_after_logout(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post(route('logout'));

        $this->assertGuest();

        $response = $this->get(route('countries.index'));

        $response
            ->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-010
    |--------------------------------------------------------------------------
    */

    public function test_identity_service_recognizes_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $identityService = app(
            \App\Core\Security\Services\IdentityService::class
        );

        $identity = $identityService->current();

        $this->assertTrue($identity->authenticated);
        $this->assertSame($user->id, $identity->id);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-011
    |--------------------------------------------------------------------------
    */

    public function test_login_does_not_modify_user_roles_or_permissions(): void
    {
        $user = User::factory()->create();

        $rolesBefore = $user->roles()->pluck('roles.id')->sort()->values()->all();
        $permissionsBefore = $user->permissions()->pluck('permissions.id')->sort()->values()->all();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $user->refresh();

        $rolesAfter = $user->roles()->pluck('roles.id')->sort()->values()->all();
        $permissionsAfter = $user->permissions()->pluck('permissions.id')->sort()->values()->all();

        $this->assertSame($rolesBefore, $rolesAfter);
        $this->assertSame($permissionsBefore, $permissionsAfter);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH-012
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_identity_remains_available_to_security_flow(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $identityService = app(
            \App\Core\Security\Services\IdentityService::class
        );

        $identity = $identityService->current();

        $this->assertTrue($identity->authenticated);
        $this->assertSame($user->id, $identity->id);

        $this->assertIsArray($identity->roles);
        $this->assertIsArray($identity->permissions);
    }
}
