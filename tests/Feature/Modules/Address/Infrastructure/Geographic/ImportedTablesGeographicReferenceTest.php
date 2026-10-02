<?php

declare(strict_types=1);

use App\Modules\Address\Infrastructure\Geographic\ImportedTablesGeographicReference;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    config([
        'database.connections.mysql.database' => 'cenicom',
    ]);

    DB::purge('mysql');

    $this->reference = new ImportedTablesGeographicReference(
        DB::connection('mysql'),
    );
});

it('returns true for a coherent geographic combination', function (): void {
    expect(
        $this->reference->isCoherent(
            countryId: 1,
            stateId: 1,
            cityId: 1,
        ),
    )->toBeTrue();
});

it('returns false when the city belongs to another state', function (): void {
    expect(
        $this->reference->isCoherent(
            countryId: 1,
            stateId: 1,
            cityId: 7,
        ),
    )->toBeFalse();
});
