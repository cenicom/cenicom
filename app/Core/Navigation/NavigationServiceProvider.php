<?php

declare(strict_types=1);

namespace App\Core\Navigation;


use App\Core\Contracts\NavigationAuthorizationInterface;
use App\Core\Contracts\TestFormRepositoryInterface;

use App\Core\Navigation\Authorization\NavigationAuthorization;
use App\Core\Navigation\Bootstrap\NavigationBootstrapper;
use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Loader\NavigationDefinitionLoader;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use App\Core\Navigation\Registry\NavigationRegistry;
use App\Core\Navigation\Resolver\NavigationActiveResolver;
use App\Core\Navigation\View\NavigationViewComposer;
use App\Core\Repositories\TestFormRepository;
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

        $this->app->singleton(
            NavigationAuthorizationInterface::class,
            NavigationAuthorization::class
        );

        $this->app->bind(
            TestFormRepositoryInterface::class,
            TestFormRepository::class
        );

        $this->app->singleton(
            NavigationActiveResolver::class
        );

        $this->app->singleton(
            NavigationDefinitionRegistry::class,
            function () {
                return new NavigationDefinitionRegistry();
            }
        );

        $this->app->singleton(
            NavigationBootstrapper::class,
            function ($app) {

                return new NavigationBootstrapper(
                    $app->make(
                        NavigationDefinitionRegistry::class
                    ),
                    $app->make(
                        NavigationRegistrarInterface::class
                    )
                );
            }
        );

        $this->app->singleton(
            NavigationDefinitionRegistry::class,
            function () {
                return new NavigationDefinitionRegistry();
            }
        );

        $this->app->singleton(
            NavigationDefinitionLoader::class,
            function ($app) {

                return new NavigationDefinitionLoader(
                    $app->make(
                        NavigationDefinitionRegistry::class
                    )
                );
            }
        );

        $this->app->singleton(
            NavigationBootstrapper::class,
            function ($app) {

                return new NavigationBootstrapper(
                    $app->make(
                        NavigationDefinitionRegistry::class
                    ),
                    $app->make(
                        NavigationRegistrarInterface::class
                    )
                );
            }
        );

    }

    public function boot(): void
    {
        app(
            NavigationDefinitionLoader::class
        )->load();

        app(
            NavigationBootstrapper::class
        )->boot();

        View::composer(
            '*',
            NavigationViewComposer::class
        );
    }
}
