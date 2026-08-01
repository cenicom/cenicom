<?php

declare(strict_types=1);

namespace App\Core\Security\DTO;

final readonly class IdentityData
{
    /**
     * Construye un contexto de identidad.
     *
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    public function __construct(
        public int|string|null $id,
        public string $name,
        public array $roles = [],
        public array $permissions = [],
        public bool $authenticated = false,
    ) {
    }

    /**
     * Verifica si la identidad posee un permiso.
     */
    public function can(string $permission): bool
    {
        return in_array(
            $permission,
            $this->permissions,
            true
        );
    }

    /**
     * Verifica si la identidad posee un rol.
     */
    public function hasRole(string $role): bool
    {
        return in_array(
            $role,
            $this->roles,
            true
        );
    }

    /**
     * Devuelve identidad anónima.
     */
    public static function guest(): self
    {
        return new self(
            id: null,
            name: 'Guest',
            roles: [],
            permissions: [],
            authenticated: false,
        );
    }

    /**
     * Convierte la identidad a arreglo.
     *
     * Útil para auditoría y diagnóstico.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
            'authenticated' => $this->authenticated,
        ];
    }
}
