<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Specification;

use App\Core\Specification\AlwaysFalseSpecification;
use PHPUnit\Framework\TestCase;

final class AlwaysFalseSpecificationTest extends TestCase
{

    /**
     * @covers \App\Core\Specification\AlwaysFalseSpecification::isSatisfiedBy
     */
    public function test_returns_false_for_object(): void
    {
        $specification = new AlwaysFalseSpecification();

        $this->assertFalse(
            $specification->isSatisfiedBy(
                new \stdClass()
            )
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysFalseSpecification::isSatisfiedBy
     */
    public function test_returns_false_for_array(): void
    {
        $specification = new AlwaysFalseSpecification();

        $this->assertFalse(
            $specification->isSatisfiedBy([])
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysFalseSpecification::isSatisfiedBy
     */
    public function test_returns_false_for_scalar(): void
    {
        $specification = new AlwaysFalseSpecification();

        $this->assertFalse(
            $specification->isSatisfiedBy(123)
        );
    }

    /**
     * @covers \App\Core\Specification\AlwaysFalseSpecification::isSatisfiedBy
     */
    public function test_returns_false_for_null(): void
    {
        $specification = new AlwaysFalseSpecification();

        $this->assertFalse(
            $specification->isSatisfiedBy(null)
        );
    }
}
