<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class InstitutionOfficialRegistration
{
    public function __construct(
        public string $country,
        public string $authority,
        public string $value,
    ) {
        if (trim($this->country) === '') {
            throw new InvalidArgumentException(
                'Registration country cannot be empty.'
            );
        }

        if (trim($this->authority) === '') {
            throw new InvalidArgumentException(
                'Registration authority cannot be empty.'
            );
        }

        if (trim($this->value) === '') {
            throw new InvalidArgumentException(
                'Registration value cannot be empty.'
            );
        }
    }
}
