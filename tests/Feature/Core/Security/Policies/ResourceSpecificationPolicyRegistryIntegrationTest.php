<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use App\Core\Security\Policies\ResourceSpecificationPolicy;
use App\Core\Security\Specifications\ResourceOwnerSpecification;
use App\Core\Security\Policies\Contracts\OwnedResourceInterface;
use Tests\TestCase;

final class ResourceSpecificationPolicyRegistryIntegrationTest extends TestCase
{
    public function test_resource_specification_policy_can_be_registered_and_resolved(): void
    {
        $registry = $this->app->make(
            PolicyRegistryInterface::class
        );

        $resolver = $this->app->make(
            PolicyResolverInterface::class
        );

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        $registry->register(
            'resource-owner',
            $policy,
        );

        $resolved = $resolver->resolve(
            'resource-owner'
        );

        self::assertSame(
            $policy,
            $resolved,
        );

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(10);

        $resource = new class implements OwnedResourceInterface {
            public function ownerId(): int
            {
                return 10;
            }
        };

        self::assertTrue(
            $resolved->allows(
                $identity,
                $resource,
            )
        );
    }
}
