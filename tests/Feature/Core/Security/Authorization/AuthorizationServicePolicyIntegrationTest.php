<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use Tests\TestCase;

final class AuthorizationServicePolicyIntegrationTest extends TestCase
{
    public function test_authorization_service_resolves_registered_policy(): void
    {
        $registry = $this->app->make(
            PolicyRegistryInterface::class
        );

        $policy = new class implements PolicyInterface {
            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return true;
            }
        };

        $registry->register(
            'institution',
            $policy,
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $identity = new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Test User';
            }

            public function roles(): array
            {
                return [];
            }

            public function permissions(): array
            {
                return [];
            }

            public function can(
                string $permission
            ): bool {
                return false;
            }

            public function authenticated(): bool
            {
                return true;
            }
        };

        self::assertTrue(
            $authorization->allows(
                $identity,
                'institution',
                new \stdClass(),
            )
        );
    }
}
