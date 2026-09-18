<?php

declare(strict_types=1);

namespace App\Modules\Country\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Country\Domain\Contracts\CountryServiceInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción CountryAction.
 *
 * Encapsula una operación específica del módulo.
 *
 * @package App\Modules\Country\Actions
 */
final readonly class CountryAction
{
    public function __construct(
        private CountryServiceInterface $service,
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
