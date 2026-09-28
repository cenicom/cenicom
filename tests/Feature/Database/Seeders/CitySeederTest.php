<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

//use App\Models\City;
//use App\Models\Country;
//use App\Models\State;
use App\Modules\City\Models\City;
use App\Modules\Country\Models\Country;
use App\Modules\State\Models\State;
use Database\Seeders\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CitySeederTest extends TestCase
{
    use RefreshDatabase;

    private function createStates(): array
    {
        $colombia = Country::query()->create([
            'name' => 'Colombia',
            'iso2' => 'CO',
            'iso3' => 'COL',
        ]);

        $ecuador = Country::query()->create([
            'name' => 'Ecuador',
            'iso2' => 'EC',
            'iso3' => 'ECU',
        ]);

        $huila = State::query()->create([
            'country_id' => $colombia->id,
            'name' => 'Huila',
        ]);

        $sucumbios = State::query()->create([
            'country_id' => $ecuador->id,
            'name' => 'Sucumbíos',
        ]);

        return [$huila, $sucumbios];
    }

    public function test_city_seeder_creates_neiva_under_huila(): void
    {
        [$huila] = $this->createStates();

        $this->seed(CitySeeder::class);

        $this->assertDatabaseHas('cities', [
            'state_id' => $huila->id,
            'name' => 'Neiva',
        ]);
    }

    public function test_city_seeder_creates_lago_agrio_under_sucumbios(): void
    {
        [, $sucumbios] = $this->createStates();

        $this->seed(CitySeeder::class);

        $this->assertDatabaseHas('cities', [
            'state_id' => $sucumbios->id,
            'name' => 'Lago Agrio',
        ]);
    }

    public function test_city_seeder_is_idempotent(): void
    {
        $this->createStates();

        $this->seed(CitySeeder::class);

        $firstCount = City::query()->count();

        $this->seed(CitySeeder::class);

        $secondCount = City::query()->count();

        $this->assertSame($firstCount, $secondCount);
        $this->assertSame(2, $secondCount);
    }

    public function test_city_seeder_keeps_state_context(): void
    {
        [$huila, $sucumbios] = $this->createStates();

        $this->seed(CitySeeder::class);

        $neiva = City::query()
            ->where('name', 'Neiva')
            ->firstOrFail();

        $lagoAgrio = City::query()
            ->where('name', 'Lago Agrio')
            ->firstOrFail();

        $this->assertSame($huila->id, $neiva->state_id);
        $this->assertSame($sucumbios->id, $lagoAgrio->state_id);
    }
}
