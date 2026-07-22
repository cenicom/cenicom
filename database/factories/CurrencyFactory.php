<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo Currency.
 *
 * @extends Factory<Currency>
 */
final class CurrencyFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Currency>
     */
    protected $model = Currency::class;

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
