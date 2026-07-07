<?php

namespace App\Support\Config;

class ConfigRepository
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }

    public static function actions(): array
    {
        return config('cn.actions', []);
    }

    public static function pages(): array
    {
        return config('cn.pages', []);
    }

    public static function navigation(): array
    {
        return config('cn.navigation', []);
    }

    public static function dashboard(): array
    {
        return config('cn.dashboard', []);
    }

    public static function ui(): array
    {
        return config('cn.ui', []);
    }
}
