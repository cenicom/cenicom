<?php

declare(strict_types=1);

namespace App\Modules\Address\Domain\Contracts;

use App\Modules\Address\Domain\DTOs\GeographicOption;

interface GeographicQueryInterface
{
    /**
     * @return list<GeographicOption>
     */
    public function countries(): array;

    /**
     * @return list<GeographicOption>
     */
    public function statesByCountry(int $countryId): array;

    /**
     * @return list<GeographicOption>
     */
    public function citiesByState(int $stateId): array;
}
