<?php

declare(strict_types=1);

namespace App\Core\Security\Policies\Contracts;

interface PolicyRegistryInterface
{
    /**
     * Registra una Policy bajo una clave.
     */
    public function register(
        string $name,
        PolicyInterface $policy
    ): void;

    /**
     * Obtiene una Policy registrada.
     */
    public function policy(
        string $name
    ): ?PolicyInterface;

    /**
     * Obtiene todas las Policies registradas.
     *
     * @return array<string, PolicyInterface>
     */
    public function all(): array;

    /**
     * Limpia el registro completo.
     */
    public function clear(): void;
}
