<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Module\ModuleDefinitionFactoryInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrap;
use App\Core\Module\Bootstrap\ModuleBootstrapPipeline;
use App\Core\Module\Bootstrap\Stages\CreateDefinitionStage;
use App\Core\Module\Bootstrap\Stages\RegisterModuleStage;
use App\Core\Module\Bootstrap\Stages\RegisterProvidersStage;
use App\Core\Module\Bootstrap\Stages\ValidationStage;
use App\Core\Module\Factory\ModuleDefinitionFactory;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapIntegrationTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $finder;

    private ModuleRegistryInterface&MockObject $registry;

    private ModuleProviderRegistrarInterface&MockObject $providerRegistrar;

    private ModuleLifecycleManager $lifecycle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $this->providerRegistrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $this->lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // No-op para pruebas.
                }
            }
        );
    }

    public function test_bootstrap_processes_valid_module(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EmptyProviders/module.php'
        );

        $this->finder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $this->registry
            ->expects($this->once())
            ->method('register');

        $this->providerRegistrar
            ->expects($this->once())
            ->method('registerDefinition');

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                new ModuleDefinitionFactory(),
                $this->lifecycle,
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $this->registry,
                $this->lifecycle,
            ),
            new RegisterProvidersStage(
                $this->providerRegistrar,
            ),
        ]);

        $bootstrap = new ModuleBootstrap(
            $this->finder,
            $pipeline,
            $this->lifecycle,
        );

        $report = $bootstrap->bootstrap();

        $this->assertNotNull($report);
        $this->assertSame(
            1,
            $report->metrics()->registered()
        );
    }

    /**
     * 🚢 ERP-INT-004.3.11.5
     *
     * MBINT-002 — Certificación de módulo deshabilitado.
     */
    public function test_bootstrap_skips_disabled_module(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EnabledFalse/module.php'
        );

        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $registrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $finder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $registry
            ->expects($this->never())
            ->method('register');

        $registrar
            ->expects($this->never())
            ->method('registerDefinition');

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                app(ModuleDefinitionFactoryInterface::class),
                $this->lifecycle,
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $registry,
                $this->lifecycle,
            ),
            new RegisterProvidersStage(
                $registrar,
            ),
        ]);

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $this->lifecycle,
        );

        $report = $bootstrap->bootstrap();

        $this->assertNotNull($report);
        $this->assertSame(
            1,
            $report->metrics()->skipped()
        );
    }

    /**
     * 🚢 ERP-INT-004.3.11.6
     *
     * MBINT-003 — Certificación de fallo controlado de Bootstrap.
     */
    public function test_bootstrap_fails_when_definition_creation_fails(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/InvalidReturn/module.php'
        );

        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $registrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $finder
            ->expects($this->once())
            ->method('find')
            ->willReturn([$manifest]);

        $registry
            ->expects($this->never())
            ->method('register');

        $registrar
            ->expects($this->never())
            ->method('registerDefinition');

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                app(ModuleDefinitionFactoryInterface::class),
                $this->lifecycle,
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $registry,
                $this->lifecycle,
            ),
            new RegisterProvidersStage(
                $registrar,
            ),
        ]);

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline,
            $this->lifecycle,
        );

        $report = $bootstrap->bootstrap();

        $this->assertNotNull($report);
        $this->assertSame(
            1,
            $report->metrics()->failed()
        );
    }
}
