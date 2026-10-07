<?php

declare(strict_types=1);

namespace App\Modules\Address\Infrastructure\Geographic;

use App\Modules\Address\Domain\Contracts\GeographicQueryInterface;
use App\Modules\Address\Domain\DTOs\GeographicOption;
use Illuminate\Database\ConnectionInterface;

final class ImportedTablesGeographicQuery implements GeographicQueryInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
    }

    public function countries(): array
    {
        $rows = $this->connection
            ->table('countries')
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $options = [];

        foreach ($rows as $row) {
            $options[] = new GeographicOption(
                id: (int) $row->id,
                name: (string) $row->name,
            );
        }

        return $options;
    }

    public function statesByCountry(int $countryId): array
    {
        $rows = $this->connection
            ->table('states')
            ->select(['id', 'name'])
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get();

        $options = [];

        foreach ($rows as $row) {
            $options[] = new GeographicOption(
                id: (int) $row->id,
                name: (string) $row->name,
            );
        }

        return $options;
    }

    public function citiesByState(int $stateId): array
    {
        $rows = $this->connection
            ->table('cities')
            ->select(['id', 'name'])
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();

        $options = [];

        foreach ($rows as $row) {
            $options[] = new GeographicOption(
                id: (int) $row->id,
                name: (string) $row->name,
            );
        }

        return $options;
    }
}
