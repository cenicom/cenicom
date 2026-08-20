<?php

declare(strict_types=1);

namespace App\Core\View\Registrar;

use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;

final readonly class ViewRegistrar implements ViewRegistrarInterface
{
    public function __construct(
        private ViewRegistryInterface $registry
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
    }
}
