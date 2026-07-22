<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Generators\RepositoryGenerator;
use App\Core\Generator\Generators\RepositoryInterfaceGenerator;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\ServiceInterfaceGenerator;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorTestSuite;
use App\Core\Generator\Validation\GeneratorValidator;
use Illuminate\Support\ServiceProvider;


class GeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StubManager::class, function ($app) {
            return new StubManager();
        });

        $this->app->singleton(
            GeneratorValidator::class
        );

        $this->app->singleton(
            GeneratorTestSuite::class
        );

        $this->app->bind(ModuleGenerator::class, function ($app) {

            return new ModuleGenerator(
                [

                    $app->make(MigrationGenerator::class),

                    $app->make(ModelGenerator::class),

                    $app->make(RepositoryInterfaceGenerator::class),

                    $app->make(RepositoryGenerator::class),

                    $app->make(ServiceInterfaceGenerator::class),

                    $app->make(ServiceGenerator::class),

                    $app->make(RequestGenerator::class),

                    $app->make(ControllerGenerator::class),


                ],
                $app->make(GeneratorTestSuite::class),

            );
        });
    }

    public function boot(): void {}
}
