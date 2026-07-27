<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa la definición de un grupo de navegación.
 *
 * Responsabilidades:
 *
 * - Definir un grupo de navegación.
 * - Ser registrado por NavigationRegistrar.
 * - Servir como entrada del NavigationBuilder.
 *
 * No debe:
 *
 * - Contener hijos.
 * - Representar el árbol de navegación.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationGroupData
{
    public function __construct(
        private string $id,
        private string $label,
        private ?string $icon,
        private int $order,
    ) {
    }

    /**
     * Identificador único del grupo.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Texto visible del grupo.
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Icono del grupo.
     */
    public function icon(): ?string
    {
        return $this->icon;
    }

    /**
     * Orden de visualización.
     */
    public function order(): int
    {
        return $this->order;
    }
}
