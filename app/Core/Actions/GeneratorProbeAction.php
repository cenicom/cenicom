<?php

declare(strict_types=1);

namespace App\Core\Actions;

use App\Models\GeneratorProbe;
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
 * @package App\Core\Actions
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
    ): GeneratorProbe
    {
        return $this->service->create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(
        GeneratorProbe $generatorProbe,
        array $data
    ): bool
    {
        return $this->service->update(
            $generatorProbe,
            $data
        );
    }

    public function delete(
        GeneratorProbe $generatorProbe
    ): bool
    {
        return $this->service->delete(
            $generatorProbe->getKey()
        );
    }
}
