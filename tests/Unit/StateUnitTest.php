<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Modules\State\Models\State;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Prueba unitaria del módulo State.
 *
 * @package Tests\Unit
 * @since 1.0.0
 */
final class StateUnitTest
    extends TestCase
{
    /**
     * Verifica que el modelo pueda instanciarse.
     */
    public function test_can_create_model_instance(): void
    {
        $model = new State();

        $this->assertInstanceOf(
            State::class,
            $model
        );
    }

    /**
     * Verifica que la clase del modelo exista.
     */
    public function test_model_class_exists(): void
    {
        $this->assertTrue(
            class_exists(State::class)
        );
    }
}
