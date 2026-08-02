<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Specifications\RoleSpecification;
use PHPUnit\Framework\TestCase;

final class RoleSpecificationTest extends TestCase
{
    public function test_returns_true_when_identity_has_role(): void
    {
        // Arrange

        $identity = $this->createIdentity([
            'administrator',
            'teacher',
        ]);

        $specification = new RoleSpecification(
            'administrator'
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

    public function test_returns_false_when_identity_does_not_have_role(): void
    {
        // Arrange

        $identity = $this->createIdentity([
            'teacher',
            'student',
        ]);

        $specification = new RoleSpecification(
            'administrator'
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

        $specification = new RoleSpecification(
            'administrator'
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

    public function test_calls_roles_on_identity(): void
    {
        // Arrange

        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->expects($this->once())
            ->method('roles')
            ->willReturn([
                'administrator',
            ]);

        $specification = new RoleSpecification(
            'administrator'
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

    public function test_uses_strict_role_comparison(): void
    {
        // Arrange

        $identity = $this->createIdentity([
            '1',
        ]);

        $specification = new RoleSpecification(
            '1'
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
        array $roles
    ): IdentityInterface {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('roles')
            ->willReturn(
                $roles
            );

        return $identity;
    }
}
