<?php

declare(strict_types=1);

namespace App\Core\Security\Policies;

use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;

final class PolicyRegistry implements PolicyRegistryInterface
{
    /**
     * @var array<string, PolicyInterface>
     */
    private array $policies = [];

    /**
     * Registra una Policy.
     */
    public function register(
        string $name,
        PolicyInterface $policy
    ): void {
        $this->policies[$name] = $policy;
    }

    /**
     * Obtiene una Policy por nombre.
     */
    public function policy(
        string $name
    ): ?PolicyInterface {
        return $this->policies[$name] ?? null;
    }

    /**
     * Obtiene todas las Policies registradas.
     *
     * @return array<string, PolicyInterface>
     */
    public function all(): array
    {
        return $this->policies;
    }

    /**
     * Limpia el registro.
     */
    public function clear(): void
    {
        $this->policies = [];
    }
}
