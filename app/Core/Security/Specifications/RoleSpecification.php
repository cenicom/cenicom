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
 * posee un rol específico.
 *
 * Responsabilidades:
 *
 * - Encapsular la regla de pertenencia a un rol.
 * - Servir como Specification atómica del dominio
 *   de Seguridad.
 *
 * No debe:
 *
 * - Resolver servicios.
 * - Consultar repositorios.
 * - Conocer permisos.
 * - Modificar la identidad.
 * - Combinar Specifications.
 *
 * ==========================================================
 */
final class RoleSpecification extends AtomicSpecification
{
    /**
     * Rol que debe poseer la identidad.
     * @var string
     */
    private string $role;

    /**
     * Constructor.
     * Rol requerido por la Specification.
     *
     * @param string $role El rol a verificar.
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * Evalúa si la identidad cumple con la especificación.
     *
     * @return bool Verdadero si la identidad tiene el rol, falso en caso contrario.
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        // Aquí se implementaría la lógica para verificar si la identidad
        // posee el rol especificado. Esto podría implicar consultar un
        // servicio de seguridad o acceder a la información de la identidad.
        // Por simplicidad, se devuelve un valor ficticio.

        // Ejemplo ficticio:
        if (! $candidate instanceof IdentityInterface) {
            return false;
        }

        return in_array(
            $this->role,
            $candidate->roles(),
            true,
        ); // Simulación de verificación de rol
    }
}
