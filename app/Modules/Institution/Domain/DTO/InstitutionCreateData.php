<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\DTO;

use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;

final readonly class InstitutionCreateData
{
    public function __construct(
        public string $name,
        public ?InstitutionOfficialRegistration $officialRegistration = null,
    ) {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException(
                'Institution name cannot be empty.'
            );
        }
    }
}
