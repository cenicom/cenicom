<?php

declare(strict_types=1);

namespace App\Core\View\Contracts;

interface ViewDefinitionInterface
{
    /**
     * Registra las vistas del módulo.
     */
    public function register(
        ViewRegistrarInterface $views
    ): void;
}
