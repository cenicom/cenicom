<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

use App\Core\Navigation\DTO\NavigationNodeData;



/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Representa el árbol completo de navegación.
 *
 * Responsabilidades:
 *
 * - Contener la raíz del árbol.
 * - Mantener la colección de nodos.
 * - Servir como modelo de salida del NavigationBuilder.
 *
 * No debe:
 *
 * - Construir nodos.
 * - Consultar Registry.
 * - Resolver permisos.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationTreeData
{
    /**
     * @param array<int, NavigationNodeData> $nodes
     */
    public function __construct(
        private array $nodes = []
    ) {
    }

    /**
     * Obtiene los nodos principales del árbol.
     *
     * @return array<int, NavigationNodeData>
     */
    public function nodes(): array
    {
        return $this->nodes;
    }

    /**
     * Indica si el árbol no contiene nodos.
     */
    public function isEmpty(): bool
    {
        return $this->nodes === [];
    }

    /**
     * Cantidad de nodos principales.
     */
    public function count(): int
    {
        return count($this->nodes);
    }
}
