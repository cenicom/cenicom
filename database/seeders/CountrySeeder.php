<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Modules\Country\Models\Country;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo Country.
 *
 * @package Database\Seeders
 */
final class CountrySeeder
    extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        Country::query()->updateOrCreate(
            ['iso2' => 'CO'],
            [
                'name' => 'Colombia',
                'iso3' => 'COL',
            ]
        );

        Country::query()->updateOrCreate(
            ['iso2' => 'EC'],
            [
                'name' => 'Ecuador',
                'iso3' => 'ECU',
            ]
        );
    }
}
