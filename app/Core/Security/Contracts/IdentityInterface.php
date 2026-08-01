<?php

declare(strict_types=1);

namespace App\Core\Security\Contracts;

interface IdentityInterface
{
    /**
     * Identificador único de la identidad autenticada.
     */
    public function id(): int|string|null;

    /**
     * Nombre visible de la identidad.
     */
    public function name(): string;

    /**
     * Roles asociados a la identidad.
     *
     * @return array<int, string>
     */
    public function roles(): array;

    /**
     * Permisos disponibles para la identidad.
     *
     * @return array<int, string>
     */
    public function permissions(): array;

    /**
     * Valida si la identidad posee un permiso.
     */
    public function can(string $permission): bool;

    /**
     * Indica si existe una identidad autenticada.
     */
    public function authenticated(): bool;
}
