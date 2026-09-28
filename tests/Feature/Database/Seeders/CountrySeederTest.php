<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

//use App\Models\Country;
use App\Modules\Country\Models\Country;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CountrySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_country_seeder_creates_colombia(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseHas('countries', [
            'iso2' => 'CO',
            'name' => 'Colombia',
            'iso3' => 'COL',
        ]);
    }

    public function test_country_seeder_creates_ecuador(): void
    {
        $this->seed(CountrySeeder::class);

        $this->assertDatabaseHas('countries', [
            'iso2' => 'EC',
            'name' => 'Ecuador',
            'iso3' => 'ECU',
        ]);
    }

    public function test_country_seeder_is_idempotent(): void
    {
        $this->seed(CountrySeeder::class);

        $firstCount = Country::query()->count();

        $this->seed(CountrySeeder::class);

        $secondCount = Country::query()->count();

        $this->assertSame($firstCount, $secondCount);
        $this->assertSame(2, $secondCount);
    }

    public function test_country_seeder_reconciles_master_attributes(): void
    {
        Country::query()->create([
            'name' => 'Nombre incorrecto',
            'iso2' => 'CO',
            'iso3' => 'XXX',
        ]);

        $this->seed(CountrySeeder::class);

        $country = Country::query()
            ->where('iso2', 'CO')
            ->firstOrFail();

        $this->assertSame('Colombia', $country->name);
        $this->assertSame('COL', $country->iso3);

        $this->assertSame(
            1,
            Country::query()->where('iso2', 'CO')->count()
        );
    }
}
