<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Identity\Identity;
use App\Core\Security\Providers\SecurityServiceProvider;
use App\Core\Security\Roles\Contracts\RoleRegistrarInterface;
use Tests\TestCase;

final class InstitutionRoleAuthorizationTest extends TestCase
{
    public function test_role_grants_institution_view_permission(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $roles = $this->app->make(
            RoleRegistrarInterface::class
        );

        $roles->register(
            name: 'institution_manager',
            label: 'Institution Manager',
            permissions: [
                'institutions.view',
            ],
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: 1,
            name: 'Institution Manager',
            roles: [
                'institution_manager',
            ],
        );

        self::assertTrue(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_role_without_institution_view_permission_is_denied(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $roles = $this->app->make(
            RoleRegistrarInterface::class
        );

        $roles->register(
            name: 'institution_viewer_restricted',
            label: 'Restricted Institution Viewer',
            permissions: [
                'institutions.create',
            ],
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: 2,
            name: 'Restricted User',
            roles: [
                'institution_viewer_restricted',
            ],
        );

        self::assertFalse(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_any_role_can_grant_institution_view_permission(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $roles = $this->app->make(
            RoleRegistrarInterface::class
        );

        $roles->register(
            name: 'institution_basic',
            label: 'Institution Basic',
            permissions: [
                'institutions.create',
            ],
        );

        $roles->register(
            name: 'institution_manager',
            label: 'Institution Manager',
            permissions: [
                'institutions.view',
            ],
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: 3,
            name: 'Multi Role User',
            roles: [
                'institution_basic',
                'institution_manager',
            ],
        );

        self::assertTrue(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_guest_cannot_receive_permission_from_role(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $roles = $this->app->make(
            RoleRegistrarInterface::class
        );

        $roles->register(
            name: 'institution_manager',
            label: 'Institution Manager',
            permissions: [
                'institutions.view',
            ],
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: null,
            name: 'Guest',
            roles: [
                'institution_manager',
            ],
        );

        self::assertFalse(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }
}
