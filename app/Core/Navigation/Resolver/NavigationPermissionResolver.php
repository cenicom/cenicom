<?php

declare(strict_types=1);

namespace App\Core\Navigation\Resolver;

use App\Core\Contracts\NavigationAuthorizationInterface;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Filtra el árbol de navegación según los permisos del usuario.
 *
 * Responsabilidades:
 *
 * - Recorrer el árbol de navegación.
 * - Evaluar permisos de cada nodo.
 * - Eliminar nodos no autorizados.
 * - Eliminar grupos vacíos cuando corresponda.
 * - Devolver un nuevo árbol inmutable.
 *
 * No debe:
 *
 * - Construir el árbol.
 * - Resolver rutas.
 * - Marcar nodos activos.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationPermissionResolver
{
    public function __construct(
        private NavigationAuthorizationInterface $authorization,
    ) {}

    public function resolve(
        NavigationTreeData $tree
    ): NavigationTreeData {

        return new NavigationTreeData(
            nodes: $this->resolveNodes(
                $tree->nodes()
            ),
        );
    }

    /**
     * @param array<int, NavigationNodeData> $nodes
     *
     * @return array<int, NavigationNodeData>
     */
    private function resolveNodes(
        array $nodes
    ): array {

        $resolved = [];

        foreach ($nodes as $node) {

            $resolvedNode = $this->resolveNode($node);

            if ($resolvedNode !== null) {
                $resolved[] = $resolvedNode;
            }
        }

        return $resolved;
    }

    private function resolveNode(
        NavigationNodeData $node
    ): ?NavigationNodeData {

        /*
        |--------------------------------------------------------------------------
        | Resolver hijos
        |--------------------------------------------------------------------------
        */

        $children = $this->resolveNodes(
            $node->children()
        );

        $node = $node->withChildren(
            $children
        );

        /*
        |--------------------------------------------------------------------------
        | Evaluar permisos
        |--------------------------------------------------------------------------
        */

        if (! $this->hasPermission($node)) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Evaluar permanencia
        |--------------------------------------------------------------------------
        */

        if ($node->hasChildren()) {

            if (! $this->shouldKeepGroup($node)) {
                return null;
            }

            return $node;
        }

        if (! $this->shouldKeepItem($node)) {
            return null;
        }

        return $node;
    }

    private function hasPermission(
        NavigationNodeData $node
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Fase 2
        |--------------------------------------------------------------------------
        |
        | Aquí se integrará el sistema de permisos del Core.
        |
        */

        return $this->authorization->allows(
            $node
        );
    }

    private function shouldKeepGroup(
        NavigationNodeData $node
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Fase 3
        |--------------------------------------------------------------------------
        */

        return $node->hasChildren();
    }

    private function shouldKeepItem(
        NavigationNodeData $node
    ): bool {

        return true;
    }
}
