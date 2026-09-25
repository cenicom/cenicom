<?php

declare(strict_types=1);

namespace App\Core\View\Registrar;

use App\Core\View\Contracts\ViewPathResolverInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use Illuminate\View\ViewFinderInterface;

final readonly class ViewRegistrar implements ViewRegistrarInterface
{
    public function __construct(
        private ViewRegistryInterface $registry,
        private ViewFinderInterface $finder,
        private ViewPathResolverInterface $pathResolver,
    ) {
    }

    public function register(string $namespace, string $path): void
    {
        $this->registry->register($namespace, $path);

        $resolvedPath = $this->pathResolver->resolve($path);

        $this->finder->addLocation($resolvedPath);
        $this->finder->replaceNamespace(
            $namespace,
            $resolvedPath,
        );
    }
}
