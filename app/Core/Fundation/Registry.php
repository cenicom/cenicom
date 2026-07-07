<?php

namespace App\Core\Foundation;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

abstract class Registry implements IteratorAggregate, Countable
{
    protected array $items = [];

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function set(string $key, mixed $value): static
    {
        $this->items[$key] = $value;

        return $this;
    }

    public function remove(string $key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function keys(): array
    {
        return array_keys($this->items);
    }

    public function values(): array
    {
        return array_values($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}