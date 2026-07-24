<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Define la configuración de seguridad de un módulo.
 *
 * Responsabilidades:
 *
 * - Determinar si requiere autenticación.
 * - Determinar si requiere verificación.
 * - Administrar middleware adicionales.
 * - Servir como fuente de información para los generadores.
 *
 * @package App\Core\Generator\DTO
 * @since 1.0.0
 */
final readonly class SecurityDefinition
{
    /**
     * Constructor principal.
     */
    public function __construct(
        private bool $auth = true,
        private bool $verified = false,
        private array $middleware = [],
        private bool $permissions = false,
    ) {}


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    |
    | Métodos de consulta.
    |
    */


    /**
     * Indica si requiere autenticación.
     */
    public function requiresAuth(): bool
    {
        return $this->auth;
    }


    /**
     * Indica si requiere usuario verificado.
     */
    public function requiresVerified(): bool
    {
        return $this->verified;
    }


    /**
     * Obtiene middleware adicionales.
     *
     * @return array<int,string>
     */
    public function middleware(): array
    {
        return $this->middleware;
    }


    /**
     * Indica si utiliza permisos.
     */
    public function usesPermissions(): bool
    {
        return $this->permissions;
    }

    public static function fromArray(
        array $data
    ): self {

        return new self(
            auth: $data['auth'] ?? true,
            verified: $data['verified'] ?? false,
            middleware: $data['middleware'] ?? [],
            permissions: $data['permissions'] ?? false,
        );
    }
}
