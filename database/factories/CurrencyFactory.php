<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * El modelo asociado con la factory.
     */
    protected $model = Currency::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            'uuid'                  => (string) Str::uuid(),
            'code'                  => strtoupper(fake()->unique()->lexify('???')),
            'symbol'                => fake()->randomElement(['$', '€', '£', '¥']),
            'name'                  => fake()->unique()->currencyCode(),
            'decimal_places'        => 2,
            'decimal_separator'     => '.',
            'thousands_separator'   => ',',
            'symbol_position'       => 'before',
            'is_default'            => false,
            'status'                => true,
            'created_by'            => null,
            'updated_by'            => null,
            'deleted_by'            => null,
        ];
    }

    /**
     * Estado: moneda por defecto.
     */
    public function default(): static
    {
        return $this->state(fn () => [
            'is_default' => true,
        ]);
    }

    /**
     * Estado: moneda inactiva.
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => false,
        ]);
    }
}
