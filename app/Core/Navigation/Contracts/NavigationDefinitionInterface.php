<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

interface NavigationDefinitionInterface
{
    /**
     * Registra la navegación del módulo.
     */
    public function register(
        NavigationRegistrarInterface $navigation
    ): void;
}
