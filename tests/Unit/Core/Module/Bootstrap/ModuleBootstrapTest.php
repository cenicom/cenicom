<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapTest extends TestCase
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
