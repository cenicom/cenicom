<?php

declare(strict_types=1);

use App\Modules\Address\Domain\DTOs\GeographicOption;
use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicQuery;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    config([
        'database.connections.mysql.database' => 'cenicom',
    ]);

    DB::purge('mysql');

    $this->query = new ImportedTablesGeographicQuery(
        DB::connection('mysql'),
    );
});

it('returns countries as geographic options ordered by name', function (): void {
    $result = $this->query->countries();

    expect($result)
        ->not->toBeEmpty()
        ->each->toBeInstanceOf(GeographicOption::class);

    expect($result[0]->id)->toBe(1);
    expect($result[0]->name)->toBe('Afghanistan');

    $names = array_map(
        static fn(GeographicOption $option): string => $option->name,
        $result,
    );

    $expectedNames = DB::connection('mysql')
        ->table('countries')
        ->orderBy('name')
        ->pluck('name')
        ->map(static fn($name): string => (string) $name)
        ->all();

    $actualNames = array_map(
        static fn(GeographicOption $option): string => $option->name,
        $result,
    );

    expect($actualNames)->toBe($expectedNames);
});

it('returns only states belonging to the selected country', function (): void {
    $result = $this->query->statesByCountry(1);

    expect($result)
        ->not->toBeEmpty()
        ->each->toBeInstanceOf(GeographicOption::class);

    expect($result[0]->id)->toBe(1);
    expect($result[0]->name)->toBe('Badakhshan');

    foreach ($result as $option) {
        $belongsToCountry = DB::connection('mysql')
            ->table('states')
            ->where('id', $option->id)
            ->where('country_id', 1)
            ->exists();

        expect($belongsToCountry)->toBeTrue();
    }
});

it('returns only cities belonging to the selected state', function (): void {
    $result = $this->query->citiesByState(1);

    expect($result)
        ->not->toBeEmpty()
        ->each->toBeInstanceOf(GeographicOption::class);

    expect($result[0]->id)->toBe(1);
    expect($result[0]->name)->toBe('Ashkāsham');

    foreach ($result as $option) {
        $belongsToState = DB::connection('mysql')
            ->table('cities')
            ->where('id', $option->id)
            ->where('state_id', 1)
            ->exists();

        expect($belongsToState)->toBeTrue();
    }
});

it('returns an empty array when a state has no cities', function (): void {
    $result = $this->query->citiesByState(999999999);

    expect($result)->toBe([]);
});
