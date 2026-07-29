<?php

declare(strict_types=1);

namespace App\Core\Module\Providers;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use App\Core\Module\Bootstrap\ModuleProviderValidator;
use App\Core\Module\Discovery\ModuleManifestFinder;
use App\Core\Module\Registry\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerModuleBindings();
    }

    private function registerModuleBindings(): void
    {
        $this->app->bind(
            ModuleManifestFinderInterface::class,
            function () {
                return new ModuleManifestFinder(
                    base_path('Modules')
                );
            }
        );

        $this->app->bind(
            ModuleProviderRegistrarInterface::class,
            ModuleProviderRegistrar::class,
        );

        $this->app->bind(
            ModuleProviderValidatorInterface::class,
            ModuleProviderValidator::class,
        );

        $this->app->singleton(
            ModuleRegistryInterface::class,
            ModuleRegistry::class,
        );
    }

    public function boot(): void
    {
        //
    }
}
