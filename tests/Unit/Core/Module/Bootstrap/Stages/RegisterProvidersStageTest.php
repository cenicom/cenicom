<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\Stages\RegisterProvidersStage;
use App\Core\Module\DTO\ModuleDefinition;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class RegisterProvidersStageTest extends TestCase
{
    private ModuleProviderRegistrarInterface&MockObject $registrar;

    private RegisterProvidersStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrar = $this->createMock(
            ModuleProviderRegistrarInterface::class
        );

        $this->stage = new RegisterProvidersStage(
            $this->registrar
        );
    }

    public function test_registers_module_providers(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [
                'Modules\\Blog\\Providers\\BlogServiceProvider',
            ],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $this->registrar
            ->expects($this->once())
            ->method('registerDefinition')
            ->with($definition);

        $this->stage->process($context);

        $this->assertFalse($context->hasException());
        $this->assertSame($definition, $context->definition());
    }

    public function test_does_not_register_when_context_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [
                'Modules\\Blog\\Providers\\BlogServiceProvider',
            ],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $context->setException(
            new RuntimeException('Previous failure.')
        );

        $this->registrar
            ->expects($this->never())
            ->method('registerDefinition');

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertSame($definition, $context->definition());
    }

    public function test_stores_exception_when_definition_is_missing(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $this->registrar
            ->expects($this->never())
            ->method('registerDefinition');

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertNull($context->definition());
    }

    public function test_stores_exception_when_registrar_fails(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [
                'Modules\\Blog\\Providers\\BlogServiceProvider',
            ],
            permissionDefinitions: [],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $context->setDefinition($definition);

        $exception = new RuntimeException(
            'Provider registration failed.'
        );

        $this->registrar
            ->expects($this->once())
            ->method('registerDefinition')
            ->with($definition)
            ->willThrowException($exception);

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertSame($exception, $context->exception());
        $this->assertSame($definition, $context->definition());
    }
}
