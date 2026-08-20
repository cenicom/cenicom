<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudDefinitionInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;

final readonly class CrudService
{
    public function __construct(
        private CrudDefinitionRegistryInterface $definitions,
        private CrudRegistrarInterface $registrar,
    ) {
    }

    /**
     * Ejecuta todas las definiciones CRUD registradas.
     */
    public function boot(): void
    {
        foreach ($this->definitions->definitions() as $definition) {
            $crud = app($definition);

            if (! $crud instanceof CrudDefinitionInterface) {
                continue;
            }

            $crud->register(
                $this->registrar
            );
        }
    }
}
