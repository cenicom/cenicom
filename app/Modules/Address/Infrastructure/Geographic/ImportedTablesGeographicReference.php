<?php

declare(strict_types=1);

namespace App\Modules\Address\Infrastructure\Geographic;

use App\Modules\Address\Domain\Contracts\GeographicReferenceInterface;
use Illuminate\Database\ConnectionInterface;

final class ImportedTablesGeographicReference implements GeographicReferenceInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
    }

    public function isCoherent(
        int $countryId,
        int $stateId,
        int $cityId,
    ): bool {
        return $this->connection
            ->table('states as s')
            ->join('cities as c', 'c.state_id', '=', 's.id')
            ->where('s.id', $stateId)
            ->where('s.country_id', $countryId)
            ->where('c.id', $cityId)
            ->where('c.country_id', $countryId)
            ->exists();
    }
}
