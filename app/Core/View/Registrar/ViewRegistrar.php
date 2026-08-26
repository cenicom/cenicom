<?php

declare(strict_types=1);

namespace App\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use Illuminate\Contracts\View\Factory as ViewFactory;

final readonly class ViewRegistrar implements ViewRegistrarInterface
{
    public function __construct(
        private ViewRegistryInterface $registry,
        private ViewFactory $views,
    ) {
    }

    /**
     * Registra un namespace de vistas.
     */
    public function register(
        string $namespace,
        string $path,
    ): void {
        $this->registry->register(
            $namespace,
            $path,
        );

        $this->views->replaceNamespace(
            $namespace,
            $path,
        );
    }
}
