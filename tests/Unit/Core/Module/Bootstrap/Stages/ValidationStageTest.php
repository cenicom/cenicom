<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap\Stages;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\Stages\ValidationStage;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\TestCase;

final class ValidationStageTest extends TestCase
{
    private ValidationStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stage = new ValidationStage();
    }

    //Primera prueba VS-001
    public function test_allows_enabled_module(): void
    {
        // Arrange

        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [],
            enabled: true,
        );

        $context->setDefinition(
            $definition
        );


        // Act

        $this->stage->process(
            $context
        );


        // Assert

        $this->assertFalse(
            $context->hasException()
        );

        $this->assertSame(
            $definition,
            $context->definition()
        );
    }

    //VS-002 Validar módulo deshabilitado
    public function test_rejects_disabled_module(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [],
            enabled: false,
        );

        $context->setDefinition(
            $definition
        );


        $this->stage->process(
            $context
        );


        $this->assertTrue(
            $context->hasException()
        );

        $this->assertSame(
            'Module "Blog" is disabled.',
            $context->exception()->getMessage()
        );
    }

    //VS-003 Validar ausencia de ModuleDefinition
    public function test_rejects_missing_definition(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $this->stage->process(
            $context
        );

        $this->assertTrue(
            $context->hasException()
        );

        $this->assertSame(
            'Module definition has not been created.',
            $context->exception()->getMessage()
        );
    }

    //VS-004 Blindar excepción previa en contexto
    public function test_does_not_execute_when_context_already_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $exception = new \RuntimeException(
            'Previous failure.'
        );

        $context->setException(
            $exception
        );

        $this->stage->process(
            $context
        );

        $this->assertSame(
            $exception,
            $context->exception()
        );
    }
}
