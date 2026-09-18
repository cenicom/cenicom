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
        Country::factory()
            ->count(10)
            ->create();
    }
}
