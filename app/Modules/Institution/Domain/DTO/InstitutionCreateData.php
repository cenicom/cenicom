<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\DTO;

use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;

final readonly class InstitutionCreateData
{
    public function __construct(
        public string $name,
        public string $shortCode,
        public ?InstitutionOfficialRegistration $officialRegistration = null,
    ) {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException(
                'Institution name cannot be empty.'
            );
        }

        if (!preg_match('/^[A-Z]{6,15}$/', $this->shortCode)) {
            throw new InvalidArgumentException(
                'Institution short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }
    }
}
