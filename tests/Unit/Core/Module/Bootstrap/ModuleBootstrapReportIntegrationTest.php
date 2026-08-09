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
use App\Core\Module\Bootstrap\ModuleBootstrapReport;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use RuntimeException;
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


        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $lifecycle,
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

        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
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
                function (ModuleBootstrapContext $context): void {

                    $this->registerModule(
                        $context,
                        'Inventory'
                    );

                    $context->markModuleRegistered();
                }
            );

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $lifecycle,
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

    /**
     * Summary of registerModule
     * @param ModuleBootstrapContext $context
     * @param string $name
     * @return void
     */
    private function registerModule(
        ModuleBootstrapContext $context,
        string $name
    ): void {
        $context->setDefinition(
            new ModuleDefinition(
                $name,
                "Modules\\{$name}",
                "/modules/{$name}",
                "/modules/{$name}/module.php",
                [],
                true
            )
        );

        $context->markModuleRegistered();
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
                function (ModuleBootstrapContext $context): void {
                    $context->markSkipped();

                    self::assertTrue(
                        $context->isSkipped()
                    );
                }
            );


        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $lifecycle,
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


        $exception = new RuntimeException(
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


        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $lifecycle,
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

                    if ($context->manifestPath() === '/modules/Inventory/module.php') {

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

                        $context->markModuleRegistered();

                        return;
                    }

                    if ($context->manifestPath() === '/modules/Blog/module.php') {
                        $context->markSkipped();
                        return;
                    }

                    $context->setException(
                        new RuntimeException('Broken module')
                    );
                }
            );


        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // no-op
                }
            }
        );

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $lifecycle,
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
