<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeServiceInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción CnGeneratorProbeAction.
 *
 * Encapsula una operación específica del módulo.
 *
 * @package App\Modules\CnGeneratorProbe\Actions
 */
final readonly class CnGeneratorProbeAction
{
    public function __construct(
        private CnGeneratorProbeServiceInterface $service,
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
