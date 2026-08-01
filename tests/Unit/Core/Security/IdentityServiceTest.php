<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use App\Core\Security\DTO\IdentityData;
use App\Core\Security\Services\IdentityService;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use PHPUnit\Framework\TestCase;

final class IdentityServiceTest extends TestCase
{
    public function test_returns_guest_identity_without_authentication(): void
    {
        $guard = new FakeGuard();

        $auth = new FakeAuthFactory($guard);

        $service = new IdentityService($auth);

        $identity = $service->current();

        $this->assertInstanceOf(
            IdentityData::class,
            $identity
        );

        $this->assertNull(
            $identity->id
        );

        $this->assertFalse(
            $identity->authenticated
        );

        $this->assertSame(
            'Guest',
            $identity->name
        );
    }


    public function test_returns_authenticated_identity(): void
    {
        $user = new FakeUser(
            id: 10,
            name: 'Administrator'
        );

        $guard = new FakeGuard($user);

        $auth = new FakeAuthFactory($guard);

        $service = new IdentityService($auth);

        $identity = $service->current();

        $this->assertSame(
            10,
            $identity->id
        );

        $this->assertSame(
            'Administrator',
            $identity->name
        );

        $this->assertTrue(
            $identity->authenticated
        );
    }


    public function test_reports_authentication_state(): void
    {
        $guard = new FakeGuard();

        $service = new IdentityService(
            new FakeAuthFactory($guard)
        );

        $this->assertFalse(
            $service->authenticated()
        );
    }
}


/**
 * Fake Auth Factory
 */
final class FakeAuthFactory implements AuthFactory
{
    public function __construct(
        private readonly FakeGuard $guard
    ) {}

    public function guard($name = null)
    {
        return $this->guard;
    }

    public function shouldUse($name)
    {
        return $this;
    }
}


/**
 * Fake Guard
 */
final class FakeGuard
{
    public function __construct(
        private readonly ?FakeUser $user = null
    ) {}

    public function user(): ?FakeUser
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }
}


/**
 * Fake User
 */
final class FakeUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {}

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }
}
