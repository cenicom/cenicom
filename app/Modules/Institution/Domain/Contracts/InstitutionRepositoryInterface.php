<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\Entity\Institution;

interface InstitutionRepositoryInterface
{
    public function save(Institution $institution): Institution;
}
