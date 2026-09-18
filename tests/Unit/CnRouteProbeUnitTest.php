<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Modules\CnRouteProbe\Models\CnRouteProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Prueba unitaria del módulo CnRouteProbe.
 *
 * @package Tests\Unit
 * @since 1.0.0
 */
final class CnRouteProbeUnitTest
    extends TestCase
{
    /**
     * Verifica que el modelo pueda instanciarse.
     */
    public function test_can_create_model_instance(): void
    {
        $model = new CnRouteProbe();

        $this->assertInstanceOf(
            CnRouteProbe::class,
            $model
        );
    }

    /**
     * Verifica que la clase del modelo exista.
     */
    public function test_model_class_exists(): void
    {
        $this->assertTrue(
            class_exists(CnRouteProbe::class)
        );
    }
}
