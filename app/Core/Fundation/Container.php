<?php

namespace App\Core\Foundation;

final class Container
{
    public static function kernel(): Kernel
    {
        return app(Kernel::class);
    }

    public static function modules()
    {
        return app(\App\Support\Registries\ModuleRegistry::class);
    }

    public static function navigation()
    {
        return app(\App\Support\Registries\NavigationRegistry::class);
    }

    public static function permissions()
    {
        return app(\App\Support\Registries\PermissionRegistry::class);
    }
}
