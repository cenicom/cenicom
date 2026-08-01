<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap\Stages;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\Stages\ValidationStage;
use App\Core\Module\DTO\ModuleDefinition;
use RuntimeException;
use Tests\TestCase;

final class ValidationStageTest extends TestCase
{
    private ValidationStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stage = new ValidationStage();
    }

    public function test_allows_enabled_module(): void
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
            enabled: true,
        );

        $context->setDefinition($definition);

        $this->stage->process($context);

        $this->assertFalse(
            $context->hasException()
        );

        $this->assertFalse(
            $context->isSkipped()
        );

        $this->assertSame(
            $definition,
            $context->definition()
        );
    }

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

        $context->setDefinition($definition);

        $this->stage->process($context);

        $this->assertTrue(
            $context->isSkipped()
        );

        $this->assertFalse(
            $context->hasException()
        );

        $this->assertNull(
            $context->exception()
        );
    }

    public function test_rejects_missing_definition(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $this->stage->process($context);

        $this->assertTrue(
            $context->hasException()
        );

        $this->assertSame(
            'Module definition has not been created.',
            $context->exception()?->getMessage()
        );
    }

    public function test_does_not_execute_when_context_already_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $exception = new RuntimeException(
            'Previous failure.'
        );

        $context->setException($exception);

        $this->stage->process($context);

        $this->assertSame(
            $exception,
            $context->exception()
        );
    }
}
