<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class InstitutionAcademicProfile
{
    public function __construct(
        public string $countryCode,
        public string $authorityCode,
        public string $jurisdictionCode,
        public string $regimeCode,
        public string $regionCode,
        public string $calendarCode,
        public ?string $registeredCountryCode = null,
    ) {
        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException('El país del perfil académico debe ser ISO alpha-2 en mayúsculas.');
        }

        foreach ([
            'authorityCode' => $authorityCode,
            'jurisdictionCode' => $jurisdictionCode,
            'regimeCode' => $regimeCode,
            'regionCode' => $regionCode,
        ] as $field => $value) {
            if (trim($value) === '') {
                throw new InvalidArgumentException(sprintf('El campo %s del perfil académico es obligatorio.', $field));
            }
        }

        if (! in_array($calendarCode, ['A', 'B'], true)) {
            throw new InvalidArgumentException('El calendario institucional debe ser A o B.');
        }

        if ($registeredCountryCode !== null && ! preg_match('/^[A-Z]{2}$/', $registeredCountryCode)) {
            throw new InvalidArgumentException('El país del registro institucional debe ser ISO alpha-2 en mayúsculas.');
        }
    }
}
