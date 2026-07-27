<?php

declare(strict_types=1);

namespace App\Core\Navigation\Registry;

use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;

final class NavigationRegistry implements NavigationRegistryInterface
{
    private ?NavigationTreeData $tree = null;

    /**
     * @var array<string, NavigationGroupData>
     */
    private array $groups = [];

    /**
     * @var array<string, NavigationItemData>
     */
    private array $items = [];

    /**
     * Registra un grupo.
     */
    public function registerGroup(
        NavigationGroupData $group
    ): void {
        $this->groups[$group->id()] = $group;
    }

    /**
     * Registra un elemento.
     */
    public function registerItem(
        NavigationItemData $item
    ): void {
        $this->items[$item->id()] = $item;
    }

    /**
     * @return array<string, NavigationGroupData>
     */
    public function groups(): array
    {
        return $this->groups;
    }

    /**
     * @return array<string, NavigationItemData>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function tree(): NavigationTreeData
    {
        return $this->tree
            ?? new NavigationTreeData();
    }

    public function setTree(
        NavigationTreeData $tree
    ): void {
        $this->tree = $tree;
    }

    /**
     * Limpia completamente el registro.
     */
    public function clear(): void
    {
        $this->groups = [];
        $this->items = [];
        $this->tree = null;
    }


}
