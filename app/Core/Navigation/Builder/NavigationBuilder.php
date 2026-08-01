<?php

declare(strict_types=1);

namespace App\Core\Navigation\Builder;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Enums\NavigationNodeType;
use App\Core\Security\Contracts\IdentityInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye el árbol maestro de navegación.
 *
 * Responsabilidades:
 *
 * - Leer información del Navigation Registry.
 * - Filtrar elementos autorizados.
 * - Transformar grupos en nodos.
 * - Construir la estructura jerárquica.
 * - Entregar NavigationTreeData.
 *
 * No debe:
 *
 * - Renderizar menús.
 * - Resolver permisos.
 * - Consultar roles.
 * - Acceder directamente a base de datos.
 *
 * ==========================================================
 */
final class NavigationBuilder implements NavigationBuilderInterface
{
    public function __construct(
        private readonly NavigationRegistryInterface $registry,
        private readonly NavigationPermissionResolverInterface $permissionResolver,
        private readonly IdentityInterface $identity,
    ) {
    }

    /**
     * Construye el árbol completo de navegación.
     */
    public function build(): NavigationTreeData
    {
        return new NavigationTreeData(
            nodes: $this->buildGroups(),
        );
    }

    /**
     * Construye los grupos principales del árbol.
     *
     * Los grupos que no contengan elementos visibles
     * para la identidad actual son descartados.
     *
     * @return array<int, NavigationNodeData>
     */
    private function buildGroups(): array
    {
        $groups = $this->registry->groups();
        $items = $this->registry->items();

        $nodes = [];

        foreach ($groups as $group) {

            $children = [];

            foreach ($items as $item) {

                if ($item->group() !== $group->id()) {
                    continue;
                }

                if (! $this->permissionResolver->canView(
                    $this->identity,
                    $item->permission()
                )) {
                    continue;
                }

                $children[] = $this->createItemNode($item);
            }

            /*
            |--------------------------------------------------------------------------
            | Omitir grupos vacíos
            |--------------------------------------------------------------------------
            */

            if ($children === []) {
                continue;
            }

            $nodes[] = $this->createGroupNode(
                $group,
                $children
            );
        }

        return $nodes;
    }

    /**
     * Convierte un grupo en NavigationNodeData.
     *
     * @param array<int, NavigationNodeData> $children
     */
    private function createGroupNode(
        NavigationGroupData $group,
        array $children
    ): NavigationNodeData {
        return new NavigationNodeData(
            id: $group->id(),
            label: $group->label(),
            type: NavigationNodeType::GROUP,
            icon: $group->icon(),
            route: null,
            order: $group->order(),
            children: $children,
        );
    }

    /**
     * Convierte un item en NavigationNodeData.
     */
    private function createItemNode(
        NavigationItemData $item
    ): NavigationNodeData {
        return new NavigationNodeData(
            id: $item->id(),
            label: $item->label(),
            type: NavigationNodeType::ITEM,
            icon: $item->icon(),
            route: $item->route(),
            order: $item->order(),
            children: [],
        );
    }
}
