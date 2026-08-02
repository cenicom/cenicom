<?php

declare(strict_types=1);

namespace App\Core\Navigation\Authorization;



use App\Core\Contracts\NavigationAuthorizationInterface;
use App\Core\Navigation\DTO\NavigationNodeData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Implementación del sistema de autorización para
 * la navegación.
 *
 * Responsabilidades:
 *
 * - Evaluar si un nodo puede visualizarse.
 * - Delegar la autorización al sistema de permisos.
 *
 * No debe:
 *
 * - Construir árboles.
 * - Resolver rutas.
 * - Modificar nodos.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationAuthorization
    implements NavigationAuthorizationInterface
{
    public function allows(
        NavigationNodeData $node
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Nodo público
        |--------------------------------------------------------------------------
        */

        if ($this->isPublic($node)) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Evaluación de permisos
        |--------------------------------------------------------------------------
        */

        return $this->checkPermissions(
            $node
        );
    }

    private function isPublic(
        NavigationNodeData $node
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Fase inicial
        |--------------------------------------------------------------------------
        */

        return true;
    }

    private function checkPermissions(
        NavigationNodeData $node
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Integración con PermissionResolver
        |--------------------------------------------------------------------------
        */

        return true;
    }
}
