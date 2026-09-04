<?php

declare(strict_types=1);

namespace App\Providers;



use App\Core\Generator\Contracts\GeneratorManagerInterface;
use App\Core\Generator\GeneratorManager;
use App\Core\Generator\GeneratorRegistry;
use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Generators\BindingGenerator;
use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Generators\FactoryGenerator;
use App\Core\Generator\Generators\FeatureTestGenerator;
use App\Core\Generator\Generators\MiddlewareGenerator;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Generators\ObserverGenerator;
use App\Core\Generator\Generators\PermissionGenerator;
use App\Core\Generator\Generators\PolicyGenerator;
use App\Core\Generator\Generators\RepositoryGenerator;
use App\Core\Generator\Generators\RepositoryInterfaceGenerator;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Generators\RouteGenerator;
use App\Core\Generator\Generators\SeederGenerator;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\ServiceInterfaceGenerator;
use App\Core\Generator\Generators\UnitTestGenerator;
use App\Core\Generator\Generators\ViewGenerator;
use App\Core\Generator\Pipeline\Contracts\PipelineInterface;
use App\Core\Generator\Pipeline\ExecuteGeneratorsStep;
use App\Core\Generator\Pipeline\FinalizePipelineStep;
use App\Core\Generator\Pipeline\Pipeline;
use App\Core\Generator\Pipeline\RegisterNavigationStep;
use App\Core\Generator\Pipeline\RegisterPermissionsStep;
use App\Core\Generator\Pipeline\Steps\PrepareDirectoriesStep;
use App\Core\Generator\Pipeline\Steps\ValidateModuleStep;
use Illuminate\Support\ServiceProvider;
//use MiddlewareGenerator;



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
        $this->app->tag(
            [
                ValidateModuleStep::class,
                PrepareDirectoriesStep::class,
                RegisterPermissionsStep::class,
                RegisterNavigationStep::class,
                ExecuteGeneratorsStep::class,
                FinalizePipelineStep::class,
            ],
            'cn.generator.pipeline.steps'
        );

        $this->registerGeneratorRegistry();

        $this->registerGeneratorManager();

        $this->registerGeneratorPipeline();

        $this->registerModuleGenerator();
    }

    private function registerGeneratorManager(): void
    {
        $this->app->singleton(
            GeneratorManagerInterface::class,
            function ($app): GeneratorManager {
                return new GeneratorManager(
                    $app->make(GeneratorRegistry::class)->all(),
                );
            }
        );
    }

    private function registerGeneratorPipeline(): void
    {
        $this->app->singleton(
            PipelineInterface::class,
            function ($app): PipelineInterface {
                return new Pipeline(
                    iterator_to_array(
                        $app->tagged('cn.generator.pipeline.steps')
                    ),
                );
            }
        );

        $this->app->singleton(
            Pipeline::class,
            function ($app): Pipeline {
                return $app->make(PipelineInterface::class);
            }
        );
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

                    $app->make(ServiceInterfaceGenerator::class),

                    $app->make(ServiceGenerator::class),

                    $app->make(RequestGenerator::class),

                    $app->make(ActionGenerator::class),

                    $app->make(ControllerGenerator::class),

                    $app->make(ViewGenerator::class),


                    $app->make(ModelGenerator::class),

                    $app->make(MigrationGenerator::class),

                    $app->make(RepositoryInterfaceGenerator::class),

                    $app->make(RepositoryGenerator::class),

                    $app->make(RouteGenerator::class),

                    $app->make(FactoryGenerator::class),

                    $app->make(SeederGenerator::class),

                    $app->make(FeatureTestGenerator::class),

                    $app->make(UnitTestGenerator::class),

                    $app->make(PolicyGenerator::class),

                    $app->make(ObserverGenerator::class),

                    $app->make(BindingGenerator::class),

                    $app->make(PermissionGenerator::class),

                    $app->make(MiddlewareGenerator::class),

                ]);
            }
        );
    }

    /**
     * Registra el ModuleGenerator.
     */
    private function registerModuleGenerator(): void
    {
        $this->app->singleton(ModuleGenerator::class, function ($app) {

            return new ModuleGenerator(
                $app->make(Pipeline::class),


            );
        });
    }
}
