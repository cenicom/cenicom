<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;
use PHPUnit\Framework\TestCase;

final class AuthorizationServiceTest extends TestCase
{
    private AuthorizationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AuthorizationService(
            new FakePermissionChecker()
        );
    }

    public function test_delegates_permission_check(): void
    {
        //
    }

    public function test_allows_granted_permission(): void
    {
        //
    }

    public function test_denies_missing_permission(): void
    {
        //
    }
}

/**
 * Fake Permission Checker para pruebas.
 */
final class FakePermissionChecker implements PermissionCheckerInterface
{
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {
        return $identity->can($permission);
    }
}

/**
 * Fake Identity para pruebas.
 */
final class FakeIdentity implements IdentityInterface
{
    /**
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    public function __construct(
        private readonly bool $authenticated = true,
        private readonly array $roles = [],
        private readonly array $permissions = [],
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
