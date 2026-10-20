<?php

declare(strict_types=1);

namespace App\Modules\Campus\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus as DomainCampus;
use App\Modules\Campus\Models\Campus as CampusModel;
use InvalidArgumentException;

final class CampusRepository extends BaseRepository implements CampusRepositoryInterface
{
    private const ALLOWED_STATUS_TRANSITIONS = [
        'draft' => ['active'],
        'active' => ['inactive'],
        'inactive' => ['active'],
    ];

    public function __construct(CampusModel $model)
    {
        parent::__construct($model);
    }

    public function save(DomainCampus $campus): DomainCampus
    {
        /** @var CampusModel $model */
        $model = $this->query()->firstOrNew([
            'id' => $campus->id(),
        ]);

        $attributes = $this->toPersistence($campus);

        if ($model->exists) {
            unset($attributes['status']);
        }

        $model->fill($attributes);
        $model->save();

        return $this->toDomain($model->refresh());
    }

    public function lockForUpdate(string $id): ?DomainCampus
    {
        /** @var CampusModel|null $model */
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
    ): ?DomainCampus {
        $this->assertAllowedTransition($expectedStatus, $targetStatus);

        $updated = $this->query()
            ->whereKey($id)
            ->where('status', $expectedStatus)
            ->update(['status' => $targetStatus]);

        if ($updated !== 1) {
            return null;
        }

        /** @var CampusModel|null $model */
        $model = $this->query()->find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    private function assertAllowedTransition(
        string $expectedStatus,
        string $targetStatus,
    ): void {
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
            throw new InvalidArgumentException(
                sprintf(
                    'Transición de estado de sede no permitida: %s → %s.',
                    $expectedStatus,
                    $targetStatus,
                ),
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    private function toPersistence(DomainCampus $campus): array
    {
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

    private function toDomain(CampusModel $model): DomainCampus
    {
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

    public function update(
        int|string $id,
        array $attributes,
    ): bool {
        if (array_key_exists('status', $attributes)) {
            throw new InvalidArgumentException(
                'El estado solo puede modificarse mediante una transición explícita.',
            );
        }

        return parent::update($id, $attributes);
    }
}
