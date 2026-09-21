<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeServiceInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción GeneratorProbeAction.
 *
 * Encapsula una operación específica del módulo.
 *
 * @package App\Modules\GeneratorProbe\Actions
 */
final readonly class GeneratorProbeAction
{
    public function __construct(
        private GeneratorProbeServiceInterface $service,
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
