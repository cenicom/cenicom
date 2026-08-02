<?php

declare(strict_types=1);

namespace App\Core\Navigation\Resolver;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Contracts\NavigationActiveResolverInterface;
use Illuminate\Support\Facades\Route;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Enriquece el árbol de navegación con su estado visual.
 *
 * Responsabilidades:
 *
 * - Detectar el nodo correspondiente a la ruta actual.
 * - Marcar nodos current.
 * - Marcar nodos ancestor.
 * - Marcar nodos active.
 * - Marcar nodos expanded.
 *
 * No debe:
 *
 * - Construir el árbol.
 * - Resolver permisos.
 * - Registrar módulos.
 * * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationActiveResolver implements NavigationActiveResolverInterface
{
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
            $resolved[] = $this->resolveNode($node);
        }

        return $resolved;
    }

    private function resolveNode(
        NavigationNodeData $node
    ): NavigationNodeData {

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
        | Determinar estado
        |--------------------------------------------------------------------------
        */

        $current = $this->determineCurrent(
            $node
        );

        $ancestor = $this->determineAncestor(
            $children
        );

        $active = $this->determineActive(
            $current,
            $ancestor
        );

        $expanded = $this->determineExpanded(
            $active
        );

        /*
        |--------------------------------------------------------------------------
        | Devolver nueva instancia
        |--------------------------------------------------------------------------
        */

        return $node->withState(
            current: $current,
            active: $active,
            ancestor: $ancestor,
            expanded: $expanded,
        );
    }

    private function determineCurrent(
        NavigationNodeData $node
    ): bool {

        if ($node->route() === null) {
            return false;
        }

        return Route::currentRouteNamed(
            $node->route()
        );
    }

    /**
     * @param array<int, NavigationNodeData> $children
     */
    private function determineAncestor(
        array $children
    ): bool {

        foreach ($children as $child) {

            if ($child->isActive()) {
                return true;
            }
        }

        return false;
    }

    private function determineActive(
        bool $current,
        bool $ancestor
    ): bool {

        return $current || $ancestor;
    }

    private function determineExpanded(
        bool $active
    ): bool {

        return $active;
    }
}
