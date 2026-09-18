<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\Country\Models\Country;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo Country.
 *
 * @extends Factory<Country>
 */
final class CountryFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Country>
     */
    protected $model = Country::class;

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
