<?php

declare(strict_types=1);

namespace App\Core\View\Registry;

final class ViewDefinitionRegistry
{
    /**
     * @var array<int, class-string>
     */
    private array $definitions = [];

    public function add(string $definition): void
    {
        $this->definitions[] = $definition;
    }

    /**
     * @return array<int, class-string>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    public function clear(): void
    {
        $this->definitions = [];
    }
}
