<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo CnGeneratorProbe.
 *
 * @extends Factory<CnGeneratorProbe>
 */
final class CnGeneratorProbeFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<CnGeneratorProbe>
     */
    protected $model = CnGeneratorProbe::class;

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
