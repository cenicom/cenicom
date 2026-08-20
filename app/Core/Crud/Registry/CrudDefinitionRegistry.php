<?php

declare(strict_types=1);

namespace App\Core\Crud\Registry;

use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;

final class CrudDefinitionRegistry implements CrudDefinitionRegistryInterface
{
    /**
     * @var array<int, class-string>
     */
    private array $definitions = [];

    /**
     * Agrega una definición CRUD.
     *
     * @param class-string $definition
     */
    public function add(string $definition): void
    {
        $this->definitions[] = $definition;
    }

    /**
     * Obtiene las definiciones registradas.
     *
     * @return array<int, class-string>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    /**
     * Limpia las definiciones.
     */
    public function clear(): void
    {
        $this->definitions = [];
    }
}
