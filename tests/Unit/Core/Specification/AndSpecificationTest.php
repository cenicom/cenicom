<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Specification;

use App\Core\Specification\AndSpecification;
use App\Core\Specification\Contracts\SpecificationInterface;
use PHPUnit\Framework\TestCase;

final class AndSpecificationTest extends TestCase
{
    public function test_returns_true_when_single_specification_is_satisfied(): void
    {
        // Arrange

        $specification = $this->createSpecification(true);

        $andSpecification = new AndSpecification(
            $specification
        );

        // Act

        $result = $andSpecification->isSatisfiedBy(
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

        $andSpecification = new AndSpecification(
            $specification
        );

        // Act

        $result = $andSpecification->isSatisfiedBy(
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

        $andSpecification = new AndSpecification(

            $this->createSpecification(true),

            $this->createSpecification(true),

            $this->createSpecification(true),

        );

        // Act

        $result = $andSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }


    public function test_returns_false_when_one_specification_fails(): void
    {
        // Arrange

        $andSpecification = new AndSpecification(

            $this->createSpecification(true),

            $this->createSpecification(false),

            $this->createSpecification(true),

        );

        // Act

        $result = $andSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
            $result
        );
    }


    public function test_stops_evaluation_after_first_failed_specification(): void
    {
        // Arrange

        $first = $this->createSpecification(
            false
        );

        $second = $this->createMock(
            SpecificationInterface::class
        );

        $second
            ->expects($this->never())
            ->method('isSatisfiedBy');

        $andSpecification = new AndSpecification(
            $first,
            $second,
        );

        // Act

        $result = $andSpecification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertFalse(
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
