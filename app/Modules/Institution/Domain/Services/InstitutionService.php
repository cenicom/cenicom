<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Services;

use App\Core\Services\BaseService;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;


final class InstitutionService extends BaseService
    implements InstitutionServiceInterface
{
    public function __construct(
        InstitutionRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
