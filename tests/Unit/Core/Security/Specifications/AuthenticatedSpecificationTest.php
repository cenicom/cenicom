<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Specifications\AuthenticatedSpecification;
use PHPUnit\Framework\TestCase;

final class AuthenticatedSpecificationTest extends TestCase
{
    public function test_returns_true_when_identity_is_authenticated(): void
    {
        // Arrange

        $identity = $this->createIdentity(true);

        $specification = new AuthenticatedSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            $identity
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    public function test_returns_false_when_identity_is_not_authenticated(): void
    {
        // Arrange

        $identity = $this->createIdentity(false);

        $specification = new AuthenticatedSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            $identity
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_returns_false_when_candidate_is_not_identity(): void
    {
        // Arrange

        $specification = new AuthenticatedSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_calls_authenticated_on_identity(): void
    {
        // Arrange

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->expects($this->once())
            ->method('authenticated')
            ->willReturn(true);

        $specification = new AuthenticatedSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            $identity
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    private function createIdentity(
        bool $authenticated
    ): IdentityInterface {

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('authenticated')
            ->willReturn(
                $authenticated
            );

        return $identity;
    }
}
