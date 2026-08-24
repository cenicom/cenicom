<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Bootstrap\CrudBootstrapper;
use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudActionAuthorization;

use App\Core\Crud\Loader\CrudDefinitionLoader;
use Illuminate\Support\ServiceProvider;

final class CrudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            CrudDefinitionLoader::class,
            function ($app) {
                return new CrudDefinitionLoader(
                    $app->make(
                        CrudDefinitionRegistryInterface::class
                    ),
                    $app->make(
                        ModuleRegistryInterface::class
                    ),
                );
            },
        );

        $this->app->singleton(
            CrudActionAuthorizationInterface::class,
            CrudActionAuthorization::class,
        );

        $this->app->singleton(
            CrudBootstrapper::class,
            function ($app) {
                return new CrudBootstrapper(
                    $app->make(CrudDefinitionRegistryInterface::class),
                    $app->make(CrudRegistrarInterface::class),
                );
            },
        );
    }

    public function boot(): void
    {
        app(CrudDefinitionLoader::class)->load();

        app(CrudBootstrapper::class)->boot();
    }
}
