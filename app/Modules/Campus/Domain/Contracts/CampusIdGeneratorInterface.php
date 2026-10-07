<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;

interface CampusIdGeneratorInterface
{
    public function generate(): string;
}
