<?php

namespace App\Providers;


use App\Support\Registries\ModuleRegistry;
use App\Support\Registries\NavigationRegistry;
use App\Support\Registries\PermissionRegistry;
use Illuminate\Support\ServiceProvider;


class CNFrameworkServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios del Framework.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class, function () {

            return (new ModuleRegistry())->load();

        });

        $this->app->singleton(
            NavigationRegistry::class
        );

        $this->app->singleton(
            PermissionRegistry::class
        );
    }

    /**
     * Inicializar el Framework.
     */
    public function boot(): void
    {
        $this->bootFramework();
    }

    /**
     * Registrar componentes internos.
     */
    protected function registerFramework(): void
    {
        //
    }

    /**
     * Inicializar componentes internos.
     */
    protected function bootFramework(): void
    {
        //
    }
}
