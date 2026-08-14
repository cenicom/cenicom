<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\OwnedResourceInterface;
use App\Core\Security\Policies\ResourceSpecificationPolicy;
use App\Core\Security\Specifications\ResourceOwnerSpecification;
use Tests\TestCase;

final class ResourceSpecificationPolicyIntegrationTest extends TestCase
{
    public function test_policy_allows_resource_owner(): void
    {
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

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    public function test_policy_denies_non_owner(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(10);

        $resource = new class implements OwnedResourceInterface {
            public function ownerId(): int
            {
                return 20;
            }
        };

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    public function test_policy_denies_resource_that_is_not_owned_resource(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(10);

        $policy = new ResourceSpecificationPolicy(
            new ResourceOwnerSpecification()
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                new \stdClass(),
            )
        );
    }
}
