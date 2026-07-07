<?php

namespace App\Support\Modules;

use Illuminate\Support\Facades\File;

class ModuleManager
{
    /**
     * Caché de módulos cargados.
     */
    protected static ?array $modules = null;

    /**
     * Devuelve todos los módulos.
     */
    public static function all(): array
    {
        if (self::$modules !== null) {
            return self::$modules;
        }

        $modules = [];

        $path = config_path('cn/modules');

        if (! File::isDirectory($path)) {
            return [];
        }

        foreach (File::files($path) as $file) {

            $module = require $file->getRealPath();

            if (! isset($module['id'])) {
                continue;
            }

            $modules[$module['id']] = $module;
        }

        ksort($modules);

        return self::$modules = $modules;
    }

    /**
     * Obtener un módulo.
     */
    public static function get(string $id): ?array
    {
        return self::all()[$id] ?? null;
    }

    /**
     * Verificar existencia.
     */
    public static function exists(string $id): bool
    {
        return isset(self::all()[$id]);
    }

    /**
     * Limpiar caché.
     */
    public static function clear(): void
    {
        self::$modules = null;
    }
}
