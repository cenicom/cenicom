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
use Tests\Fixtures\View\Isolation\InstitutionViewDefinition;
use Tests\Fixtures\View\Isolation\InventoryViewDefinition;

final class ViewModuleIsolationTest extends TestCase
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

    public function test_enabled_modules_resolve_only_their_own_view_namespaces(): void
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
            sys_get_temp_dir() . '/cenicom-blade-isolation-tests',
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

        $institution = new ModuleDefinition(
            name: 'Institution',
            namespace: 'App\\Modules\\Institution',
            basePath: dirname(__DIR__, 3) . '/app/Modules/Institution',
            manifestPath: dirname(__DIR__, 3) . '/app/Modules/Institution/module.php',
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                InstitutionViewDefinition::class,
            ],
            enabled: true,
        );

        $inventory = new ModuleDefinition(
            name: 'Inventory',
            namespace: 'App\\Modules\\Inventory',
            basePath: dirname(__DIR__, 3) . '/app/Modules/Inventory',
            manifestPath: dirname(__DIR__, 3) . '/app/Modules/Inventory/module.php',
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                InventoryViewDefinition::class,
            ],
            enabled: true,
        );

        $modules->register($institution);
        $modules->register($inventory);

        Container::getInstance()->bind(
            InstitutionViewDefinition::class,
            fn() => new InstitutionViewDefinition(),
        );

        Container::getInstance()->bind(
            InventoryViewDefinition::class,
            fn() => new InventoryViewDefinition(),
        );

        $loader->load();

        $bootstrapper->boot();

        $hints = $finder->getHints();

        self::assertSame(
            realpath(
                dirname(__DIR__, 3)
                    . '/Fixtures/View/Isolation/Institution'
            ),
            realpath($hints['institution'][0]),
        );

        self::assertSame(
            realpath(
                dirname(__DIR__, 3)
                    . '/Fixtures/View/Isolation/Inventory'
            ),
            realpath($hints['inventory'][0]),
        );

        self::assertSame(
            ['institution', 'inventory'],
            array_keys($hints),
        );

        self::assertSame(
            'institution-isolated-ok',
            trim(
                $views
                    ->make('institution::index')
                    ->render()
            ),
        );

        self::assertSame(
            'inventory-isolated-ok',
            trim(
                $views
                    ->make('inventory::index')
                    ->render()
            ),
        );
    }

    public function test_disabled_module_does_not_register_its_laravel_namespace(): void
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
            sys_get_temp_dir() . '/cenicom-blade-isolation-disabled-tests',
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

        $modules->register(
            new ModuleDefinition(
                name: 'Inventory',
                namespace: 'App\\Modules\\Inventory',
                basePath: dirname(__DIR__, 3) . '/app/Modules/Inventory',
                manifestPath: dirname(__DIR__, 3) . '/app/Modules/Inventory/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [],
                viewDefinitions: [
                    InventoryViewDefinition::class,
                ],
                enabled: false,
            )
        );

        Container::getInstance()->bind(
            InventoryViewDefinition::class,
            fn() => new InventoryViewDefinition(),
        );

        $loader->load();

        $bootstrapper->boot();

        self::assertSame(
            [],
            $finder->getHints(),
        );

        self::assertSame(
            [],
            $registry->all(),
        );
    }
}
