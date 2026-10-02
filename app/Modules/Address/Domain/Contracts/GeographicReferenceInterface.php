<?php

declare(strict_types=1);

namespace App\Modules\Address\Domain\Contracts;

interface GeographicReferenceInterface
{
    public function isCoherent(
        int $countryId,
        int $stateId,
        int $cityId,
    ): bool;
}
