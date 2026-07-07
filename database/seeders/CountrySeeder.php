<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'CO',
                'iso3' => 'COL',
                'name' => 'Colombia',
                'nationality' => 'Colombiano(a)',
                'phone_code' => '+57',
                'currency_code' => 'COP',
                'language' => 'es',
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'EC',
                'iso3' => 'ECU',
                'name' => 'Ecuador',
                'nationality' => 'Ecuatoriano(a)',
                'phone_code' => '+593',
                'currency_code' => 'USD',
                'language' => 'es',
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'PE',
                'iso3' => 'PER',
                'name' => 'Perú',
                'nationality' => 'Peruano(a)',
                'phone_code' => '+51',
                'currency_code' => 'PEN',
                'language' => 'es',
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'CL',
                'iso3' => 'CHL',
                'name' => 'Chile',
                'nationality' => 'Chileno(a)',
                'phone_code' => '+56',
                'currency_code' => 'CLP',
                'language' => 'es',
                'active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
