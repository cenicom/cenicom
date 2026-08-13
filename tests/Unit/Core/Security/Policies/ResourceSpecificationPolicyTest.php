<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\ResourceSpecificationInterface;
use App\Core\Security\Policies\ResourceSpecificationPolicy;
use Tests\TestCase;

final class ResourceSpecificationPolicyTest extends TestCase
{
    public function test_delegates_identity_and_resource_to_specification(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = $this->createMock(
            ResourceSpecificationInterface::class
        );

        $specification
            ->expects($this->once())
            ->method('isSatisfiedBy')
            ->with(
                $identity,
                $resource,
            )
            ->willReturn(true);

        $policy = new ResourceSpecificationPolicy(
            $specification
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    public function test_returns_true_when_specification_allows(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = $this->createMock(
            ResourceSpecificationInterface::class
        );

        $specification
            ->method('isSatisfiedBy')
            ->with(
                $identity,
                $resource,
            )
            ->willReturn(true);

        $policy = new ResourceSpecificationPolicy(
            $specification
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }

    public function test_returns_false_when_specification_denies(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = $this->createMock(
            ResourceSpecificationInterface::class
        );

        $specification
            ->method('isSatisfiedBy')
            ->with(
                $identity,
                $resource,
            )
            ->willReturn(false);

        $policy = new ResourceSpecificationPolicy(
            $specification
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                $resource,
            )
        );
    }
}
