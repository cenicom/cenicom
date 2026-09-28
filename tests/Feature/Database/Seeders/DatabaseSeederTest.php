<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

use App\Modules\City\Models\City;
use App\Modules\Country\Models\Country;
use App\Modules\Currency\Models\Currency;
use App\Modules\State\Models\State;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_loads_the_complete_master_catalog(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Currency::query()->count());
        $this->assertSame(2, Country::query()->count());
        $this->assertSame(2, State::query()->count());
        $this->assertSame(2, City::query()->count());

        $colombia = Country::query()
            ->where('iso2', 'CO')
            ->firstOrFail();

        $ecuador = Country::query()
            ->where('iso2', 'EC')
            ->firstOrFail();

        $cop = Currency::query()
            ->where('code', 'COP')
            ->firstOrFail();

        $usd = Currency::query()
            ->where('code', 'USD')
            ->firstOrFail();

        $huila = State::query()
            ->where('country_id', $colombia->id)
            ->where('name', 'Huila')
            ->firstOrFail();

        $sucumbios = State::query()
            ->where('country_id', $ecuador->id)
            ->where('name', 'Sucumbíos')
            ->firstOrFail();

        $this->assertDatabaseHas('cities', [
            'state_id' => $huila->id,
            'name' => 'Neiva',
        ]);

        $this->assertDatabaseHas('cities', [
            'state_id' => $sucumbios->id,
            'name' => 'Lago Agrio',
        ]);

        $this->assertDatabaseHas('country_currency', [
            'country_id' => $colombia->id,
            'currency_id' => $cop->id,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('country_currency', [
            'country_id' => $ecuador->id,
            'currency_id' => $usd->id,
            'is_primary' => true,
        ]);
    }

    public function test_database_seeder_is_idempotent_for_the_master_catalog(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Currency::query()->count());
        $this->assertSame(2, Country::query()->count());
        $this->assertSame(2, State::query()->count());
        $this->assertSame(2, City::query()->count());

        $this->assertSame(
            2,
            DB::table('country_currency')->count()
        );

        $this->assertDatabaseHas('country_currency', [
            'country_id' => Country::query()
                ->where('iso2', 'CO')
                ->value('id'),
            'currency_id' => Currency::query()
                ->where('code', 'COP')
                ->value('id'),
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('country_currency', [
            'country_id' => Country::query()
                ->where('iso2', 'EC')
                ->value('id'),
            'currency_id' => Currency::query()
                ->where('code', 'USD')
                ->value('id'),
            'is_primary' => true,
        ]);
    }
}
