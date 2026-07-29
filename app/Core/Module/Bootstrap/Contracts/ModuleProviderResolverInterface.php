<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Contracts;


use App\Core\Module\DTO\ModuleDefinition;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato para resolver los Service Providers
 * pertenecientes a un módulo.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
interface ModuleProviderResolverInterface
{
    /**
     * Resuelve los Service Providers asociados
     * al módulo indicado.
     *
     * @return array<class-string>
     */
    public function resolve(ModuleDefinition $module): array;
}
