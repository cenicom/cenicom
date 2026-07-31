<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\TestCase;

final class ModuleBootstrapMetricsIntegrationTest extends TestCase
{
    public function test_bootstrap_returns_report_with_metrics(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $finder
            ->method('find')
            ->willReturn([]);


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertNotNull(
            $report->metrics()
        );
    }


    public function test_metrics_count_registered_modules(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $finder
            ->method('find')
            ->willReturn([
                '/modules/Inventory/module.php',
                '/modules/Finance/module.php',
            ]);


        $pipeline
            ->expects($this->exactly(2))
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    $context->setDefinition(
                        new ModuleDefinition(
                            'TestModule',
                            'Modules\\TestModule',
                            '/modules/TestModule',
                            $context->manifestPath(),
                            [],
                            true
                        )
                    );
                }
            );


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertSame(
            2,
            $report->metrics()->registered()
        );
    }


    public function test_metrics_count_skipped_modules(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $finder
            ->method('find')
            ->willReturn([
                '/modules/Disabled/module.php',
            ]);


        $pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    $context->markSkipped();
                }
            );


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertSame(
            1,
            $report->metrics()->skipped()
        );
    }


    public function test_metrics_count_failed_modules(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $finder
            ->method('find')
            ->willReturn([
                '/modules/Broken/module.php',
            ]);


        $pipeline
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


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertSame(
            1,
            $report->metrics()->failed()
        );
    }


    public function test_metrics_calculates_execution_duration(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $finder
            ->method('find')
            ->willReturn([]);


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertNotNull(
            $report->metrics()->duration()
        );
    }
}
