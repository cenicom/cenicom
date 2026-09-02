<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Services;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Entity\Institution;

final readonly class InstitutionCreator implements InstitutionCreatorInterface
{
    public function __construct(
        private InstitutionIdGeneratorInterface $idGenerator,
        private InstitutionCodeGeneratorInterface $codeGenerator,
    ) {
    }

    public function create(InstitutionCreateData $data): Institution
    {
        return new Institution(
            id: $this->idGenerator->generate(),
            name: $data->name,
            code: $this->codeGenerator->generate(),
            officialRegistration: $data->officialRegistration,
        );
    }
}
