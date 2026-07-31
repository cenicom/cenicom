<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

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
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class ModuleBootstrapIntegrationTest extends TestCase
{
    private ModuleManifestFinderInterface&MockObject $finder;

    private ModuleRegistryInterface&MockObject $registry;

    private ModuleProviderRegistrarInterface&MockObject $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $this->registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $this->registrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
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

        $this->registrar
            ->expects($this->once())
            ->method('registerDefinition');

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                new ModuleDefinitionFactory()
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $this->registry
            ),
            new RegisterProvidersStage(
                $this->registrar
            ),
        ]);

        $bootstrap = new ModuleBootstrap(
            $this->finder,
            $pipeline
        );

        $bootstrap->bootstrap();
    }

    //🚢 ERP-INT-004.3.11.5 — Bootstrap Integration Hardening
    //MBINT-002 — Certificación de módulo deshabilitado
    public function test_bootstrap_skips_disabled_module(): void
    {
        $manifest = base_path(
            'tests/Fixtures/ModuleDefinitionFactory/EnabledFalse/module.php'
        );

        $context = null;

        $finder = $this->createMock(
            ModuleManifestFinderInterface::class
        );

        $registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $registrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                app(ModuleDefinitionFactoryInterface::class)
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $registry
            ),
            new RegisterProvidersStage(
                $registrar
            ),
        ]);

        $finder
            ->expects($this->once())
            ->method('find')
            ->willReturn([
                $manifest,
            ]);

        $registry
            ->expects($this->never())
            ->method('register');

        $registrar
            ->expects($this->never())
            ->method('registerDefinition');

        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );

        $bootstrap->bootstrap();
    }

    //🚢 ERP-INT-004.3.11.6 — MBINT-003
    //Certificación de fallo controlado de Bootstrap
    //Objetivo:
    //Validar que un módulo con manifiesto inválido:
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
            ->willReturn([
                $manifest,
            ]);

        $registry
            ->expects($this->never())
            ->method('register');

        $registrar
            ->expects($this->never())
            ->method('registerDefinition');


        $pipeline = new ModuleBootstrapPipeline([
            new CreateDefinitionStage(
                app(ModuleDefinitionFactoryInterface::class)
            ),
            new ValidationStage(),
            new RegisterModuleStage(
                $registry
            ),
            new RegisterProvidersStage(
                $registrar
            ),
        ]);


        $bootstrap = new ModuleBootstrap(
            $finder,
            $pipeline
        );


        $this->expectException(\UnexpectedValueException::class);

        $this->expectExceptionMessage(
            'Module manifest must return an array.'
        );


        $bootstrap->bootstrap();
    }


}
