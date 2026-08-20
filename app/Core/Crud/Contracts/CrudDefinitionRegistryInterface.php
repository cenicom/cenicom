<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudDefinitionRegistryInterface
{
    /**
     * Agrega una definición CRUD.
     *
     * @param class-string $definition
     */
    public function add(string $definition): void;

    /**
     * Obtiene las definiciones CRUD registradas.
     *
     * @return array<int, class-string>
     */
    public function definitions(): array;

    /**
     * Limpia las definiciones registradas.
     */
    public function clear(): void;
}
