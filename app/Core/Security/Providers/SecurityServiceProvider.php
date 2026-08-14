<?php

declare(strict_types=1);

namespace App\Core\Security\Providers;

use App\Core\Security\Authorization\PermissionResolver;
use App\Core\Security\Authorization\RolePermissionResolver;
use App\Core\Security\Authorization\Contracts\RolePermissionResolverInterface;
use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;

use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;

use App\Core\Security\Permissions\PermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistry;
use App\Core\Security\Roles\Contracts\RoleCheckerInterface;
use App\Core\Security\Roles\Contracts\RoleRegistrarInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\RoleChecker;
use App\Core\Security\Roles\RoleRegistrar;
use App\Core\Security\Roles\RoleRegistry;
use App\Core\Security\Services\IdentityService;
use Illuminate\Support\ServiceProvider;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Identity\Identity;
use App\Core\Security\Authorization\AuthorizationAssignmentService;
use App\Core\Security\Authorization\Contracts\AuthorizationAssignmentServiceInterface;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\PermissionDefinitionRegistry;
use App\Core\Security\Permissions\Bootstrap\PermissionDefinitionBootstrapper;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\PolicyRegistry;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use App\Core\Security\Policies\PolicyResolver;

final class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios del Security Layer.
     */
    public function register(): void
    {
        $this->app->singleton(
            IdentityService::class,
            fn($app) => new IdentityService(
                $app->make(
                    \Illuminate\Contracts\Auth\Factory::class
                )
            )
        );

        $this->app->singleton(
            PermissionRegistryInterface::class,
            fn() => new PermissionRegistry()
        );

        $this->app->singleton(
            PermissionRegistrarInterface::class,
            fn($app) => new PermissionRegistrar(
                $app->make(
                    PermissionRegistryInterface::class
                )
            )
        );

        $this->app->singleton(
            RoleRegistryInterface::class,
            fn() => new RoleRegistry()
        );

        $this->app->singleton(
            RoleRegistrarInterface::class,
            fn($app) => new RoleRegistrar(
                $app->make(RoleRegistryInterface::class)
            )
        );

        $this->app->singleton(
            RoleCheckerInterface::class,
            fn() => new RoleChecker()
        );

        $this->app->singleton(
            RolePermissionResolverInterface::class,
            fn($app) => new RolePermissionResolver(
                $app->make(RoleRegistryInterface::class)
            )
        );

        $this->app->singleton(
            AuthorizationServiceInterface::class,
            fn($app) => new AuthorizationService(
                $app->make(PermissionResolverInterface::class),
                $app->make(PolicyResolverInterface::class),
            )
        );

        $this->app->bind(
            IdentityInterface::class,
            function ($app): IdentityInterface {
                $data = $app
                    ->make(IdentityService::class)
                    ->current();

                return new Identity(
                    id: $data->id,
                    name: $data->name,
                    roles: $data->roles,
                    permissions: $data->permissions,
                );
            }
        );

        $this->app->singleton(
            AuthorizationAssignmentServiceInterface::class,
            AuthorizationAssignmentService::class
        );

        $this->app->singleton(
            PermissionResolverInterface::class,
            fn($app) => new PermissionResolver(
                $app->make(
                    RolePermissionResolverInterface::class
                )
            )
        );

        $this->app->singleton(
            PermissionDefinitionRegistryInterface::class,
            PermissionDefinitionRegistry::class
        );

        $this->app->singleton(
            PermissionDefinitionLoader::class,
            fn($app) => new PermissionDefinitionLoader(
                $app->make(
                    PermissionDefinitionRegistryInterface::class
                )
            )
        );

        $this->app->singleton(
            PermissionDefinitionBootstrapper::class,
            fn($app) => new PermissionDefinitionBootstrapper(
                $app->make(
                    PermissionDefinitionRegistryInterface::class
                ),
                $app->make(
                    PermissionRegistrarInterface::class
                )
            )
        );

        $this->app->singleton(
            PolicyRegistryInterface::class,
            PolicyRegistry::class
        );

        $this->app->singleton(
            PolicyResolverInterface::class,
            fn($app) => new PolicyResolver(
                $app->make(
                    PolicyRegistryInterface::class
                )
            )
        );
    }


    /**
     * Inicializar servicios.
     */
    public function boot(): void
    {
        //
        app(
            PermissionDefinitionLoader::class
        )->load();

        app(
            PermissionDefinitionBootstrapper::class
        )->boot();
    }
}
