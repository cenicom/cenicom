<?php

namespace Database\Seeders;

//use App\Models\Admin\Eps;


use App\Models\User;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountryCurrencySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\StateSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CurrencySeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            CountryCurrencySeeder::class,
        ]);

        User::factory(30)->create();


    }
}
