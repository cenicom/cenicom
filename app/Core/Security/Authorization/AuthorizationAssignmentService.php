<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\AuthorizationAssignmentServiceInterface;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;


final readonly class AuthorizationAssignmentService
    implements AuthorizationAssignmentServiceInterface
{
    /**
     * Asigna un rol a un usuario.
     */
    public function assignRole(
        User $user,
        Role $role
    ): void {
        DB::transaction(function () use ($user, $role): void {
            $user->roles()->syncWithoutDetaching([
                $role->getKey(),
            ]);

            event(
                AuthorizationChanged::user(
                    $this->identityId($user)
                )
            );
        });
    }

    /**
     * Retira un rol de un usuario.
     */
    public function revokeRole(
        User $user,
        Role $role
    ): void {
        DB::transaction(function () use ($user, $role): void {
            $user->roles()->detach(
                $role->getKey()
            );

            event(
                AuthorizationChanged::user(
                    $this->identityId($user)
                )
            );
        });
    }

    /**
     * Concede un permiso directamente a un usuario.
     */
    public function grantPermission(
        User $user,
        Permission $permission
    ): void {
        DB::transaction(function () use ($user, $permission): void {
            $user->permissions()->syncWithoutDetaching([
                $permission->getKey(),
            ]);

            event(
                AuthorizationChanged::user(
                    $this->identityId($user)
                )
            );
        });
    }

    /**
     * Revoca un permiso directo de un usuario.
     */
    public function revokePermission(
        User $user,
        Permission $permission
    ): void {
        DB::transaction(function () use ($user, $permission): void {
            $user->permissions()->detach(
                $permission->getKey()
            );

            event(
                AuthorizationChanged::user(
                    $this->identityId($user)
                )
            );
        });
    }

    /**
     * Concede un permiso a un rol.
     *
     * Un cambio de permisos de rol puede afectar
     * a múltiples usuarios.
     */
    public function grantPermissionToRole(
        Role $role,
        Permission $permission
    ): void {
        DB::transaction(function () use ($role, $permission): void {
            $role->permissions()->syncWithoutDetaching([
                $permission->getKey(),
            ]);

            event(
                AuthorizationChanged::role(
                    $role->name
                )
            );
        });
    }

    /**
     * Revoca un permiso de un rol.
     */
    public function revokePermissionFromRole(
        Role $role,
        Permission $permission
    ): void {
        DB::transaction(function () use ($role, $permission): void {
            $role->permissions()->detach(
                $permission->getKey()
            );

            event(
                AuthorizationChanged::role(
                    $role->name
                )
            );
        });
    }

    private function identityId(
        User $user
    ): int|string {
        $id = $user->getAuthIdentifier();

        if ($id === null) {
            throw new \LogicException(
                'An authorization identity must have an identifier.'
            );
        }

        return $id;
    }
}
