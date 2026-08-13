<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;

use App\Core\Security\Identity\Identity;
use App\Core\Security\Providers\SecurityServiceProvider;
use Tests\TestCase;

final class InstitutionAuthorizationTest extends TestCase
{
    public function test_identity_with_institution_view_permission_is_authorized(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: 1,
            name: 'Institution User',
            permissions: [
                'institutions.view',
            ],
        );

        self::assertTrue(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_identity_without_institution_view_permission_is_denied(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new Identity(
            id: 2,
            name: 'Restricted User',
        );

        self::assertFalse(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }
}
