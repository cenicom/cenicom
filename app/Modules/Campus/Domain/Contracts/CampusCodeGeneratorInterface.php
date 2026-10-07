<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;

interface CampusCodeGeneratorInterface
{
    public function generate(
        string $institutionShortCode,
        string $campusShortCode,
        int $sequence
    ): string;
}
