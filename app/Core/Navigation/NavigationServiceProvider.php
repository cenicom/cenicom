<?php

declare(strict_types=1);

namespace App\Core\Navigation;

use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Registry\NavigationRegistry;
use App\Core\Navigation\View\NavigationViewComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


final class NavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            NavigationRegistryInterface::class,
            NavigationRegistry::class
        );

        $this->app->singleton(
            NavigationRegistrarInterface::class,
            NavigationRegistrar::class
        );

        $this->app->singleton(
            NavigationBuilderInterface::class,
            NavigationBuilder::class
        );
    }

    public function boot(): void
    {
        View::composer(
            '*',
            NavigationViewComposer::class
        );
    }
}
