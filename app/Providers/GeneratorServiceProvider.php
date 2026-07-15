<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Core\Generator\ModuleGenerator;

use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Generators\RepositoryGenerator;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Generators\ControllerGenerator;


class GeneratorServiceProvider extends ServiceProvider
{

    public function register(): void
    {

        $this->app->bind(ModuleGenerator::class, function ($app) {

            return new ModuleGenerator([

                $app->make(MigrationGenerator::class),

                $app->make(ModelGenerator::class),

                $app->make(RepositoryGenerator::class),

                $app->make(ServiceGenerator::class),

                $app->make(RequestGenerator::class),

                $app->make(ControllerGenerator::class),

            ]);

        });

    }


    public function boot(): void
    {
    }
}
