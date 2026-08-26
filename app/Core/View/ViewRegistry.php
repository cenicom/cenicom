<?php

declare(strict_types=1);

namespace App\Core\View;

use App\Core\View\Contracts\ViewRegistryInterface;
use LogicException;

final class ViewRegistry implements ViewRegistryInterface
{
    /**
     * @var array<string, string>
     */
    private array $views = [];

    public function register(
        string $namespace,
        string $path,
    ): void {
        if (isset($this->views[$namespace])) {
            throw new LogicException(
                sprintf(
                    'View namespace [%s] is already registered.',
                    $namespace,
                ),
            );
        }

        $this->views[$namespace] = $path;
    }

    public function path(
        string $namespace
    ): ?string {
        return $this->views[$namespace] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->views;
    }

    public function clear(): void
    {
        $this->views = [];
    }
}
