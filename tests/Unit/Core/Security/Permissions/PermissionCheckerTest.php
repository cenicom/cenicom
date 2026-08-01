<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Permissions\DTO\PermissionDefinition;
use App\Core\Security\Permissions\PermissionChecker;
use App\Core\Security\Permissions\PermissionRegistry;
use PHPUnit\Framework\TestCase;

final class PermissionCheckerTest extends TestCase
{
    private PermissionRegistry $registry;

    private PermissionChecker $checker;


    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new PermissionRegistry();

        $this->checker = new PermissionChecker(
            $this->registry
        );
    }


    public function test_authenticated_identity_allows_registered_permission(): void
    {
        $this->registry->register(
            new PermissionDefinition(
                name: 'inventory.products.create'
            )
        );

        $identity = new FakeIdentity(
            authenticated: true,
            permissions: ['inventory.products.create']
        );

        $this->assertTrue(
            $this->checker->can(
                $identity,
                'inventory.products.create'
            )
        );
    }


    public function test_authenticated_identity_denies_unknown_permission(): void
    {
        $identity = new FakeIdentity(
            authenticated: true,
            permissions: ['inventory.products.create']
        );

        $this->assertFalse(
            $this->checker->can(
                $identity,
                'inventory.products.delete'
            )
        );
    }


    public function test_guest_identity_is_denied(): void
    {
        $this->registry->register(
            new PermissionDefinition(
                name: 'inventory.products.create'
            )
        );

        $identity = new FakeIdentity(
            authenticated: false
        );

        $this->assertFalse(
            $this->checker->can(
                $identity,
                'inventory.products.create'
            )
        );
    }
}


/**
 * Fake Identity para pruebas.
 */
final class FakeIdentity implements IdentityInterface
{
    public function __construct(
        private readonly bool $authenticated = true,
        private readonly array $permissions = [],
        private readonly array $roles = [],
        private readonly int|string|null $id = 1,
        private readonly string $name = 'Test User',
    ) {}

    public function id(): int|string|null
    {
        return $this->authenticated
            ? $this->id
            : null;
    }

    public function name(): string
    {
        return $this->authenticated
            ? $this->name
            : '';
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public function permissions(): array
    {
        return $this->permissions;
    }

    public function can(string $permission): bool
    {
        return in_array(
            $permission,
            $this->permissions,
            true
        );
    }

    public function authenticated(): bool
    {
        return $this->authenticated;
    }
}
