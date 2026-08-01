<?php

declare(strict_types=1);

namespace App\Core\Security\Providers;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Core\Security\Permissions\PermissionChecker;
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
            PermissionCheckerInterface::class,
            fn($app) => new PermissionChecker(
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
            AuthorizationServiceInterface::class,
            fn($app) => new AuthorizationService(
                $app->make(PermissionCheckerInterface::class)
            )
        );
    }


    /**
     * Inicializar servicios.
     */
    public function boot(): void
    {
        //
    }
}
