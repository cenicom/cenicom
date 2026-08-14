<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\SpecificationPolicyInterface;
use Tests\TestCase;

final class SpecificationPolicyContractTest extends TestCase
{
    public function test_contract_can_be_implemented(): void
    {
        $policy = new class implements SpecificationPolicyInterface {
            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return true;
            }
        };

        self::assertInstanceOf(
            PolicyInterface::class,
            $policy,
        );

        self::assertTrue(
            $policy->allows(
                $this->createMock(IdentityInterface::class),
                new \stdClass(),
            ),
        );
    }
}
