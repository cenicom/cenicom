<?php

declare(strict_types=1);

namespace App\Core\Navigation\Builder;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;

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
 * - Transformar grupos en nodos.
 * - Construir la estructura jerárquica.
 * - Entregar NavigationTreeData.
 *
 * No debe:
 *
 * - Renderizar menús.
 * - Resolver permisos.
 * - Consultar usuarios.
 * - Acceder directamente a base de datos.
 *
 * ==========================================================
 */
final class NavigationBuilder implements NavigationBuilderInterface
{
    public function __construct(
        private readonly NavigationRegistryInterface $registry,
    ) {}


    /**
     * Construye el árbol completo de navegación.
     */
    public function build(): NavigationTreeData
    {
        return new NavigationTreeData(
            nodes: $this->buildGroups()
        );
    }


    /**
     * Construye grupos principales como nodos.
     *
     * @return array<int, NavigationNodeData>
     */
    private function buildGroups(): array
    {
        $groups = $this->registry->groups();

        $items = $this->registry->items();


        return array_map(
            function (NavigationGroupData $group) use ($items): NavigationNodeData {

                $children = array_map(
                    fn(NavigationItemData $item): NavigationNodeData =>
                        $this->createItemNode($item),

                    array_filter(
                        $items,
                        fn(NavigationItemData $item): bool =>
                            $item->group() === $group->id()
                    )
                );


                return $this->createGroupNode(
                    $group,
                    array_values($children)
                );

            },
            array_values($groups)
        );
    }


    /**
     * Convierte un grupo en NavigationNodeData.
     */
    private function createGroupNode(
        NavigationGroupData $group,
        array $children
    ): NavigationNodeData {

        return new NavigationNodeData(
            id: $group->id(),
            label: $group->label(),
            type: 'GROUP',
            icon: $group->icon(),
            route: null,
            order: $group->order(),
            children: $children
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
            type: 'ITEM',
            icon: $item->icon(),
            route: $item->route(),
            order: $item->order(),
            children: []
        );
    }
}
