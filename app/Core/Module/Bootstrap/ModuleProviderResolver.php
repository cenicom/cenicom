<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;



use App\Core\Module\Bootstrap\Contracts\ModuleProviderResolverInterface;
use App\Core\Module\DTO\ModuleDefinition;



/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Responsable de resolver los Service Providers
 * pertenecientes a un módulo.
 *
 * Este componente únicamente determina qué
 * Providers corresponden al módulo indicado.
 *
 * No valida.
 * No registra.
 * No interactúa con Laravel.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
final readonly class ModuleProviderResolver implements ModuleProviderResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function resolve(ModuleDefinition $module): array
    {
        return [
            $this->resolvePrimaryProvider($module),
        ];
    }

    /**
     * Resuelve el Service Provider principal
     * del módulo.
     */
    private function resolvePrimaryProvider(
        ModuleDefinition $module,
    ): string {

        return sprintf(
            '%s\\Providers\\%sServiceProvider',
            $module->namespace,
            $module->name,
        );
    }
}
