<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $manifestFinder;

    private ModuleBootstrapPipelineInterface&MockObject $pipeline;

    private ModuleLifecycleManager $lifecycle;

    private ModuleBootstrap $bootstrap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manifestFinder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );

        $this->lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // No-op para pruebas unitarias.
                }
            }
        );

        $this->bootstrap = new ModuleBootstrap(
            $this->manifestFinder,
            $this->pipeline,
            $this->lifecycle,
        );
    }

    public function test_bootstrap_processes_every_discovered_manifest(): void
    {
        $manifests = [
            '/modules/Blog/module.php',
            '/modules/User/module.php',
            '/modules/Inventory/module.php',
        ];

        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn($manifests);

        $contexts = [];

        $this->pipeline
            ->expects($this->exactly(3))
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context) use (&$contexts): void {
                    $contexts[] = $context;

                    $context->setDefinition(
                        new ModuleDefinition(
                            name: basename(
                                dirname($context->manifestPath())
                            ),
                            namespace: 'Modules\\Test',
                            basePath: '/modules/test',
                            manifestPath: $context->manifestPath(),
                            providers: [],
                            enabled: true,
                        )
                    );

                    $context->markModuleRegistered();
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertCount(3, $contexts);

        $this->assertSame(
            '/modules/Blog/module.php',
            $contexts[0]->manifestPath()
        );

        $this->assertSame(
            '/modules/User/module.php',
            $contexts[1]->manifestPath()
        );

        $this->assertSame(
            '/modules/Inventory/module.php',
            $contexts[2]->manifestPath()
        );
    }

    public function test_bootstrap_processes_registered_module(): void
    {
        $this->manifestFinder
            ->expects($this->once())
            ->method('find')
            ->willReturn([
                '/modules/Blog/module.php',
            ]);

        $context = null;

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $bootstrapContext) use (&$context): void {
                    $context = $bootstrapContext;

                    $bootstrapContext->setDefinition(
                        new ModuleDefinition(
                            name: 'Blog',
                            namespace: 'Modules\\Blog',
                            basePath: '/modules/Blog',
                            manifestPath: $bootstrapContext->manifestPath(),
                            providers: [],
                            enabled: true,
                        )
                    );

                    $bootstrapContext->markModuleRegistered();
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertInstanceOf(
            ModuleBootstrapContext::class,
            $context
        );

        $this->assertTrue(
            $context->isModuleRegistered()
        );

        $this->assertSame(
            'Blog',
            $context->definition()?->name
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
            '/modules/Blog/module.php',
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
}
