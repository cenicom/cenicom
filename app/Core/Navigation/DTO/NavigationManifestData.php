<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa un manifiesto completo de navegación
 * perteneciente a un módulo.
 *
 * Responsabilidades:
 *
 * - Contener el nombre del módulo.
 * - Contener los grupos registrados.
 * - Contener los ítems registrados.
 *
 * No debe:
 *
 * - Construir grupos.
 * - Construir ítems.
 * - Resolver permisos.
 * - Registrar navegación.
 * - Construir árboles.
 *
 * ==========================================================
 */
final readonly class NavigationManifestData
{
    /**
     * @param array<int, NavigationGroupData> $groups
     * @param array<int, NavigationItemData>  $items
     */
    public function __construct(
        public string $module,
        public array $groups = [],
        public array $items = [],
    ) {
    }

    /**
     * Indica si el manifiesto no contiene grupos ni ítems.
     */
    public function isEmpty(): bool
    {
        return $this->groups === []
            && $this->items === [];
    }

    /**
     * Cantidad de grupos.
     */
    public function groupCount(): int
    {
        return count($this->groups);
    }

    /**
     * Cantidad de ítems.
     */
    public function itemCount(): int
    {
        return count($this->items);
    }

    /**
     * Devuelve el manifiesto como arreglo.
     *
     * @return array{
     *     module:string,
     *     groups:array<int, NavigationGroupData>,
     *     items:array<int, NavigationItemData>
     * }
     */
    public function toArray(): array
    {
        return [
            'module' => $this->module,
            'groups' => $this->groups,
            'items'  => $this->items,
        ];
    }
}
