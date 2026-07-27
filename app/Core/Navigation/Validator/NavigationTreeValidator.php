<?php

declare(strict_types=1);

namespace App\Core\Navigation\Validator;

use App\Core\Navigation\DTO\NavigationNodeData;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\DTO\NavigationValidationResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Valida la integridad estructural del árbol maestro
 * de navegación.
 *
 * Responsabilidades:
 *
 * - Validar existencia del árbol.
 * - Validar nodos principales.
 * - Validar estructura GROUP / ITEM.
 * - Reportar inconsistencias.
 *
 * ==========================================================
 */
final class NavigationTreeValidator
{

    private array $errors = [];

    private array $warnings = [];

    private function resetMessages(): void
    {
        $this->errors = [];

        $this->warnings = [];
    }
    /**
     * Valida la estructura completa del árbol.
     */
    public function validate(
        NavigationTreeData $tree
    ): NavigationValidationResult {
        $this->resetMessages();

        $valid = $this->validateDeepTree(
            $tree
        );

        return new NavigationValidationResult(
            valid: $valid,
            errors: $this->errors,
            warnings: $this->warnings
        );
    }

    /**
     * Valida la colección de nodos principales.
     *
     * @param array<int, NavigationNodeData> $nodes
     */
    private function validateNodes(
        array $nodes
    ): bool {
        foreach ($nodes as $node) {

            if (! $this->validateNode($node)) {

                return false;
            }
        }

        return true;
    }

    /**
     * Valida un nodo individual del árbol.
     */
    private function validateNode(
        NavigationNodeData $node
    ): bool {
        if ($node->id() === '') {
            $this->addError(
                'El nodo de navegación no posee identificador'
            );
            return false;
        }

        if ($node->label() === '') {
            $this->addError(
                'El nodo de navegación no posee etiqueta'
            );
            return false;
        }

        if (! $this->validateType($node)) {
            $this->addError(
                sprintf(
                    'Tipo de nodo inválido: %s',
                    $node->type()
                )
            );

            return false;
        }

        return $this->validateChildren($node);
    }

    /**
     * Valida que el tipo del nodo sea permitido.
     */
    private function validateType(
        NavigationNodeData $node
    ): bool {
        return in_array(
            $node->type(),
            [
                'GROUP',
                'ITEM',
            ],
            true
        );
    }

    /**
     * Valida la estructura jerárquica de los hijos.
     */
    private function validateChildren(
        NavigationNodeData $node
    ): bool {
        $children = $node->children();

        if ($node->type() === 'ITEM') {
            return $children === [];
        }

        foreach ($children as $child) {

            if ($child->type() !== 'ITEM') {
                $this->addError(
                    'Un nodo ITEM no puede contener hijos'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Ejecuta validaciones profundas sobre todo el árbol.
     */
    private function validateDeepTree(
        NavigationTreeData $tree
    ): bool {
        return $this->validateNodes(
            $tree->nodes()
        );
    }

    /**
     * Valida recursivamente las ramas del árbol.
     *
     * @param array<int, NavigationNodeData> $nodes
     */
    private function validateRecursiveNodes(
        array $nodes
    ): bool {
        foreach ($nodes as $node) {

            if (! $this->validateNode($node)) {
                return false;
            }

            if (
                $node->children() !== []
                && ! $this->validateRecursiveNodes(
                    $node->children()
                )
            ) {
                return false;
            }
        }

        return true;
    }

    private function addError(
        string $message
    ): void {
        $this->errors[] = $message;
    }

    private function addWarning(
        string $message
    ): void {
        $this->warnings[] = $message;
    }
}
