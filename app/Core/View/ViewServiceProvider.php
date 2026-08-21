<?php

declare(strict_types=1);

namespace App\Core\View;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Contracts\ViewRegistrarInterface;
use App\Core\View\Loader\ViewDefinitionLoader;
use App\Core\View\Registry\ViewDefinitionRegistry;
use Illuminate\Support\ServiceProvider;

final class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ViewDefinitionRegistry::class,
            ViewDefinitionRegistry::class,
        );

        $this->app->singleton(
            ViewDefinitionLoader::class,
            function ($app) {
                return new ViewDefinitionLoader(
                    $app->make(ViewDefinitionRegistry::class),
                    $app->make(ModuleRegistryInterface::class),
                );
            },
        );

        $this->app->singleton(
            ViewBootstrapper::class,
            function ($app) {
                return new ViewBootstrapper(
                    $app->make(ViewDefinitionRegistry::class),
                    $app->make(ViewRegistrarInterface::class),
                );
            },
        );
    }

    public function boot(): void
    {
        app(ViewDefinitionLoader::class)->load();

        app(ViewBootstrapper::class)->boot();
    }
}
