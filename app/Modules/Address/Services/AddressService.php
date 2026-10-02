<?php

declare(strict_types=1);

namespace App\Modules\Address\Services;

use App\Core\Services\BaseService;
use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Address\Domain\Contracts\GeographicReferenceInterface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class AddressService extends BaseService implements AddressServiceInterface
{
    public function __construct(
        AddressRepositoryInterface $repository,
        private readonly GeographicReferenceInterface $geographicReference,
    ) {
        parent::__construct($repository);
    }

    public function create(array $attributes): Model
    {
        $countryId = $this->normalizeGeographicId($attributes['country_id'] ?? null);
        $stateId = $this->normalizeGeographicId($attributes['state_id'] ?? null);
        $cityId = $this->normalizeGeographicId($attributes['city_id'] ?? null);

        $this->ensureGeographicCoherence(
            $countryId,
            $stateId,
            $cityId,
        );

        return $this->repository->create($attributes);
    }

    public function update(
        int|string $id,
        array $attributes
    ): bool {
        $current = $this->repository->findOrFail($id);

        $countryId = $this->normalizeGeographicId(
            $attributes['country_id'] ?? $current->country_id
        );

        $stateId = $this->normalizeGeographicId(
            $attributes['state_id'] ?? $current->state_id
        );

        $cityId = $this->normalizeGeographicId(
            $attributes['city_id'] ?? $current->city_id
        );

        $this->ensureGeographicCoherence(
            $countryId,
            $stateId,
            $cityId,
        );

        return $this->repository->update($id, $attributes);
    }

    private function normalizeGeographicId(mixed $value): int
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            $normalized = ltrim($value, '0');

            if ($normalized !== '') {
                $max = (string) PHP_INT_MAX;

                if (
                    strlen($normalized) < strlen($max) ||
                    (
                        strlen($normalized) === strlen($max) &&
                        strcmp($normalized, $max) <= 0
                    )
                ) {
                    return (int) $normalized;
                }
            }
        }

        throw new InvalidArgumentException(
            'La combinación geográfica indicada no es coherente.'
        );
    }

    private function ensureGeographicCoherence(
        int $countryId,
        int $stateId,
        int $cityId,
    ): void {
        if (
            !$this->geographicReference->isCoherent(
                $countryId,
                $stateId,
                $cityId,
            )
        ) {
            throw new InvalidArgumentException(
                'La combinación geográfica indicada no es coherente.'
            );
        }
    }
}
