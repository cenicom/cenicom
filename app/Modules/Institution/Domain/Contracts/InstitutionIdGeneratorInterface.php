<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Contracts;

interface InstitutionIdGeneratorInterface
{
    public function generate(): string;
}
