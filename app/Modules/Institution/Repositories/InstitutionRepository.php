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
    public function __construct(InstitutionModel $model)
    {
        parent::__construct($model);
    }

    private const ALLOWED_STATUS_TRANSITIONS = [
        'draft' => ['active'],
        'active' => ['inactive'],
        'inactive' => ['active'],
    ];

    public function save(DomainInstitution $institution): DomainInstitution
    {
        /** @var InstitutionModel $model */
        $model = $this->query()->firstOrNew([
            'id' => $institution->id(),
        ]);

        $attributes = $this->toPersistence($institution);

        if ($model->exists) {
            unset($attributes['status']);
        }

        $model->fill($attributes);
        $model->save();

        return $this->toDomain($model->refresh());
    }

    public function lockForUpdate(string $id): ?DomainInstitution
    {
        /** @var InstitutionModel|null $model */
        $model = $this->query()
            ->whereKey($id)
            ->lockForUpdate()
            ->first();

        return $model === null ? null : $this->toDomain($model);
    }

    public function transitionStatus(
        string $id,
        string $expectedStatus,
        string $targetStatus,
    ): ?DomainInstitution {
        if (
            ! array_key_exists(
                $expectedStatus,
                self::ALLOWED_STATUS_TRANSITIONS,
            )
            || ! in_array(
                $targetStatus,
                self::ALLOWED_STATUS_TRANSITIONS[$expectedStatus],
                true,
            )
        ) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Transición de estado de institución no permitida: %s → %s.',
                    $expectedStatus,
                    $targetStatus,
                ),
            );
        }

        $updated = $this->query()
            ->whereKey($id)
            ->where('status', $expectedStatus)
            ->update(['status' => $targetStatus]);

        if ($updated !== 1) {
            return null;
        }

        /** @var InstitutionModel|null $model */
        $model = $this->query()->find($id);

        return $model === null ? null : $this->toDomain($model);
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
            'short_code' => $institution->shortCode(),
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
            shortCode: $model->short_code,
            officialRegistration: $registration,
            status: $model->status,
        );
    }

    public function update(
        int|string $id,
        array $attributes,
    ): bool {
        if (array_key_exists('status', $attributes)) {
            throw new \InvalidArgumentException(
                'El estado solo puede modificarse mediante una transición explícita.',
            );
        }

        return parent::update($id, $attributes);
    }
}
