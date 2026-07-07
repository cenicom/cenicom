<?php

namespace App\Support\Core;

abstract class Manager
{
    protected static function config(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }

    protected static function has(string $key): bool
    {
        return config()->has($key);
    }

    protected static function collection(array $items)
    {
        return collect($items);
    }
}
