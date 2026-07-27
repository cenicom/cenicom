<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa un elemento navegable dentro del Registry.
 *
 * Responsabilidades:
 *
 * - Contener la información de un módulo.
 * - Servir como entrada del NavigationBuilder.
 * - Mantener datos necesarios para crear nodos ITEM.
 *
 * No debe:
 *
 * - Construir NavigationNodeData.
 * - Resolver permisos.
 * - Consultar rutas.
 * - Renderizar interfaces.
 *
 * ==========================================================
 */
final readonly class NavigationItemData
{
    public function __construct(
        private string $id,
        private string $label,
        private string $route,
        private ?string $icon = null,
        private int $order = 0,
        private string $group = '',
    ) {
    }


    public function id(): string
    {
        return $this->id;
    }


    public function label(): string
    {
        return $this->label;
    }


    public function route(): string
    {
        return $this->route;
    }


    public function icon(): ?string
    {
        return $this->icon;
    }


    public function order(): int
    {
        return $this->order;
    }


    public function group(): string
    {
        return $this->group;
    }
}
