<?php

declare(strict_types=1);

namespace App\Modules\City\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Modules\City\Domain\Contracts\CityServiceInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción CityAction.
 *
 * Encapsula una operación específica del módulo.
 *
 * @package App\Modules\City\Actions
 */
final readonly class CityAction
{
    public function __construct(
        private CityServiceInterface $service,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(
        array $data
    ): Model {
        return $this->service->create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(
        int|string $id,
        array $data
    ): bool {
        return $this->service->update(
            $id,
            $data
        );
    }

    public function delete(
        int|string $id
    ): bool {
        return $this->service->delete(
            $id
        );
    }
}
