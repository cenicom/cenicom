<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Generator\GeneratorRegistry;
use App\Core\Generator\ModuleGenerator;
use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Generators\RepositoryGenerator;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Generators\RouteGenerator;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\ViewGenerator;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Service Provider del CN Generator Framework.
 *
 * Registra toda la infraestructura necesaria para que
 * el generador pueda resolverse automáticamente mediante
 * el Service Container de Laravel.
 *
 * @package App\Providers
 * @since 1.0.0
 */
final class CNGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios del CN Generator.
     */
    public function register(): void
    {
        $this->registerGeneratorRegistry();

        $this->registerModuleGenerator();
    }

    /**
     * Inicializa servicios del framework.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Registra el GeneratorRegistry.
     */
    private function registerGeneratorRegistry(): void
    {
        $this->app->singleton(
            GeneratorRegistry::class,
            function ($app): GeneratorRegistry {

                return new GeneratorRegistry([

                    $app->make(ModelGenerator::class),

                    $app->make(MigrationGenerator::class),

                    $app->make(RepositoryGenerator::class),

                    $app->make(ServiceGenerator::class),

                    $app->make(RequestGenerator::class),

                    $app->make(ControllerGenerator::class),

                    $app->make(ViewGenerator::class),

                    $app->make(RouteGenerator::class),

                ]);
            }
        );
    }

    /**
     * Registra el ModuleGenerator.
     */
    private function registerModuleGenerator(): void
    {
        $this->app->singleton(
            ModuleGenerator::class,
            function ($app): ModuleGenerator {

                return new ModuleGenerator(
                    $app
                        ->make(GeneratorRegistry::class)
                        ->all()
                );
            }
        );
    }
}
