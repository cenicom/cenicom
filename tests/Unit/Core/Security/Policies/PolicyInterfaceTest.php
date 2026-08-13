<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use PHPUnit\Framework\TestCase;

final class PolicyInterfaceTest extends TestCase
{
    public function test_policy_contract_can_be_implemented(): void
    {
        $policy = new class implements PolicyInterface {
            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return true;
            }
        };

        $identity = $this->createMock(
            IdentityInterface::class
        );

        self::assertTrue(
            $policy->allows(
                $identity,
                new \stdClass()
            )
        );
    }
}
