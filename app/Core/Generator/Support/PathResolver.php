<?php

namespace App\Core\Generator\Support;

use App\Core\Generator\Support\Contracts\PathResolverInterface;

final class PathResolver implements PathResolverInterface
{
    public function __construct(
        private ?string $appBase = null,
        private ?string $resourceBase = null,
        private ?string $databaseBase = null,
        private ?string $routesBase = null,
    ) {}

    public function app(string $path = ''): string
    {
        $base = $this->appBase ?? app_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

    public function resource(string $path = ''): string
    {
        $base = $this->resourceBase ?? resource_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

    public function database(string $path = ''): string
    {
        $base = $this->databaseBase ?? database_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

    public function routes(string $path = ''): string
    {
        $base = $this->routesBase ?? base_path('routes');

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

    public function base(string $path = ''): string
    {
        return base_path($path);
    }



}
