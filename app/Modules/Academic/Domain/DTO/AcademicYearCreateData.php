<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\DTO;

use InvalidArgumentException;

final readonly class AcademicYearCreateData
{
    public string $institutionId;
    public string $publicationVersionId;
    public string $cycleCode;
    public string $calendarCode;

    public function __construct(
        string $institutionId,
        string $publicationVersionId,
        string $cycleCode,
        string $calendarCode,
    ) {
        $institutionId = trim($institutionId);
        $publicationVersionId = trim($publicationVersionId);
        $cycleCode = trim($cycleCode);
        $calendarCode = trim($calendarCode);

        if ($institutionId === '') {
            throw new InvalidArgumentException('La institución es obligatoria.');
        }

        if ($publicationVersionId === '') {
            throw new InvalidArgumentException('La versión oficial es obligatoria.');
        }

        if ($cycleCode === '' || mb_strlen($cycleCode) > 20) {
            throw new InvalidArgumentException(
                'El código del ciclo debe contener entre 1 y 20 caracteres.',
            );
        }

        if (! in_array($calendarCode, ['A', 'B'], true)) {
            throw new InvalidArgumentException('El calendario debe ser A o B.');
        }

        $this->institutionId = $institutionId;
        $this->publicationVersionId = $publicationVersionId;
        $this->cycleCode = $cycleCode;
        $this->calendarCode = $calendarCode;
    }
}
