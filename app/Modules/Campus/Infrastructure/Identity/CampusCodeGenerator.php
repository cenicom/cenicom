<?php

declare(strict_types=1);

namespace App\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Domain\Contracts\CampusCodeGeneratorInterface;
use InvalidArgumentException;

final readonly class CampusCodeGenerator implements CampusCodeGeneratorInterface
{
    public function generate(
        string $institutionShortCode,
        string $campusShortCode,
        int $sequence
    ): string {
        if (! preg_match('/^[A-Z]{6,15}$/', $institutionShortCode)) {
            throw new InvalidArgumentException(
                'Institution short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if (! preg_match('/^[A-Z]{6,15}$/', $campusShortCode)) {
            throw new InvalidArgumentException(
                'Campus short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if ($sequence <= 0) {
            throw new InvalidArgumentException(
                'Campus code sequence must be greater than zero.'
            );
        }

        return sprintf(
            'CEN-%s-%s-%03d',
            $institutionShortCode,
            $campusShortCode,
            $sequence
        );
    }
}
