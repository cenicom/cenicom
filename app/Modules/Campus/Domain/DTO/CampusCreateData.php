<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\DTO;

use InvalidArgumentException;

final readonly class CampusCreateData
{
    public function __construct(
        public string $institutionId,
        public string $institutionShortCode,
        public string $shortCode,
        public string $name,
        public string $addressId,
        public ?string $ministryCode = null,
    ) {
        if (trim($this->institutionId) === '') {
            throw new InvalidArgumentException(
                'Campus institution id cannot be empty.'
            );
        }

        if (!preg_match('/^[A-Z]{6,15}$/', $this->institutionShortCode)) {
            throw new InvalidArgumentException(
                'Campus institution short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if (!preg_match('/^[A-Z]{6,15}$/', $this->shortCode)) {
            throw new InvalidArgumentException(
                'Campus short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if (trim($this->name) === '') {
            throw new InvalidArgumentException(
                'Campus name cannot be empty.'
            );
        }

        if (trim($this->addressId) === '') {
            throw new InvalidArgumentException(
                'Campus address id cannot be empty.'
            );
        }
    }
}
