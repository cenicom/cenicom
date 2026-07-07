<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Support\Registries\ModuleRegistry;

/**
 * @method static array all()
 * @method static array|null get(string $key)
 * @method static bool has(string $key)
 */
class Modules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModuleRegistry::class;
    }
}
