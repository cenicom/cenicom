<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus;

interface CampusRepositoryInterface extends RepositoryInterface
{
    public function save(Campus $campus): Campus;
}
