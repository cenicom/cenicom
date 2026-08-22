<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Entity\Institution;

interface InstitutionCreatorInterface
{
    public function create(
        InstitutionCreateData $data
    ): Institution;
}
