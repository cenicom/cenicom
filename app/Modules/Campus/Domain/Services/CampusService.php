<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Services;

use App\Core\Services\BaseService;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Contracts\CampusServiceInterface;

final class CampusService extends BaseService
    implements CampusServiceInterface
{
    public function __construct(
        CampusRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
