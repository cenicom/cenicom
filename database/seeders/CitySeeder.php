<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\City\Models\City;
use App\Modules\Country\Models\Country;
use App\Modules\State\Models\State;
use Illuminate\Database\Seeder;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo City.
 *
 * @package Database\Seeders
 */
final class CitySeeder
    extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $colombia = Country::query()
            ->where('iso2', 'CO')
            ->firstOrFail();

        $ecuador = Country::query()
            ->where('iso2', 'EC')
            ->firstOrFail();

        $huila = State::query()
            ->where('country_id', $colombia->id)
            ->where('name', 'Huila')
            ->firstOrFail();

        $sucumbios = State::query()
            ->where('country_id', $ecuador->id)
            ->where('name', 'Sucumbíos')
            ->firstOrFail();

        City::query()->updateOrCreate(
            [
                'state_id' => $huila->id,
                'name' => 'Neiva',
            ],
            [
                'name' => 'Neiva',
            ]
        );

        City::query()->updateOrCreate(
            [
                'state_id' => $sucumbios->id,
                'name' => 'Lago Agrio',
            ],
            [
                'name' => 'Lago Agrio',
            ]
        );
    }
}
