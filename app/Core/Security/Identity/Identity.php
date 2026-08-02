<?php

declare(strict_types=1);

namespace App\Core\Security\Identity;

use App\Core\Security\Contracts\IdentityInterface;

final readonly class Identity implements IdentityInterface
{
    /**
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    public function __construct(
        private int|string|null $id = null,
        private string $name = 'Guest',
        private array $roles = [],
        private array $permissions = [],
    ) {
    }

    public function id(): int|string|null
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, string>
     */
    public function roles(): array
    {
        return $this->roles;
    }

    /**
     * @return array<int, string>
     */
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
        return $this->id !== null;
    }
}
