<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Specification;

use App\Core\Specification\AlwaysTrueSpecification;
use PHPUnit\Framework\TestCase;

final class AlwaysTrueSpecificationTest extends TestCase
{
    /**
     * @covers \App\Core\Specification\AlwaysTrueSpecification::isSatisfiedBy
     */
    public function test_returns_true_for_object(): void
    {
        // Arrange

        $specification = new AlwaysTrueSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            new \stdClass()
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysTrueSpecification::isSatisfiedBy
     */
    public function test_returns_true_for_array(): void
    {
        // Arrange

        $specification = new AlwaysTrueSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            []
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysTrueSpecification::isSatisfiedBy
     */
    public function test_returns_true_for_scalar(): void
    {
        // Arrange

        $specification = new AlwaysTrueSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            123
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysTrueSpecification::isSatisfiedBy
     */
    public function test_returns_true_for_null(): void
    {
        // Arrange

        $specification = new AlwaysTrueSpecification();

        // Act

        $result = $specification->isSatisfiedBy(
            null
        );

        // Assert

        $this->assertTrue(
            $result
        );
    }
}
