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
 * se encuentra autenticada.
 *
 * Responsabilidades:
 *
 * - Encapsular la regla de autenticación.
 * - Servir como Specification atómica del dominio
 *   de Seguridad.
 *
 * No debe:
 *
 * - Resolver servicios.
 * - Consultar repositorios.
 * - Conocer permisos.
 * - Conocer roles.
 * - Combinar Specifications.
 *
 * ==========================================================
 */
final class AuthenticatedSpecification extends AtomicSpecification
{


    /**
     * Determina si la identidad se encuentra
     * autenticada.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool {
        if (! $candidate instanceof IdentityInterface) {
            return false;
        }

        return $candidate->authenticated();
    }
}
