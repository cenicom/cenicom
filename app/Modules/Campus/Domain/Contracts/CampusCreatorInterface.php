<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;


use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;


interface CampusCreatorInterface
{
    public function create(
        CampusCreateData $data
    ): Campus;
}
