<?php

declare(strict_types=1);

namespace App\Core\Module\Providers;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleBootstrapReporterInterface;
use App\Core\Contracts\Module\ModuleDefinitionFactoryInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapPipeline;
use App\Core\Module\Bootstrap\ModuleProviderRegistrar;
use App\Core\Module\Bootstrap\ModuleProviderValidator;
use App\Core\Module\Bootstrap\NullModuleBootstrapReporter;
use App\Core\Module\Bootstrap\Stages\CreateDefinitionStage;
use App\Core\Module\Bootstrap\Stages\RegisterModuleStage;
use App\Core\Module\Bootstrap\Stages\RegisterProvidersStage;
use App\Core\Module\Bootstrap\Stages\ValidationStage;
use App\Core\Module\Discovery\ModuleManifestFinder;
use App\Core\Module\Factory\ModuleDefinitionFactory;
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
                    base_path('tests/Fixtures/Modules')
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

        $this->app->bind(
            ModuleDefinitionFactoryInterface::class,
            ModuleDefinitionFactory::class,
        );

        $this->app->singleton(
            ModuleBootstrapReporterInterface::class,
            NullModuleBootstrapReporter::class,
        );

        $this->app->singleton(
            ModuleBootstrapPipelineInterface::class,
            function ($app) {
                return new ModuleBootstrapPipeline([
                    $app->make(CreateDefinitionStage::class),
                    $app->make(ValidationStage::class),
                    $app->make(RegisterModuleStage::class),
                    $app->make(RegisterProvidersStage::class),
                ]);
            }
        );

        $this->app->bind(
            CreateDefinitionStage::class,
            CreateDefinitionStage::class,
        );

        $this->app->bind(
            ValidationStage::class,
            ValidationStage::class,
        );

        $this->app->bind(
            RegisterModuleStage::class,
            RegisterModuleStage::class,
        );

        $this->app->bind(
            RegisterProvidersStage::class,
            RegisterProvidersStage::class,
        );

        $this->app->singleton(
            ModuleBootstrap::class,
            function ($app) {
                return new ModuleBootstrap(
                    $app->make(ModuleManifestFinderInterface::class),
                    $app->make(ModuleBootstrapPipelineInterface::class),
                );
            }
        );
    }

    public function boot(): void
    {
        //
        if ($this->app->runningUnitTests()) {
            return;
        }

        app(ModuleBootstrap::class)->bootstrap();
    }
}
