<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;
use App\Core\Specification\NotSpecification;
use PHPUnit\Framework\TestCase;

final class NotSpecificationTest extends TestCase
{
    public function test_returns_false_when_inner_specification_is_satisfied(): void
    {
        // Arrange

        $specification = $this->createSpecification(true);

        $notSpecification = new NotSpecification(
            $specification
        );

        // Act

        $result = $notSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_returns_true_when_inner_specification_is_not_satisfied(): void
    {
        // Arrange

        $specification = $this->createSpecification(false);

        $notSpecification = new NotSpecification(
            $specification
        );

        // Act

        $result = $notSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    private function createSpecification(
        bool $result
    ): SpecificationInterface {

        $specification = $this->createMock(
            SpecificationInterface::class
        );

        $specification
            ->method('isSatisfiedBy')
            ->willReturn($result);

        return $specification;
    }
}
