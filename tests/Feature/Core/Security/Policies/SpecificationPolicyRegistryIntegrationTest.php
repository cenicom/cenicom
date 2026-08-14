<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use App\Core\Security\Policies\SpecificationPolicy;
use App\Core\Specification\AlwaysTrueSpecification;
use Tests\TestCase;

final class SpecificationPolicyRegistryIntegrationTest extends TestCase
{
    public function test_specification_policy_can_be_registered_and_resolved(): void
    {
        $registry = $this->app->make(
            PolicyRegistryInterface::class
        );

        $resolver = $this->app->make(
            PolicyResolverInterface::class
        );

        $policy = new SpecificationPolicy(
            new AlwaysTrueSpecification()
        );

        $registry->register(
            'institution',
            $policy,
        );

        $resolved = $resolver->resolve(
            'institution'
        );

        self::assertSame(
            $policy,
            $resolved,
        );

        self::assertTrue(
            $resolved->allows(
                $this->createMock(
                    IdentityInterface::class
                ),
                new \stdClass(),
            )
        );
    }
}
