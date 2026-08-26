<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\GeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo GeneratorProbe.
 *
 * @package Database\Seeders
 */
final class GeneratorProbeSeeder
    extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        GeneratorProbe::factory()
            ->count(10)
            ->create();
    }
}
