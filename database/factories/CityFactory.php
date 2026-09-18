<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\City\Models\City;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo City.
 *
 * @extends Factory<City>
 */
final class CityFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<City>
     */
    protected $model = City::class;

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
