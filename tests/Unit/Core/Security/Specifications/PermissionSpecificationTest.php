<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Specifications\PermissionSpecification;
use PHPUnit\Framework\TestCase;

final class PermissionSpecificationTest extends TestCase
{
    public function test_returns_true_when_identity_has_permission(): void
    {
        // Arrange

        $identity = $this->createIdentity(true);

        $specification = new PermissionSpecification(
            'users.view'
        );

        // Act

        $result = $specification->isSatisfiedBy(
            $identity
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    public function test_returns_false_when_identity_does_not_have_permission(): void
    {
        // Arrange

        $identity = $this->createIdentity(false);

        $specification = new PermissionSpecification(
            'users.view'
        );

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

        $specification = new PermissionSpecification(
            'users.view'
        );

        // Act

        $result = $specification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_calls_identity_with_expected_permission(): void
    {
        // Arrange

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->expects($this->once())
            ->method('can')
            ->with('users.view')
            ->willReturn(true);

        $specification = new PermissionSpecification(
            'users.view'
        );

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
        bool $result
    ): IdentityInterface {

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('can')
            ->willReturn($result);

        return $identity;
    }
}
