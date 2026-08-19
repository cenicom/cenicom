<?php

namespace App\Providers;

use App\Core\Generator\Pipeline\ExecuteGeneratorsStep;
use App\Core\Generator\Pipeline\Steps\ValidateModuleStep;
use App\Core\Generator\Presentation\Contracts\PresentationRendererInterface;
use App\Core\Generator\Presentation\Renderers\BladePresentationRenderer;
use App\Core\Generator\Security\MiddlewareRegistry;
use App\Core\Generator\Specifications\Validators\SpecificationValidator;
use App\Core\Generator\Support\Contracts\FileWriterInterface;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Validation\GeneratorValidator;
use App\Core\Generator\Validation\Validators\FieldsValidator;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\Services\NavigationService;
use App\Support\Navigation\NavigationManager;
use App\Core\Generator\Pipeline\FinalizePipelineStep;
use App\Core\Generator\Pipeline\Pipeline;
use App\Core\Generator\Pipeline\RegisterNavigationStep;
use App\Core\Generator\Pipeline\RegisterPermissionsStep;
use App\Core\Generator\Pipeline\Steps\PrepareDirectoriesStep;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(NavigationManager::class, function () {
            return new NavigationManager();
        });

        $this->app->bind(
            PresentationRendererInterface::class,
            BladePresentationRenderer::class
        );

        $this->app->singleton(GeneratorValidator::class, function ($app) {
            return new GeneratorValidator([
                $app->make(SpecificationValidator::class),
                $app->make(FieldsValidator::class),
            ]);
        });

        $this->app->singleton(
            MiddlewareRegistry::class,
            function () {
                return new MiddlewareRegistry();
            }
        );

        $this->app->bind(
            NavigationServiceInterface::class,
            NavigationService::class
        );

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

        $this->app->singleton(
            Pipeline::class,
            function ($app): Pipeline {
                return new Pipeline(
                    iterator_to_array(
                        $app->tagged('cn.generator.pipeline.steps')
                    )
                );
            }
        );

        $this->app->bind(
            FileWriterInterface::class,
            FileWriter::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        View::composer('*', function ($view) {

            $nav = app(NavigationManager::class);

            $view->with(
                'legacyNavigation',
                $nav->grouped()
            );
        });
    }
}
