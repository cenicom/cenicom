<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Modules\State\Models\State;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Factory del modelo State.
 *
 * @extends Factory<State>
 */
final class StateFactory
    extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<State>
     */
    protected $model = State::class;

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
