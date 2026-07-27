<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;

use App\Core\Navigation\DTO\NavigationTreeData;

interface NavigationRegistryInterface
{
    /**
     * Registra un grupo de navegación.
     */
    public function registerGroup(
        NavigationGroupData $group
    ): void;

    /**
     * Registra un elemento de navegación.
     */
    public function registerItem(
        NavigationItemData $item
    ): void;

    /**
     * Obtiene los grupos registrados.
     *
     * @return array<string, NavigationGroupData>
     */
    public function groups(): array;

    /**
     * Obtiene los elementos registrados.
     *
     * @return array<string, NavigationItemData>
     */
    public function items(): array;

    /**
     * Obtiene el árbol construido.
     */
    public function tree(): NavigationTreeData;

    /**
     * Guarda el árbol construido.
     */
    public function setTree(
        NavigationTreeData $tree
    ): void;

    /**
     * Limpia el registro completo.
     *
     * Útil para pruebas o reconstrucciones.
     */
    public function clear(): void;
}
