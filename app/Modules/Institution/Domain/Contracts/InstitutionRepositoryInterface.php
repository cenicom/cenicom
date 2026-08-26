<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Contracts;

use App\Modules\Institution\Domain\Entity\Institution;
use App\Core\Contracts\RepositoryInterface;

interface InstitutionRepositoryInterface extends RepositoryInterface
{
    public function save(Institution $institution): Institution;
}
