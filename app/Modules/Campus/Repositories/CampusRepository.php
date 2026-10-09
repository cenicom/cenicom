<?php

declare(strict_types=1);

namespace App\Modules\Campus\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus as DomainCampus;
use App\Modules\Campus\Models\Campus as CampusModel;

final class CampusRepository extends BaseRepository implements CampusRepositoryInterface
{
    public function __construct(
        CampusModel $model
    ) {
        parent::__construct($model);
    }

    public function save(DomainCampus $campus): DomainCampus
    {
        /** @var CampusModel $model */
        $model = $this->query()->updateOrCreate(
            ['id' => $campus->id()],
            $this->toPersistence($campus),
        );

        return $this->toDomain($model);
    }

    /**
     * @return array<string, string|null>
     */
    private function toPersistence(
        DomainCampus $campus
    ): array {
        return [
            'id' => $campus->id(),
            'institution_id' => $campus->institutionId(),
            'code' => $campus->code(),
            'short_code' => $campus->shortCode(),
            'name' => $campus->name(),
            'ministry_code' => $campus->ministryCode(),
            'address_id' => $campus->addressId(),
            'status' => $campus->status(),
        ];
    }

    private function toDomain(
        CampusModel $model
    ): DomainCampus {
        return new DomainCampus(
            id: $model->getKey(),
            institutionId: $model->institution_id,
            code: $model->code,
            shortCode: $model->short_code,
            name: $model->name,
            addressId: $model->address_id,
            ministryCode: $model->ministry_code,
            status: $model->status,
        );
    }
}
