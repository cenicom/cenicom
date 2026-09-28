<?php

declare(strict_types=1);

namespace Tests\Feature\Database\Seeders;

//use App\Models\Country;
//use App\Models\Currency;
use App\Modules\Country\Models\Country;
use App\Modules\Currency\Models\Currency;
use Database\Seeders\CountryCurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CountryCurrencySeederTest extends TestCase
{
    use RefreshDatabase;

    private function createCatalogDependencies(): array
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

        $cop = Currency::query()->create([
            'name' => 'Peso colombiano',
            'code' => 'COP',
            'precision' => 2,
            'symbol' => '$',
            'decimal_mark' => ',',
            'thousands_separator' => '.',
        ]);

        $usd = Currency::query()->create([
            'name' => 'Dólar estadounidense',
            'code' => 'USD',
            'precision' => 2,
            'symbol' => '$',
            'decimal_mark' => '.',
            'thousands_separator' => ',',
        ]);

        return [$colombia, $ecuador, $cop, $usd];
    }

    public function test_country_currency_seeder_creates_colombia_cop(): void
    {
        [$colombia, , $cop] = $this->createCatalogDependencies();

        $this->seed(CountryCurrencySeeder::class);

        $this->assertDatabaseHas('country_currency', [
            'country_id' => $colombia->id,
            'currency_id' => $cop->id,
            'is_primary' => true,
        ]);
    }

    public function test_country_currency_seeder_creates_ecuador_usd(): void
    {
        [, $ecuador, , $usd] = $this->createCatalogDependencies();

        $this->seed(CountryCurrencySeeder::class);

        $this->assertDatabaseHas('country_currency', [
            'country_id' => $ecuador->id,
            'currency_id' => $usd->id,
            'is_primary' => true,
        ]);
    }

    public function test_country_currency_seeder_is_idempotent(): void
    {
        $this->createCatalogDependencies();

        $this->seed(CountryCurrencySeeder::class);

        $firstCount = DB::table('country_currency')->count();

        $this->seed(CountryCurrencySeeder::class);

        $secondCount = DB::table('country_currency')->count();

        $this->assertSame($firstCount, $secondCount);
        $this->assertSame(2, $secondCount);
    }

    public function test_country_currency_seeder_reconciles_primary_flag(): void
    {
        [$colombia, , $cop] = $this->createCatalogDependencies();

        DB::table('country_currency')->insert([
            'country_id' => $colombia->id,
            'currency_id' => $cop->id,
            'is_primary' => false,
        ]);

        $this->seed(CountryCurrencySeeder::class);

        $relation = DB::table('country_currency')
            ->where('country_id', $colombia->id)
            ->where('currency_id', $cop->id)
            ->first();

        $this->assertNotNull($relation);
        $this->assertTrue((bool) $relation->is_primary);

        $this->assertSame(
            1,
            DB::table('country_currency')
                ->where('country_id', $colombia->id)
                ->where('currency_id', $cop->id)
                ->count()
        );
    }
}
