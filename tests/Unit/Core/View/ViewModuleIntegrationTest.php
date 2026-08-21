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
use Tests\Fixtures\View\TestViewDefinition;
use PHPUnit\Framework\TestCase;

final class ViewModuleIntegrationTest extends TestCase
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

    public function test_module_view_definition_is_registered_in_view_registry(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new ViewDefinitionRegistry();

        $loader = new ViewDefinitionLoader(
            $definitions,
            $modules,
        );

        $registry = new ViewRegistry();

        $registrar = new ViewRegistrar(
            $registry
        );

        $bootstrapper = new ViewBootstrapper(
            $definitions,
            $registrar,
        );

        $module = new ModuleDefinition(
            name: 'Test',
            namespace: 'Tests\\Fixtures\\Modules\\Test',
            basePath: __DIR__,
            manifestPath: __FILE__,
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                TestViewDefinition::class,
            ],
            enabled: true,
        );

        $modules->register($module);

        Container::getInstance()->bind(
            TestViewDefinition::class,
            fn() => new TestViewDefinition()
        );

        $loader->load();

        $bootstrapper->boot();

        $expectedPath = realpath(
            __DIR__ . '/../../Fixtures/View/views'
        );

        $actualPath = realpath(
            $registry->path('tests')
        );

        self::assertSame(
            $expectedPath,
            $actualPath
        );
    }
}
