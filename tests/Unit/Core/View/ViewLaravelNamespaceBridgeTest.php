<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Loader\ViewDefinitionLoader;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\Registry\ViewDefinitionRegistry;
use App\Core\View\ViewRegistry;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Tests\TestCase;

final class ViewLaravelNamespaceBridgeTest extends TestCase
{
    public function test_module_view_namespace_is_registered_in_laravel(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new ViewDefinitionRegistry();

        $loader = new ViewDefinitionLoader(
            $definitions,
            $modules,
        );

        $registry = new ViewRegistry();

        $views = $this->app->make(ViewFactory::class);

        $registrar = new ViewRegistrar(
            $registry,
            $views,
        );

        $bootstrapper = new ViewBootstrapper(
            $definitions,
            $registrar,
        );

        $module = new ModuleDefinition(
            name: 'Institution',
            namespace: 'App\\Modules\\Institution',
            basePath: base_path('app/Modules/Institution'),
            manifestPath: base_path('app/Modules/Institution/module.php'),
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                \App\Modules\Institution\View\InstitutionView::class,
            ],
            enabled: true,
        );

        $modules->register($module);


        $loader->load();

        $bootstrapper->boot();

        self::assertSame(
            'app/Modules/Institution/resources/views',
            $registry->path('institutions'),
        );

        self::assertSame(
            'institution-view-bridge-ok',
            trim(
                $views
                    ->make('institutions::bridge-test')
                    ->render()
            ),
        );
    }
}
