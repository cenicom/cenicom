<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

use Illuminate\Support\Facades\Route;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa un nodo dentro del árbol de navegación.
 *
 * Responsabilidades:
 *
 * - Mantener la información de un elemento navegable.
 * - Representar grupos y módulos.
 * - Contener nodos hijos.
 *
 * No debe:
 *
 * - Construir nodos.
 * - Resolver permisos.
 * - Consultar Registry.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationNodeData
{
    /**
     * @param array<int, NavigationNodeData> $children
     */
    public function __construct(
        private string $id,
        private string $label,
        private string $type,
        private ?string $icon = null,
        private ?string $route = null,
        private int $order = 0,
        private array $children = [],
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function route(): ?string
    {
        return $this->route;
    }

    public function order(): int
    {
        return $this->order;
    }

    /**
     * @return array<int, NavigationNodeData>
     */
    public function children(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function isLeaf(): bool
    {
        return $this->children === [];
    }

    public function withChildren(
        array $children
    ): self {
        return new self(
            id: $this->id,
            label: $this->label,
            type: $this->type,
            icon: $this->icon,
            route: $this->route,
            order: $this->order,
            children: $children,
        );
    }

    public function url(): string
    {
        if (! $this->route) {
            return '#';
        }

        if (! Route::has($this->route)) {
            return '#';
        }

        return route($this->route);
    }
}
