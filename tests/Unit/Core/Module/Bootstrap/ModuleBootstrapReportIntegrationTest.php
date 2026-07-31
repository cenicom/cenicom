<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapReport;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\TestCase;

final class ModuleBootstrapReportIntegrationTest extends TestCase
{
    public function test_bootstrap_returns_report(): void
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


        self::assertInstanceOf(
            ModuleBootstrapReport::class,
            $report
        );
    }


    public function test_report_contains_registered_module(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $manifest = '/modules/Inventory/module.php';


        $finder
            ->method('find')
            ->willReturn([
                $manifest,
            ]);


        $pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    $context->setDefinition(
                        new ModuleDefinition(
                            'Inventory',
                            'Modules\\Inventory',
                            '/modules/Inventory',
                            '/modules/Inventory/module.php',
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


        self::assertCount(
            1,
            $report->registered()
        );


        self::assertSame(
            'Inventory',
            $report->registered()[0]['module']
        );
    }


    public function test_report_contains_skipped_module(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $manifest = '/modules/Disabled/module.php';


        $finder
            ->method('find')
            ->willReturn([
                $manifest,
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


        self::assertCount(
            1,
            $report->skipped()
        );


        self::assertSame(
            $manifest,
            $report->skipped()[0]['module']
        );
    }


    public function test_report_contains_failed_module(): void
    {
        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $pipeline = $this->createMock(
            ModuleBootstrapPipelineInterface::class
        );


        $manifest = '/modules/Broken/module.php';


        $finder
            ->method('find')
            ->willReturn([
                $manifest,
            ]);


        $exception = new \RuntimeException(
            'Definition creation failed'
        );


        $pipeline
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ) use ($exception): void {

                    $context->setException(
                        $exception
                    );
                }
            );


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertCount(
            1,
            $report->failed()
        );


        self::assertSame(
            $exception,
            $report->failed()[0]['exception']
        );
    }


    public function test_report_handles_multiple_modules(): void
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
                '/modules/Blog/module.php',
                '/modules/Broken/module.php',
            ]);


        $pipeline
            ->expects($this->exactly(3))
            ->method('process')
            ->willReturnCallback(
                function (
                    ModuleBootstrapContext $context
                ): void {

                    match ($context->manifestPath()) {

                        '/modules/Inventory/module.php'
                        => $context->setDefinition(
                            new \App\Core\Module\DTO\ModuleDefinition(
                                'Inventory',
                                'Modules\\Inventory',
                                '/modules/Inventory',
                                '/modules/Inventory/module.php',
                                [],
                                true
                            )
                        ),

                        '/modules/Blog/module.php'
                        => $context->markSkipped(),

                        '/modules/Broken/module.php'
                        => $context->setException(
                            new \RuntimeException(
                                'Broken module'
                            )
                        ),
                    };
                }
            );


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $report = $bootstrap->bootstrap();


        self::assertCount(
            1,
            $report->registered()
        );

        self::assertCount(
            1,
            $report->skipped()
        );

        self::assertCount(
            1,
            $report->failed()
        );
    }
}
