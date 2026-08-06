<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $manifestFinder;

    private ModuleBootstrapPipelineInterface&MockObject $pipeline;

    private ModuleBootstrap $bootstrap;

    private ModuleRegistryInterface $registry;

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

        $this->bootstrap = new ModuleBootstrap(
            $this->manifestFinder,
            $this->pipeline,
            $this->registry,
        );
    }

    public function test_bootstrap_processes_every_discovered_manifest(): void
    {
        $manifests = [
            '/modules/Blog/module.json',
            '/modules/User/module.json',
            '/modules/Inventory/module.json',
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
                }
            );

        $this->bootstrap->bootstrap();


        $this->assertCount(3, $contexts);

        $this->assertSame(
            '/modules/Blog/module.json',
            $contexts[0]->manifestPath()
        );

        $this->assertSame(
            '/modules/User/module.json',
            $contexts[1]->manifestPath()
        );

        $this->assertSame(
            '/modules/Inventory/module.json',
            $contexts[2]->manifestPath()
        );
    }

    public function test_bootstrap_registers_modules(): void
    {
        $this->manifestFinder
            ->method('find')
            ->willReturn([
                '/modules/Blog/module.json',
            ]);

        $this->pipeline
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context): void {

                    $context->setDefinition(
                        new ModuleDefinition(
                            name: 'Blog',
                            namespace: 'Modules\\Blog',
                            basePath: '/modules/Blog',
                            manifestPath: $context->manifestPath(),
                            providers: [],
                            enabled: true,
                        )
                    );
                }
            );

        $this->bootstrap->bootstrap();

        $this->assertTrue(
            $this->registry->has('Blog')
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
            '/modules/Blog/module.json',
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
