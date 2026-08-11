<?php

declare(strict_types=1);

namespace App\Core\Security\Services;

use App\Core\Security\DTO\IdentityData;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class IdentityService
{
    public function __construct(
        private readonly AuthFactory $auth
    ) {
    }

    /**
     * Obtiene la identidad actual.
     */
    public function current(): IdentityData
    {
        $user = $this->auth->guard()->user();

        if ($user === null) {
            return IdentityData::guest();
        }

        return new IdentityData(
            id: $user->getAuthIdentifier(),
            name: $this->resolveName($user),
            roles: $this->resolveRoles($user),
            permissions: $this->resolvePermissions($user),
            authenticated: true,
        );
    }

    /**
     * Determina si existe identidad autenticada.
     */
    public function authenticated(): bool
    {
        return $this->auth
            ->guard()
            ->check();
    }

    /**
     * Obtiene el nombre de la identidad.
     */
    private function resolveName(object $user): string
    {
        $firstName = $user->first_name ?? null;
        $lastName = $user->last_name ?? null;

        if ($firstName !== null || $lastName !== null) {
            return trim(
                sprintf(
                    '%s %s',
                    $firstName ?? '',
                    $lastName ?? ''
                )
            );
        }

        return $user->name ?? 'Unknown';
    }

    /**
     * Obtiene roles actuales.
     *
     * @return array<int, string>
     */
    private function resolveRoles(object $user): array
    {
        if (!method_exists($user, 'roles')) {
            return [];
        }

        return $user
            ->roles()
            ->pluck('name')
            ->toArray();
    }

    /**
     * Obtiene permisos efectivos.
     *
     * Combina permisos directos y permisos heredados
     * desde los roles de la identidad.
     *
     * @return array<int, string>
     */
    private function resolvePermissions(object $user): array
    {
        $permissions = [];

        /*
         * Permisos asignados directamente al usuario.
         */
        if (method_exists($user, 'permissions')) {
            $permissions = $user
                ->permissions()
                ->pluck('name')
                ->toArray();
        }

        /*
         * Permisos heredados desde los roles.
         */
        if (method_exists($user, 'roles')) {
            $roles = $user->roles();

            /*
             * Las relaciones Eloquent permiten cargar
             * los permisos de los roles.
             */
            if ($roles instanceof BelongsToMany) {
                $rolePermissions = $roles
                    ->with('permissions')
                    ->get()
                    ->flatMap(
                        fn ($role) => $role->permissions->pluck('name')
                    )
                    ->toArray();

                $permissions = array_merge(
                    $permissions,
                    $rolePermissions
                );
            }
        }

        return array_values(
            array_unique($permissions)
        );
    }
}
