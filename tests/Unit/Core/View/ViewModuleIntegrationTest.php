<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\View\Bootstrap\ViewBootstrapper;
use App\Core\View\Contracts\ViewPathResolverInterface;
use App\Core\View\Loader\ViewDefinitionLoader;
use App\Core\View\Registrar\ViewRegistrar;
use App\Core\View\Registry\ViewDefinitionRegistry;
use App\Core\View\ViewRegistry;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\View\TestViewDefinition;

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

        $finder = new FileViewFinder(
            new Filesystem(),
            [],
            ['blade.php', 'php'],
        );

        $resolver = $this->createMock(
            ViewPathResolverInterface::class,
        );

        $expectedPath = realpath(
            __DIR__ . '/../../../Fixtures/View'
        );

        self::assertNotFalse($expectedPath);

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->anything())
            ->willReturn($expectedPath);

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
            $resolver,
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

        self::assertSame(
            dirname((new \ReflectionClass(TestViewDefinition::class))->getFileName())
                . '/views',
            $registry->path('tests'),
        );
    }

    public function test_disabled_module_view_definition_is_not_registered(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new ViewDefinitionRegistry();

        $loader = new ViewDefinitionLoader(
            $definitions,
            $modules,
        );

        $registry = new ViewRegistry();

        $finder = new FileViewFinder(
            new Filesystem(),
            [],
            ['blade.php', 'php'],
        );

        $resolver = $this->createMock(
            ViewPathResolverInterface::class,
        );

        $resolver
            ->expects($this->never())
            ->method('resolve');

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
            $resolver,
        );

        $bootstrapper = new ViewBootstrapper(
            $definitions,
            $registrar,
        );

        $module = new ModuleDefinition(
            name: 'Disabled',
            namespace: 'Tests\\Fixtures\\Modules\\Disabled',
            basePath: __DIR__,
            manifestPath: __FILE__,
            providers: [],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [
                TestViewDefinition::class,
            ],
            enabled: false,
        );

        $modules->register($module);

        Container::getInstance()->bind(
            TestViewDefinition::class,
            fn() => new TestViewDefinition()
        );

        $loader->load();

        $bootstrapper->boot();

        self::assertSame(
            [],
            $registry->all(),
        );
    }

    public function test_multiple_modules_register_their_view_definitions_independently(): void
    {
        $modules = new ModuleRegistry();

        $definitions = new ViewDefinitionRegistry();

        $loader = new ViewDefinitionLoader(
            $definitions,
            $modules,
        );

        $registry = new ViewRegistry();

        $finder = new FileViewFinder(
            new Filesystem(),
            [],
            ['blade.php', 'php'],
        );

        $resolver = $this->createMock(
            ViewPathResolverInterface::class,
        );

        $expectedPath = realpath(
            __DIR__ . '/../../../Fixtures/View'
        );

        self::assertNotFalse($expectedPath);

        $resolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->willReturn($expectedPath);

        $registrar = new ViewRegistrar(
            $registry,
            $finder,
            $resolver,
        );

        $bootstrapper = new ViewBootstrapper(
            $definitions,
            $registrar,
        );

        $secondDefinition = new class implements \App\Core\View\Contracts\ViewDefinitionInterface {
            public function register(
                \App\Core\View\Contracts\ViewRegistrarInterface $views
            ): void {
                $views->register(
                    'second',
                    __DIR__ . '/../../Fixtures/View',
                );
            }
        };

        $modules->register(
            new ModuleDefinition(
                name: 'First',
                namespace: 'Tests\\Fixtures\\Modules\\First',
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
            )
        );

        $modules->register(
            new ModuleDefinition(
                name: 'Second',
                namespace: 'Tests\\Fixtures\\Modules\\Second',
                basePath: __DIR__,
                manifestPath: __FILE__,
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [],
                viewDefinitions: [
                    $secondDefinition::class,
                ],
                enabled: true,
            )
        );

        Container::getInstance()->bind(
            TestViewDefinition::class,
            fn() => new TestViewDefinition()
        );

        Container::getInstance()->bind(
            $secondDefinition::class,
            fn() => $secondDefinition
        );

        $loader->load();

        $bootstrapper->boot();

        $expectedSecondPath = __DIR__ . '/../../Fixtures/View';

        self::assertSame(
            $expectedSecondPath,
            $registry->path('second'),
        );

        self::assertCount(
            2,
            $registry->all(),
        );
    }
}
