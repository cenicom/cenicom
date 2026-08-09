<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap\Stages;

use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Module\ModuleDefinitionFactoryInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\Stages\CreateDefinitionStage;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class CreateDefinitionStageTest extends TestCase
{
    private ModuleDefinitionFactoryInterface&MockObject $factory;

    private ModuleLifecycleManager $lifecycle;

    private CreateDefinitionStage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(
            ModuleDefinitionFactoryInterface::class
        );

        $this->lifecycle = new ModuleLifecycleManager(
            new class implements EventDispatcherInterface {
                public function dispatch(object $event): void
                {
                    // No-op para pruebas unitarias.
                }
            }
        );

        $this->stage = new CreateDefinitionStage(
            $this->factory,
            $this->lifecycle,
        );
    }

    public function test_creates_definition_and_stores_it_in_context(): void
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

        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with('/modules/Blog/module.php')
            ->willReturn($definition);

        $this->stage->process($context);

        $this->assertTrue($context->hasDefinition());
        $this->assertSame($definition, $context->definition());
        $this->assertFalse($context->hasException());
    }

    public function test_stores_exception_when_factory_fails(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Broken/module.php'
        );

        $exception = new RuntimeException(
            'Invalid manifest.'
        );

        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with('/modules/Broken/module.php')
            ->willThrowException($exception);

        $this->stage->process($context);

        $this->assertFalse($context->hasDefinition());
        $this->assertTrue($context->hasException());
        $this->assertSame($exception, $context->exception());
    }

    public function test_does_not_execute_when_context_already_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $context->setException(
            new RuntimeException('Previous failure.')
        );

        $this->factory
            ->expects($this->never())
            ->method('create');

        $this->stage->process($context);

        $this->assertTrue($context->hasException());
        $this->assertFalse($context->hasDefinition());
    }
}
