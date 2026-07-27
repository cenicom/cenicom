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
        private ?string $url = null,
        private array $routeParameters = [],
        private bool $current = false,
        private bool $active = false,
        private bool $ancestor = false,
        private bool $expanded = false,

    ) {}

    public function href(): string
    {
        if ($this->url !== null) {
            return $this->url;
        }

        if (
            $this->route === null ||
            ! Route::has($this->route)
        ) {
            return '#';
        }

        return route(
            $this->route,
            $this->routeParameters
        );
    }

    public function routeParameters(): array
    {
        return $this->routeParameters;
    }

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
            url: $this->url,
            routeParameters: $this->routeParameters,
            current: $this->current,
            active: $this->active,
            ancestor: $this->ancestor,
            expanded: $this->expanded,

        );
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isAncestor(): bool
    {
        return $this->ancestor;
    }

    public function isExpanded(): bool
    {
        return $this->expanded;
    }

    public function withState(
        bool $current,
        bool $active,
        bool $ancestor,
        bool $expanded,
    ): self {
        return new self(
            id: $this->id,
            label: $this->label,
            type: $this->type,
            icon: $this->icon,
            route: $this->route,
            order: $this->order,
            children: $this->children,
            url: $this->url,
            routeParameters: $this->routeParameters,
            current: $current,
            active: $active,
            ancestor: $ancestor,
            expanded: $expanded,
        );
    }
}
