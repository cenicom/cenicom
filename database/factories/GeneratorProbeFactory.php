<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\GeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo GeneratorProbe.
 *
 * @extends Factory<GeneratorProbe>
 */
final class GeneratorProbeFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<GeneratorProbe>
     */
    protected $model = GeneratorProbe::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string,mixed>
     */
    public function definition(): array
    {
        return [

        ];
    }
}
