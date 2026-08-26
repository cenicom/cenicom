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
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\View\InstitutionTestViewDefinition;
use Tests\Fixtures\View\TestViewDefinition;

final class ViewModuleResolutionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Container::setInstance(
            new Container()
        );
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_module_view_is_resolved_through_laravel_view_api(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new ViewDefinitionRegistry();

        $loader = new ViewDefinitionLoader(
            $definitions,
            $modules,
        );

        $registry = new ViewRegistry();

        $filesystem = new Filesystem();

        $finder = new FileViewFinder(
            $filesystem,
            [],
            ['blade.php', 'php'],
        );

        $engines = new EngineResolver();

        $blade = new BladeCompiler(
            $filesystem,
            sys_get_temp_dir() . '/cenicom-blade-tests',
        );

        $engines->register(
            'blade',
            fn() => new CompilerEngine($blade),
        );

        $views = new Factory(
            $engines,
            $finder,
            new Dispatcher(),
        );

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
            basePath: dirname(__DIR__, 4) . '/app/Modules/Institution',
            manifestPath: dirname(__DIR__, 4) . '/app/Modules/Institution/module.php',
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                InstitutionTestViewDefinition::class,
            ],
            enabled: true,
        );

        $modules->register($module);

        Container::getInstance()->bind(
            TestViewDefinition::class,
            fn() => new TestViewDefinition(),
        );

        $loader->load();

        $bootstrapper->boot();

        self::assertSame(
            [
                'institution' => [
                    dirname(__DIR__, 4) . '/app/Modules/Institution/resources/views',
                ],
            ],
            $finder->getHints(),
        );

        $rendered = $views
            ->make('institution::bridge-test')
            ->render();

        self::assertSame(
            'institution-view-bridge-ok',
            trim($rendered),
        );

        viewDefinitions:
        [
            InstitutionTestViewDefinition::class,
        ];

        Container::getInstance()->bind(
            InstitutionTestViewDefinition::class,
            fn() => new InstitutionTestViewDefinition(),
        );
    }
}
