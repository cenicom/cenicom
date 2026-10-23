<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile;
use App\Modules\Academic\Domain\ValueObjects\OfficialCalendarWindow;
use DomainException;

final class OfficialCalendarCompatibilityValidator
{
    public function assertCompatible(
        InstitutionAcademicProfile $institution,
        OfficialCalendarWindow $publication,
        string $requestedCalendarCode,
    ): void {
        if (
            $institution->registeredCountryCode !== null
            && $institution->registeredCountryCode !== $institution->countryCode
        ) {
            throw new DomainException(
                'El país del perfil académico no coincide con el país del registro oficial de la institución.',
            );
        }

        $dimensions = [
            'país' => [$institution->countryCode, $publication->countryCode],
            'autoridad reguladora' => [$institution->authorityCode, $publication->authorityCode],
            'jurisdicción' => [$institution->jurisdictionCode, $publication->jurisdictionCode],
            'régimen educativo' => [$institution->regimeCode, $publication->regimeCode],
            'región' => [$institution->regionCode, $publication->regionCode],
        ];

        foreach ($dimensions as $label => [$expected, $actual]) {
            if ($expected !== $actual) {
                throw new DomainException(sprintf(
                    'La publicación oficial no es compatible con la institución: no coincide %s.',
                    $label,
                ));
            }
        }

        if ($publication->calendarCode === null) {
            throw new DomainException(
                'La publicación oficial no define calendario A/B; la compatibilidad no puede demostrarse.',
            );
        }

        if ($institution->calendarCode !== $publication->calendarCode) {
            throw new DomainException(
                'El calendario A/B de la publicación oficial no coincide con el calendario institucional.',
            );
        }

        if ($requestedCalendarCode !== $institution->calendarCode) {
            throw new DomainException(
                'El calendario A/B solicitado no coincide con la selección canónica de la institución.',
            );
        }
    }
}
