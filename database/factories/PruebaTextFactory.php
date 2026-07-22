<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\PruebaText;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo PruebaText.
 *
 * @extends Factory<PruebaText>
 */
final class PruebaTextFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<PruebaText>
     */
    protected $model = PruebaText::class;

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
