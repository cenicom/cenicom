<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\Stages\RegisterModuleStage;
use App\Core\Module\DTO\ModuleDefinition;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class RegisterModuleStageTest extends TestCase
{
    private ModuleRegistryInterface&MockObject $registry;

    private RegisterModuleStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(
            ModuleRegistryInterface::class
        );

        $this->stage = new RegisterModuleStage(
            $this->registry
        );
    }

    public function test_registers_module_definition(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.json',
            providers: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $this->registry
            ->expects($this->once())
            ->method('register')
            ->with($definition);

        $this->stage->process($context);

        $this->assertFalse($context->hasException());
        $this->assertSame($definition, $context->definition());
    }

    public function test_does_not_register_when_context_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.json',
            providers: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $context->setException(
            new RuntimeException('Previous failure.')
        );

        $this->registry
            ->expects($this->never())
            ->method('register');

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertSame($definition, $context->definition());
    }

    public function test_stores_exception_when_definition_is_missing(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $this->registry
            ->expects($this->never())
            ->method('register');

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertNull($context->definition());
    }

    public function test_stores_exception_when_registry_fails(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.json',
            providers: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $exception = new RuntimeException(
            'Registry failure.'
        );

        $this->registry
            ->expects($this->once())
            ->method('register')
            ->with($definition)
            ->willThrowException($exception);

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertSame($exception, $context->exception());
        $this->assertSame($definition, $context->definition());
    }
}
