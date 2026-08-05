<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Core\Generator\Support\Contracts\PathResolverInterface;

final class SandboxPathResolver implements PathResolverInterface
{
    public function __construct(
        private readonly string $root,
    ) {
    }

    public function app(string $path = ''): string
    {
        return $this->join('app', $path);
    }

    public function resource(string $path = ''): string
    {
        return $this->join('resources', $path);
    }

    public function database(string $path = ''): string
    {
        return $this->join('database', $path);
    }

    public function routes(string $path = ''): string
    {
        return $this->join('routes', $path);
    }

    public function base(string $path = ''): string
    {
        return $this->join('', $path);
    }

    private function join(
        string $prefix,
        string $path
    ): string {

        $base = rtrim(
            $this->root
            . DIRECTORY_SEPARATOR
            . $prefix,
            DIRECTORY_SEPARATOR
        );

        return $path === ''
            ? $base
            : $base . DIRECTORY_SEPARATOR . $path;
    }
}
