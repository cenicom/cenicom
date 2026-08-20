<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\Contracts\ResourceRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;
use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use App\Core\Crud\ResourceService;
use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Contracts\ViewRegistryInterface;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\ViewRegistry;
use Illuminate\Support\ServiceProvider;

final class CoreBindingsServiceProvider extends ServiceProvider
{
    /**
     * Registra todos los bindings del Core.
     */
    public function register(): void
    {
        $this->app->singleton(
            CrudRegistrarInterface::class,
            CrudRegistrar::class,
        );

        $this->app->singleton(
            ResourceRegistryInterface::class,
            fn($app) => $app->make(
                CrudRegistrarInterface::class
            ),
        );

        $this->app->singleton(
            CrudDefinitionRegistryInterface::class,
            CrudDefinitionRegistry::class,
        );

        $this->app->singleton(
            ResourceServiceInterface::class,
            ResourceService::class,
        );

        $this->app->singleton(
            ViewRegistryInterface::class,
            ViewRegistry::class,
        );

        $this->app->singleton(
            ViewRegistrarInterface::class,
            ViewRegistrar::class,
        );

        $bindings = config('cn-bindings', []);

        if (! is_array($bindings)) {
            return;
        }

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind(
                $abstract,
                $concrete,
            );
        }
    }
}
