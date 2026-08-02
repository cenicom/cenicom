<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;
use App\Core\Specification\OrSpecification;
use PHPUnit\Framework\TestCase;

final class OrSpecificationTest extends TestCase
{
    public function test_returns_true_when_single_specification_is_satisfied(): void
    {
        // Arrange

        $specification = $this->createSpecification(true);

        $orSpecification = new OrSpecification(
            $specification
        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    public function test_returns_false_when_single_specification_is_not_satisfied(): void
    {
        // Arrange

        $specification = $this->createSpecification(false);

        $orSpecification = new OrSpecification(
            $specification
        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_returns_true_when_all_specifications_are_satisfied(): void
    {
        // Arrange

        $orSpecification = new OrSpecification(

            $this->createSpecification(true),

            $this->createSpecification(true),

            $this->createSpecification(true),

        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    public function test_returns_false_when_all_specifications_fail(): void
    {
        // Arrange

        $orSpecification = new OrSpecification(

            $this->createSpecification(false),

            $this->createSpecification(false),

            $this->createSpecification(false),

        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }

    public function test_returns_true_when_one_specification_is_satisfied(): void
    {
        // Arrange

        $orSpecification = new OrSpecification(

            $this->createSpecification(false),

            $this->createSpecification(false),

            $this->createSpecification(true),

            $this->createSpecification(false),

        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    public function test_stops_evaluation_after_first_satisfied_specification(): void
    {
        // Arrange

        $first = $this->createSpecification(
            true
        );

        $second = $this->createMock(
            SpecificationInterface::class
        );

        $second
            ->expects($this->never())
            ->method('isSatisfiedBy');

        $orSpecification = new OrSpecification(
            $first,
            $second,
        );

        // Act

        $result = $orSpecification->isSatisfiedBy(
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
