<?php

declare(strict_types=1);

namespace App\Core\Navigation;

use App\Core\Navigation\Authorization\NavigationPermissionResolver;
use App\Core\Navigation\Bootstrap\NavigationBootstrapper;
use App\Core\Navigation\Bootstrap\NavigationManifestBootstrapper;
use App\Core\Navigation\Builder\NavigationBuilder;
use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\Contracts\NavigationCacheInvalidatorInterface;
use App\Core\Navigation\Cache\NavigationCache;
use App\Core\Navigation\Cache\NavigationCacheInvalidator;
use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationManifestBootstrapperInterface;
use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\Discovery\NavigationManifestDiscoveryService;
use App\Core\Navigation\Discovery\NavigationManifestFinder;
use App\Core\Navigation\Discovery\NavigationManifestLoader;
use App\Core\Navigation\Discovery\NavigationManifestRegistrar;
use App\Core\Navigation\Listeners\NavigationAuthorizationChangedListener;
use App\Core\Navigation\Loader\NavigationDefinitionLoader;
use App\Core\Navigation\Registrar\NavigationRegistrar;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use App\Core\Navigation\Registry\NavigationRegistry;
use App\Core\Navigation\Resolver\NavigationActiveResolver;
use App\Core\Navigation\Services\NavigationService;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use Illuminate\Support\Facades\Event;
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
            NavigationPermissionResolverInterface::class,
            NavigationPermissionResolver::class
        );

        $this->app->singleton(
            NavigationManifestFinderInterface::class,
            NavigationManifestFinder::class
        );

        $this->app->singleton(
            NavigationManifestLoaderInterface::class,
            NavigationManifestLoader::class
        );

        $this->app->singleton(
            NavigationManifestRegistrarInterface::class,
            NavigationManifestRegistrar::class
        );

        $this->app->singleton(
            NavigationManifestDiscoveryInterface::class,
            NavigationManifestDiscoveryService::class
        );

        $this->app->singleton(
            NavigationManifestBootstrapperInterface::class,
            NavigationManifestBootstrapper::class
        );

        $this->app->bind(
            NavigationCacheInterface::class,
            NavigationCache::class
        );

        $this->app->singleton(
            NavigationCacheInvalidatorInterface::class,
            NavigationCacheInvalidator::class
        );

        $this->app->singleton(
            NavigationServiceInterface::class,
            NavigationService::class
        );
    }

    public function boot(): void
    {
        Event::listen(
            AuthorizationChanged::class,
            NavigationAuthorizationChangedListener::class
        );

        app(
            NavigationDefinitionLoader::class
        )->load();

        app(
            NavigationBootstrapper::class
        )->boot();

        app(
            NavigationManifestBootstrapperInterface::class
        )->boot();
    }
}

