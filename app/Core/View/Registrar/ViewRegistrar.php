<?php

declare(strict_types=1);

namespace App\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use Illuminate\View\ViewFinderInterface;

final readonly class ViewRegistrar implements ViewRegistrarInterface
{
    public function __construct(
        private ViewRegistryInterface $registry,
        private ViewFinderInterface $finder,
    ) {
    }

    /**
     * Registra un namespace y su ubicación física de vistas.
     */
    public function register(
        string $namespace,
        string $path,
    ): void {
        $this->registry->register(
            $namespace,
            $path,
        );

        $this->finder->addLocation(
            $path,
        );

        $this->finder->replaceNamespace(
            $namespace,
            $path,
        );
    }
}
