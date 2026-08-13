<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\SpecificationPolicy;
use App\Core\Specification\Contracts\SpecificationInterface;
use Mockery;
use Tests\TestCase;

final class SpecificationPolicyTest extends TestCase
{
    public function test_allows_when_specification_is_satisfied(): void
    {
        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = Mockery::mock(
            SpecificationInterface::class
        );

        $specification
            ->shouldReceive('isSatisfiedBy')
            ->once()
            ->with($identity)
            ->andReturn(true);

        $policy = new SpecificationPolicy(
            $specification
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource
            )
        );
    }

    public function test_denies_when_specification_is_not_satisfied(): void
    {
        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = Mockery::mock(
            SpecificationInterface::class
        );

        $specification
            ->shouldReceive('isSatisfiedBy')
            ->once()
            ->with($identity)
            ->andReturn(false);

        $policy = new SpecificationPolicy(
            $specification
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                $resource
            )
        );
    }

    public function test_resource_is_accepted_by_policy_contract(): void
    {
        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = Mockery::mock(
            SpecificationInterface::class
        );

        $specification
            ->shouldReceive('isSatisfiedBy')
            ->once()
            ->with($identity)
            ->andReturn(true);

        $policy = new SpecificationPolicy(
            $specification
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                $resource
            )
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
