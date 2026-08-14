<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use Tests\TestCase;

final class PolicyResolutionIntegrationTest extends TestCase
{
    public function test_registry_resolver_and_policy_are_integrated(): void
    {
        $registry = $this->app->make(
            PolicyRegistryInterface::class
        );

        $resolver = $this->app->make(
            PolicyResolverInterface::class
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

        self::assertSame(
            $policy,
            $resolver->resolve('institution'),
        );
    }

    public function test_resolver_returns_null_for_unregistered_policy(): void
    {
        $resolver = $this->app->make(
            PolicyResolverInterface::class
        );

        self::assertNull(
            $resolver->resolve('institution'),
        );
    }
}
