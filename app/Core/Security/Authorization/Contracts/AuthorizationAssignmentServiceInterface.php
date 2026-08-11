<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization\Contracts;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

interface AuthorizationAssignmentServiceInterface
{
    public function assignRole(
        User $user,
        Role $role
    ): void;

    public function revokeRole(
        User $user,
        Role $role
    ): void;

    public function grantPermission(
        User $user,
        Permission $permission
    ): void;

    public function revokePermission(
        User $user,
        Permission $permission
    ): void;

    public function grantPermissionToRole(
        Role $role,
        Permission $permission
    ): void;

    public function revokePermissionFromRole(
        Role $role,
        Permission $permission
    ): void;
}
