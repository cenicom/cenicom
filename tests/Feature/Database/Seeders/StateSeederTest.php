<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

//use App\Models\Country;
//use App\Models\State;
use App\Modules\Country\Models\Country;
use App\Modules\State\Models\State;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StateSeederTest extends TestCase
{
    use RefreshDatabase;

    private function createCountries(): array
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

        return [$colombia, $ecuador];
    }

    public function test_state_seeder_creates_huila_for_colombia(): void
    {
        [$colombia] = $this->createCountries();

        $this->seed(StateSeeder::class);

        $this->assertDatabaseHas('states', [
            'country_id' => $colombia->id,
            'name' => 'Huila',
        ]);
    }

    public function test_state_seeder_creates_sucumbios_for_ecuador(): void
    {
        [, $ecuador] = $this->createCountries();

        $this->seed(StateSeeder::class);

        $this->assertDatabaseHas('states', [
            'country_id' => $ecuador->id,
            'name' => 'Sucumbíos',
        ]);
    }

    public function test_state_seeder_is_idempotent(): void
    {
        $this->createCountries();

        $this->seed(StateSeeder::class);

        $firstCount = State::query()->count();

        $this->seed(StateSeeder::class);

        $secondCount = State::query()->count();

        $this->assertSame($firstCount, $secondCount);
        $this->assertSame(2, $secondCount);
    }

    public function test_state_seeder_keeps_country_context(): void
    {
        [$colombia, $ecuador] = $this->createCountries();

        $this->seed(StateSeeder::class);

        $huila = State::query()
            ->where('name', 'Huila')
            ->firstOrFail();

        $sucumbios = State::query()
            ->where('name', 'Sucumbíos')
            ->firstOrFail();

        $this->assertSame($colombia->id, $huila->country_id);
        $this->assertSame($ecuador->id, $sucumbios->country_id);
    }
}
