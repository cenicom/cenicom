<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\RoleChecker;
use PHPUnit\Framework\TestCase;

final class RoleCheckerTest extends TestCase
{
    private RoleChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checker = new RoleChecker();
    }

    //1️⃣ authenticated identity has role
    public function test_authenticated_identity_has_role(): void
    {
        $identity = new FakeIdentity(
            authenticated: true,
            roles: [
                'administrator',
                'teacher',
            ]
        );

        $this->assertTrue(
            $this->checker->hasRole(
                $identity,
                'administrator'
            )
        );
    }

    //2️⃣ authenticated identity lacks role
    public function test_authenticated_identity_lacks_role(): void
    {
        $identity = new FakeIdentity(
            authenticated: true,
            roles: [
                'teacher',
            ]
        );

        $this->assertFalse(
            $this->checker->hasRole(
                $identity,
                'administrator'
            )
        );
    }

    //3️⃣ guest identity is denied
    public function test_guest_identity_is_denied(): void
    {
        $identity = new FakeIdentity(
            authenticated: false,
            roles: [
                'administrator',
            ]
        );

        $this->assertFalse(
            $this->checker->hasRole(
                $identity,
                'administrator'
            )
        );
    }
}

/**
 * Fake Identity para pruebas.
 */
final class FakeIdentity implements IdentityInterface
{
    /**
     * @param array<int, string> $roles
     */
    public function __construct(
        private readonly bool $authenticated,
        private readonly array $roles = [],
    ) {}

    public function id(): int|string|null
    {
        return $this->authenticated ? 1 : null;
    }

    public function name(): string
    {
        return $this->authenticated
            ? 'Test User'
            : '';
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public function permissions(): array
    {
        return [];
    }

    public function can(string $permission): bool
    {
        return false;
    }

    public function authenticated(): bool
    {
        return $this->authenticated;
    }
}
