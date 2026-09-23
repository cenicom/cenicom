<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo CnGeneratorProbe.
 *
 * @package Database\Seeders
 */
final class CnGeneratorProbeSeeder
    extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        CnGeneratorProbe::factory()
            ->count(10)
            ->create();
    }
}
