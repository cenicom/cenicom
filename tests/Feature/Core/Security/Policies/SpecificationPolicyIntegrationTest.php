<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\SpecificationPolicy;
use App\Core\Specification\AlwaysFalseSpecification;
use App\Core\Specification\AlwaysTrueSpecification;
use Tests\TestCase;

final class SpecificationPolicyIntegrationTest extends TestCase
{
    public function test_policy_allows_when_real_specification_is_satisfied(): void
    {
        $policy = new SpecificationPolicy(
            new AlwaysTrueSpecification()
        );

        $identity = $this->createMock(
            IdentityInterface::class
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                new \stdClass(),
            )
        );
    }

    public function test_policy_denies_when_real_specification_is_not_satisfied(): void
    {
        $policy = new SpecificationPolicy(
            new AlwaysFalseSpecification()
        );

        $identity = $this->createMock(
            IdentityInterface::class
        );

        self::assertFalse(
            $policy->allows(
                $identity,
                new \stdClass(),
            )
        );
    }
}
