<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class CampusCreationUseCase
{
    private const TRANSACTION_ATTEMPTS = 3;

    public function __construct(
        private CampusCreatorInterface $creator,
        private CampusRepositoryInterface $repository,
        private InstitutionRepositoryInterface $institutions,
    ) {
    }

    public function execute(CampusCreateData $data): Campus
    {
        return DB::transaction(
            function () use ($data): Campus {
                $institution = $this->institutions->lockForUpdate(
                    $data->institutionId,
                );

                if ($institution === null) {
                    throw new DomainException(
                        'No se puede crear una sede porque la institución no existe.',
                    );
                }

                if ($institution->status() !== 'active') {
                    throw new DomainException(
                        'Solo se pueden crear sedes para instituciones activas.',
                    );
                }

                $campus = $this->creator->create($data);

                return $this->repository->save($campus);
            },
            self::TRANSACTION_ATTEMPTS,
        );
    }
}
