<?php

declare(strict_types=1);

namespace App\Core\View\Bootstrap;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Registry\ViewDefinitionRegistry;

final readonly class ViewBootstrapper
{
    public function __construct(
        private ViewDefinitionRegistry $definitions,
        private ViewRegistrarInterface $registrar,
    ) {
    }

    /**
     * Ejecuta todas las definiciones de vistas registradas.
     */
    public function boot(): void
    {
        foreach ($this->definitions->definitions() as $definition) {
            $view = app($definition);

            if (! $view instanceof ViewDefinitionInterface) {
                continue;
            }

            $view->register(
                $this->registrar
            );
        }
    }
}
