<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato público para consumo de navegación.
 *
 * Responsabilidades:
 *
 * - Exponer árbol maestro.
 * - Entregar grupos.
 * - Entregar items.
 *
 * ==========================================================
 */
interface NavigationServiceInterface
{
    /**
     * Obtiene el árbol completo de navegación.
     */
    public function tree(): NavigationTreeData;


}
