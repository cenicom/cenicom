<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use Illuminate\Support\Facades\DB;

final readonly class CampusCreationUseCase
{
    private const TRANSACTION_ATTEMPTS = 3;

    public function __construct(
        private CampusCreatorInterface $creator,
        private CampusRepositoryInterface $repository,
    ) {}

    public function execute(CampusCreateData $data): Campus
    {
        return DB::transaction(
            function () use ($data): Campus {
                $campus = $this->creator->create($data);

                return $this->repository->save($campus);
            },
            self::TRANSACTION_ATTEMPTS,
        );
    }
}
