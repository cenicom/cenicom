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

    /**
     * @test
     * 1. Doble negación
     * Este caso verifica que la composición funciona correctamente.
     */
    public function test_double_negation_returns_original_result(): void
    {
        $specification = $this->createSpecification(true);

        $not = new NotSpecification(
            new NotSpecification($specification)
        );

        $this->assertTrue(
            $not->isSatisfiedBy(new \stdClass())
        );
    }

    /**
     * @test
     * 2. Propaga correctamente el candidato
     * Actualmente el mock devuelve un valor fijo, pero no certifica que el mismo objeto recibido por NotSpecification se entregue a la Specification interna.
     */
    public function test_passes_candidate_to_inner_specification(): void
    {
        $candidate = new \stdClass();

        $specification = $this->createMock(
            SpecificationInterface::class
        );

        $specification
            ->expects($this->once())
            ->method('isSatisfiedBy')
            ->with($candidate)
            ->willReturn(true);

        $not = new NotSpecification($specification);

        $not->isSatisfiedBy($candidate);
    }
}
