<?php

namespace App\Support\UI;

class ActionManager
{
    /**
     * Obtiene la configuración de una acción.
     */
    public static function get(string $action): array
    {
        return config("cn-actions.$action", []);
    }

    /**
     * Verifica si existe.
     */
    public static function exists(string $action): bool
    {
        return config()->has("cn-actions.$action");
    }

    /**
     * Devuelve todas las acciones.
     */
    public static function all(): array
    {
        return config('cn-actions', []);
    }
}
