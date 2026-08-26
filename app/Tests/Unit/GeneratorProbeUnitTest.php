<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

use App\Models\GeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Prueba unitaria del módulo GeneratorProbe.
 *
 * @package Tests\Feature
 * @since 1.0.0
 */
final class GeneratorProbeUnitTest
    extends TestCase
{
    /**
     * Verifica que el modelo pueda instanciarse.
     */
    public function test_can_create_model_instance(): void
    {
        $model = new GeneratorProbe();

        $this->assertInstanceOf(
            GeneratorProbe::class,
            $model
        );
    }

    /**
     * Verifica que la clase del modelo exista.
     */
    public function test_model_class_exists(): void
    {
        $this->assertTrue(
            class_exists(GeneratorProbe::class)
        );
    }
}
