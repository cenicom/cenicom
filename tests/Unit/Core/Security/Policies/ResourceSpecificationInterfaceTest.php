<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\ResourceSpecificationInterface;
use Tests\TestCase;

final class ResourceSpecificationInterfaceTest extends TestCase
{
    public function test_contract_can_evaluate_identity_and_resource(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $resource = new \stdClass();

        $specification = new class implements ResourceSpecificationInterface {
            public function isSatisfiedBy(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return $resource instanceof \stdClass;
            }
        };

        self::assertTrue(
            $specification->isSatisfiedBy(
                $identity,
                $resource
            )
        );
    }
}
