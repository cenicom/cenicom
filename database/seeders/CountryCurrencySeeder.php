<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Country\Models\Country;
use App\Modules\Currency\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CountryCurrencySeeder extends Seeder
{
    public function run(): void
    {
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

        DB::table('country_currency')->updateOrInsert(
            [
                'country_id' => $colombia->id,
                'currency_id' => $cop->id,
            ],
            [
                'is_primary' => true,
            ]
        );

        DB::table('country_currency')->updateOrInsert(
            [
                'country_id' => $ecuador->id,
                'currency_id' => $usd->id,
            ],
            [
                'is_primary' => true,
            ]
        );
    }
}
