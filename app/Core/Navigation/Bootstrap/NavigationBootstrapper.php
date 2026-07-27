<?php

declare(strict_types=1);

namespace App\Core\Navigation\Bootstrap;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;

final readonly class NavigationBootstrapper
{
    public function __construct(
        private NavigationDefinitionRegistry $definitions,
        private NavigationRegistrarInterface $registrar,
    ) {
    }


    /**
     * Ejecuta todas las definiciones registradas.
     */
    public function boot(): void
    {
        foreach ($this->definitions->definitions() as $definition) {

            $navigation = app($definition);

            if (! $navigation instanceof NavigationDefinitionInterface) {
                continue;
            }

            $navigation->register(
                $this->registrar
            );
        }
    }
}
