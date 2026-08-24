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
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use App\Core\Module\Registry\ModuleRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapMetricsIntegrationTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $finder;

    private ModuleBootstrapPipelineInterface&MockObject $pipeline;

    private ModuleRegistryInterface $registry;

    private ModuleProviderRegistrarInterface&MockObject $providerRegistrar;

    private ModuleLifecycleManager $lifecycle;

    private ModuleBootstrap $bootstrap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );

        $this->registry = new ModuleRegistry();

        $this->providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $this->lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $this->bootstrap = new ModuleBootstrap(
            $this->finder,
            $this->pipeline,
            $this->lifecycle,
            new NullModuleBootstrapReporter(),
        );
    }

    public function test_bootstrap_returns_report_with_metrics(): void
    {
        $this->finder
            ->method('find')
            ->willReturn([]);

        $report = $this->bootstrap->bootstrap();

        self::assertNotNull(
            $report->metrics()
        );
    }


    public function test_metrics_count_registered_modules(): void
    {
        $this->finder
            ->method('find')
            ->willReturn([
                '/modules/Inventory/module.php',
                '/modules/Finance/module.php',
            ]);

        $this->pipeline
            ->expects($this->exactly(2))
            ->method('process')
            ->willReturnCallback(
                function (ModuleBootstrapContext $context): void {
                    $name = basename(dirname($context->manifestPath()));

                    $context->setDefinition(
                        new ModuleDefinition(
                            $name,
                            "Modules\\{$name}",
                            "/modules/{$name}",
                            $context->manifestPath(),
                            providers: [],
                            permissionDefinitions: [],
                            navigationDefinitions: [],
                            crudDefinitions: [],
                            viewDefinitions: [],
                            enabled: true
                        )
                    );

                    // Simula RegisterModuleStage
                    $context->markModuleRegistered();
                }
            );


        $report = $this->bootstrap->bootstrap();

        self::assertSame(
            2,
            $report->metrics()->registered()
        );
    }


    public function test_metrics_count_skipped_modules(): void
    {
        $this->finder
            ->method('find')
            ->willReturn([
                '/modules/Disabled/module.php',
            ]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    $context->markSkipped();
                }
            );

        $report = $this->bootstrap->bootstrap();

        self::assertSame(
            1,
            $report->metrics()->skipped()
        );
    }


    public function test_metrics_count_failed_modules(): void
    {
        $this->finder
            ->method('find')
            ->willReturn([
                '/modules/Broken/module.php',
            ]);

        $this->pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    $context->setException(
                        new \RuntimeException(
                            'Bootstrap failure'
                        )
                    );
                }
            );

        $report = $this->bootstrap->bootstrap();


        self::assertSame(
            1,
            $report->metrics()->failed()
        );
    }


    public function test_metrics_calculates_execution_duration(): void
    {
        $this->finder
            ->method('find')
            ->willReturn([]);

        $report = $this->bootstrap->bootstrap();


        self::assertNotNull(
            $report->metrics()->duration()
        );
    }
}
