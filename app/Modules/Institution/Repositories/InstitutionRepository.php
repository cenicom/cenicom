<?php

declare(strict_types=1);

namespace App\Modules\Institution\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution as DomainInstitution;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use App\Modules\Institution\Models\Institution as InstitutionModel;


final class InstitutionRepository extends BaseRepository implements InstitutionRepositoryInterface
{
    public function __construct(
        InstitutionModel $model
    ) {
        parent::__construct($model);
    }

    public function save(DomainInstitution $institution): DomainInstitution
    {
        /** @var InstitutionModel $model */
        $model = $this->query()->updateOrCreate(
            ['id' => $institution->id()],
            $this->toPersistence($institution),
        );

        return $this->toDomain($model);
    }

    /**
     * @return array<string, string|null>
     */
    private function toPersistence(
        DomainInstitution $institution
    ): array {
        $registration = $institution->officialRegistration();

        return [
            'id' => $institution->id(),
            'code' => $institution->code(),
            'name' => $institution->name(),
            'official_registration_country' => $registration?->country,
            'official_registration_authority' => $registration?->authority,
            'official_registration_value' => $registration?->value,
            'status' => $institution->status(),
        ];
    }

    private function toDomain(
        InstitutionModel $model
    ): DomainInstitution {
        $registration = null;

        if (
            $model->official_registration_country !== null
            && $model->official_registration_authority !== null
            && $model->official_registration_value !== null
        ) {
            $registration = new InstitutionOfficialRegistration(
                country: $model->official_registration_country,
                authority: $model->official_registration_authority,
                value: $model->official_registration_value,
            );
        }

        return new DomainInstitution(
            id: $model->getKey(),
            name: $model->name,
            code: $model->code,
            officialRegistration: $registration,
            status: $model->status,
        );
    }
}
