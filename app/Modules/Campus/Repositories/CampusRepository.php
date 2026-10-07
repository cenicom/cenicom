<?php

declare(strict_types=1);

namespace App\Modules\Campus\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Models\Campus;

final class CampusRepository extends BaseRepository implements CampusRepositoryInterface
{
    public function __construct(Campus $model)
    {
        parent::__construct($model);
    }
}
