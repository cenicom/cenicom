<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencySeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'COP',
                'symbol' => '$',
                'name' => 'Peso Colombiano',
                'is_default' => true,
            ],
            [
                'code' => 'USD',
                'symbol' => 'US$',
                'name' => 'Dólar Estadounidense',
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
            ],
            [
                'code' => 'PEN',
                'symbol' => 'S/',
                'name' => 'Sol Peruano',
            ],
            [
                'code' => 'CLP',
                'symbol' => '$',
                'name' => 'Peso Chileno',
            ],
            [
                'code' => 'MXN',
                'symbol' => '$',
                'name' => 'Peso Mexicano',
            ],
        ];

        foreach ($currencies as $currency) {

            Currency::updateOrCreate(

                ['code' => $currency['code']],

                [
                    'uuid' => (string) Str::uuid(),
                    'symbol' => $currency['symbol'],
                    'name' => $currency['name'],
                    'decimal_places' => 2,
                    'decimal_separator' => '.',
                    'thousands_separator' => ',',
                    'symbol_position' => 'before',
                    'is_default' => $currency['is_default'] ?? false,
                    'status' => true,
                ]
            );
        }
    }
}
