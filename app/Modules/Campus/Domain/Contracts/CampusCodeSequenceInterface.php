<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;

interface CampusCodeSequenceInterface
{
    public function next(string $institutionId): int;
}
