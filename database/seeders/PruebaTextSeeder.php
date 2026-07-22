<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PruebaText;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo PruebaText.
 *
 * @package Database\Seeders
 */
final class PruebaTextSeeder
    extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        PruebaText::factory()
            ->count(10)
            ->create();
    }
}
