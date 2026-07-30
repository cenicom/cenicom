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

final class ModuleBootstrapLifecycleTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $manifestFinder;

    private ModuleBootstrapPipelineInterface&MockObject $pipeline;

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

        $this->bootstrap = new ModuleBootstrap(
            $this->manifestFinder,
            $this->pipeline,
        );
    }

    public function test_bootstrap_processes_single_manifest(): void
    {
        $manifest = '/tmp/TestModule/module.json';

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
            '/tmp/ModuleA/module.json',
            '/tmp/ModuleB/module.json',
            '/tmp/ModuleC/module.json',
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
            '/tmp/A/module.json',
            '/tmp/B/module.json',
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
            '/tmp/A/module.json',
            $contexts[0]->manifestPath()
        );

        $this->assertSame(
            '/tmp/B/module.json',
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
            '/tmp/TestModule/module.json',
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
        $manifest = '/tmp/Inventory/module.json';

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
            manifestPath: '/tmp/TestModule/module.json',
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
}
