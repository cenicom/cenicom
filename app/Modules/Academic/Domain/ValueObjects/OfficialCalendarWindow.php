<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class OfficialCalendarWindow
{
    public function __construct(
        public string $startDate,
        public string $endDate,
        public ?string $calendarCode,
        public string $countryCode,
        public string $authorityCode,
        public string $jurisdictionCode,
        public string $regimeCode,
        public string $regionCode,
    ) {
        if ($startDate === '' || $endDate === '' || $startDate > $endDate) {
            throw new InvalidArgumentException(
                'La versión oficial no contiene un intervalo de fechas válido.',
            );
        }

        if ($calendarCode !== null && ! in_array($calendarCode, ['A', 'B'], true)) {
            throw new InvalidArgumentException(
                'La publicación contiene un calendario incompatible con el contrato A/B.',
            );
        }

        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException('La publicación contiene un país regulador no válido.');
        }

        foreach ([
            'authorityCode' => $authorityCode,
            'jurisdictionCode' => $jurisdictionCode,
            'regimeCode' => $regimeCode,
            'regionCode' => $regionCode,
        ] as $field => $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException(sprintf('La publicación contiene %s vacío.', $field));
            }
        }
    }
}
