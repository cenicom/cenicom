<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\NullModuleBootstrapReporter;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Events\ModuleBooted;
use App\Core\Module\Events\ModuleBooting;
use App\Core\Module\Events\ModuleDiscovered;
use App\Core\Module\Events\ModuleFailed;
use App\Core\Module\Events\ModuleRegistered;
use App\Core\Module\Events\ModuleRunning;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use App\Core\Module\Lifecycle\ModuleState;
use App\Core\Module\Registry\ModuleRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class ModuleBootstrapLifecycleTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $manifestFinder;

    private ModuleBootstrapPipelineInterface&MockObject $pipeline;

    private ModuleBootstrap $bootstrap;

    private ModuleRegistryInterface $registry;

    private ModuleProviderRegistrarInterface&MockObject $providerRegistrar;

    private ModuleLifecycleManager $lifecycle;

    /*Agrega una propiedad para capturar los eventos:*/
    private array $lifecycleEvents = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->manifestFinder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );

        $this->registry = new ModuleRegistry();

        $this->providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $this->lifecycleEvents = [];

        $this->lifecycle = new ModuleLifecycleManager(
            new class($this->lifecycleEvents) implements EventDispatcherInterface {
                public function __construct(
                    private array &$events,
                ) {}

                public function dispatch(object $event): void
                {
                    $this->events[] = $event::class;
                }
            }
        );

        $this->bootstrap = new ModuleBootstrap(
            $this->manifestFinder,
            $this->pipeline,
            $this->lifecycle,
            new NullModuleBootstrapReporter(),
        );
    }

    public function test_bootstrap_processes_single_manifest(): void
    {
        $manifest = '/tmp/TestModule/module.php';

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->callback(function (ModuleBootstrapContext $context) use ($manifest): bool {
                    return $context->manifestPath() === $manifest;
                })
            );

        $this->bootstrap->bootstrap();
    }

    public function test_bootstrap_processes_multiple_manifests(): void
    {
        $manifests = [
            '/tmp/ModuleA/module.php',
            '/tmp/ModuleB/module.php',
            '/tmp/ModuleC/module.php',
        ];

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $received = [];

        $this->pipeline
            ->expects($this->exactly(3))
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use (&$received): void {
                    $received[] = $context->manifestPath();
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertSame($manifests, $received);
    }

    public function test_bootstrap_creates_one_context_per_manifest(): void
    {
        $manifests = [
            '/tmp/A/module.php',
            '/tmp/B/module.php',
        ];

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $contexts = [];

        $this->pipeline
            ->expects($this->exactly(2))
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use (&$contexts): void {
                    $contexts[] = $context;
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertCount(2, $contexts);

        $this->assertNotSame(
            $contexts[0],
            $contexts[1]
        );

        $this->assertSame(
            '/tmp/A/module.php',
            $contexts[0]->manifestPath()
        );

        $this->assertSame(
            '/tmp/B/module.php',
            $contexts[1]->manifestPath()
        );
    }

    public function test_bootstrap_handles_empty_manifest_list(): void
    {
        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([]);

        $this->pipeline
            ->expects($this->never())
            ->method('process');

        $this->bootstrap->bootstrap();

        $this->assertTrue(true);
    }

    public function test_bootstrap_is_idempotent(): void
    {
        $manifests = [
            '/tmp/TestModule/module.php',
        ];

        $this->manifestFinder
            ->expects($this->exactly(2))
            ->method('find')
            ->willReturn($manifests);

        $this->pipeline
            ->expects($this->exactly(2))
            ->method('process');

        $this->bootstrap->bootstrap();

        $this->bootstrap->bootstrap();
    }

    public function test_bootstrap_passes_manifest_path_to_context(): void
    {
        $manifest = '/tmp/Inventory/module.php';

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->with(
                $this->callback(function (ModuleBootstrapContext $context) use ($manifest): bool {
                    return $context->manifestPath() === $manifest
                        && ! $context->hasDefinition()
                        && ! $context->hasException();
                })
            );

        $this->bootstrap->bootstrap();
    }

    public function test_registry_is_idempotent_when_registering_same_module_twice(): void
    {
        $registry = new ModuleRegistry();

        $definition = new ModuleDefinition(
            name: 'TestModule',
            namespace: 'Tests\\Fixtures\\Bootstrap',
            basePath: '/tmp/TestModule',
            manifestPath: '/tmp/TestModule/module.php',
            providers: [],
            enabled: true,
        );

        $registry->register($definition);
        $registry->register($definition);

        $this->assertCount(
            1,
            $registry->all()
        );

        $this->assertTrue(
            $registry->has('TestModule')
        );
    }

    /*Agrega prueba de lifecycle exitoso*/
    public function test_successful_bootstrap_follows_complete_lifecycle(): void
    {
        $manifest = '/tmp/TestModule/module.php';

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use ($manifest): void {
                    $context->setDefinition(
                        new ModuleDefinition(
                            name: 'TestModule',
                            namespace: 'Tests\\Fixtures\\Bootstrap',
                            basePath: '/tmp/TestModule',
                            manifestPath: $manifest,
                            providers: [],
                            enabled: true,
                        )
                    );

                    $context->markModuleRegistered();
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertSame(
            ModuleDiscovered::class,
            $this->lifecycleEvents[0]
        );

        $this->assertSame(
            ModuleRegistered::class,
            $this->lifecycleEvents[1]
        );

        $this->assertSame(
            ModuleBooting::class,
            $this->lifecycleEvents[2]
        );

        $this->assertSame(
            ModuleBooted::class,
            $this->lifecycleEvents[3]
        );

        $this->assertSame(
           ModuleRunning::class,
            $this->lifecycleEvents[4]
        );

        $this->assertSame(
            ModuleState::RUNNING,
            $this->lifecycle->state('TestModule')
        );
    }

    /*Agrega la prueba de failure*/
    public function test_failed_bootstrap_follows_failure_lifecycle(): void
    {
        $manifest = '/tmp/TestModule/module.php';

        $exception = new RuntimeException(
            'Bootstrap failed.'
        );

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use (
                    $manifest,
                    $exception
                ): void {
                    $context->setDefinition(
                        new ModuleDefinition(
                            name: 'TestModule',
                            namespace: 'Tests\\Fixtures\\Bootstrap',
                            basePath: '/tmp/TestModule',
                            manifestPath: $manifest,
                            providers: [],
                            enabled: true,
                        )
                    );

                    $context->markModuleRegistered();

                    $context->setException($exception);
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertSame(
            [
                ModuleDiscovered::class,
                ModuleRegistered::class,
                ModuleBooting::class,
                ModuleFailed::class,
            ],
            $this->lifecycleEvents
        );

        $this->assertSame(
            ModuleState::FAILED,
            $this->lifecycle->state('TestModule')
        );
    }

    /* Agrega la prueba de skipped
     * Aquí respetamos el contrato actual del Lifecycle:
     * DISCOVERED
     */
    public function test_skipped_bootstrap_stops_lifecycle_before_registration(): void
    {
        $manifest = '/tmp/DisabledModule/module.php';

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use ($manifest): void {
                    $context->setDefinition(
                        new ModuleDefinition(
                            name: 'DisabledModule',
                            namespace: 'Tests\\Fixtures\\Bootstrap',
                            basePath: '/tmp/DisabledModule',
                            manifestPath: $manifest,
                            providers: [],
                            enabled: false,
                        )
                    );

                    $context->markSkipped();
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertSame(
            [
                ModuleDiscovered::class,
            ],
            $this->lifecycleEvents
        );

        $this->assertSame(
            ModuleState::DISCOVERED,
            $this->lifecycle->state('DisabledModule')
        );
    }
}
