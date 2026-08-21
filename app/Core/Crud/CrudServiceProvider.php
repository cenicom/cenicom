<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;
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
            CrudService::class,
            CrudService::class,
        );
    }

    public function boot(): void
    {
        app(CrudDefinitionLoader::class)->load();

        app(CrudService::class)->boot();
    }
}
