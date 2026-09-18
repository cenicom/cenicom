<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Modules\City\Models\City;

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
        City::factory()
            ->count(10)
            ->create();
    }
}
