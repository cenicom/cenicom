<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Modules\Currency\Models\Currency;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Seeder del módulo Currency.
 *
 * @package Database\Seeders
 */
final class CurrencySeeder
extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        Currency::query()->updateOrCreate(
            ['code' => 'COP'],
            [
                'name' => 'Peso colombiano',
                'precision' => 2,
                'symbol' => '$',
                'decimal_mark' => ',',
                'thousands_separator' => '.',
            ]
        );

        Currency::query()->updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'Dólar estadounidense',
                'precision' => 2,
                'symbol' => '$',
                'decimal_mark' => '.',
                'thousands_separator' => ',',
            ]
        );
    }
}
