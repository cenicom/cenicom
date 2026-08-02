<?php

declare(strict_types=1);

namespace App\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Specification\AtomicSpecification;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que determina si una identidad
 * posee un permiso específico.
 *
 * Responsabilidades:
 *
 * - Encapsular la regla de autorización basada
 *   en un único permiso.
 * - Servir como Specification atómica del dominio
 *   de Seguridad.
 *
 * No debe:
 *
 * - Resolver servicios.
 * - Consultar repositorios.
 * - Conocer navegación.
 * - Combinar Specifications.
 *
 * ==========================================================
 */
final class PermissionSpecification extends AtomicSpecification
{
    /**
     * Permiso requerido por la Specification.
     */
    private string $permission;

    /**
     * Constructor de la Specification.
     */
    public function __construct(string $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Determina si la identidad posee
     * el permiso requerido.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool {
        if (! $candidate instanceof IdentityInterface) {
            return false;
        }

        return $candidate->can(
            $this->permission
        );
    }
}
