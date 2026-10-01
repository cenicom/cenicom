<?php

namespace App\Support\UI;

use Illuminate\Support\Facades\Route;

class PageManager
{
    /**
     * Ruta actual.
     */
    public static function route(): ?string
    {
        return Route::currentRouteName();
    }

    /**
     * Configuración de la página.
     */
    public static function current(): array
    {
        return config(
            'cn-pages.'.self::route(),
            []
        );
    }

    public static function title(): ?string
    {
        return self::current()['title'] ?? null;
    }

    public static function subtitle(): ?string
    {
        return self::current()['subtitle'] ?? null;
    }

    public static function icon(): ?string
    {
        return self::current()['icon'] ?? null;
    }
}
