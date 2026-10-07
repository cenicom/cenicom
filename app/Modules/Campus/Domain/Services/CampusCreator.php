<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Services;

use App\Modules\Campus\Domain\Contracts\CampusCodeGeneratorInterface;
use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusIdGeneratorInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;

final readonly class CampusCreator implements CampusCreatorInterface
{
    public function __construct(
        private CampusIdGeneratorInterface $idGenerator,
        private CampusCodeSequenceInterface $codeSequence,
        private CampusCodeGeneratorInterface $codeGenerator,
    ) {
    }

    public function create(CampusCreateData $data): Campus
    {
        $id = $this->idGenerator->generate();

        $sequence = $this->codeSequence->next(
            $data->institutionId
        );

        $code = $this->codeGenerator->generate(
            $data->institutionShortCode,
            $data->shortCode,
            $sequence,
        );

        return new Campus(
            id: $id,
            institutionId: $data->institutionId,
            code: $code,
            shortCode: $data->shortCode,
            name: $data->name,
            addressId: $data->addressId,
            ministryCode: $data->ministryCode,
            status: 'draft',
        );
    }
}
