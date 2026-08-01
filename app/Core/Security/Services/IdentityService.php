<?php

declare(strict_types=1);

namespace App\Core\Security\Services;

use App\Core\Security\DTO\IdentityData;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

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
            name: $user->name ?? 'Unknown',
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
     * Obtiene roles actuales.
     *
     * Preparado para integración CN-SEC-003.
     *
     * @return array<int,string>
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
     * Obtiene permisos actuales.
     *
     * Preparado para integración CN-SEC-002.
     *
     * @return array<int,string>
     */
    private function resolvePermissions(object $user): array
    {
        if (!method_exists($user, 'permissions')) {
            return [];
        }

        return $user
            ->permissions()
            ->pluck('name')
            ->toArray();
    }
}
