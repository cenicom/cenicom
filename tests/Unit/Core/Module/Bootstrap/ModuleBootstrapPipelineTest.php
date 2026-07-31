<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\Bootstrap\ModuleBootstrapPipeline;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class ModuleBootstrapPipelineTest extends TestCase
{


    public function test_executes_all_stages_in_order(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $executionOrder = [];

        $stage1 = $this->createCallbackStage(
            function (ModuleBootstrapContext $context) use (&$executionOrder): void {
                $executionOrder[] = 'stage1';
            }
        );

        $stage2 = $this->createCallbackStage(
            function (ModuleBootstrapContext $context) use (&$executionOrder): void {
                $executionOrder[] = 'stage2';
            }
        );

        $stage3 = $this->createCallbackStage(
            function (ModuleBootstrapContext $context) use (&$executionOrder): void {
                $executionOrder[] = 'stage3';
            }
        );

        $pipeline = new ModuleBootstrapPipeline([
            $stage1,
            $stage2,
            $stage3,
        ]);

        $pipeline->process($context);

        $this->assertSame(
            ['stage1', 'stage2', 'stage3'],
            $executionOrder
        );
    }

    public function test_stops_pipeline_when_context_contains_exception(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $executionOrder = [];

        $stage1 = $this->createCallbackStage(
            function (ModuleBootstrapContext $context) use (&$executionOrder): void {
                $executionOrder[] = 'stage1';

                $context->setException(
                    new RuntimeException('Stop pipeline')
                );
            }
        );

        $stage2 = $this->createCallbackStage(
            function () use (&$executionOrder): void {
                $executionOrder[] = 'stage2';
            }
        );

        $pipeline = new ModuleBootstrapPipeline([
            $stage1,
            $stage2,
        ]);

        $pipeline->process($context);

        $this->assertSame(
            ['stage1'],
            $executionOrder
        );

        $this->assertTrue($context->hasException());
    }

    public function test_uses_same_context_for_every_stage(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json'
        );

        $stage1 = $this->createMock(
            ModuleBootstrapStageInterface::class
        );

        $stage2 = $this->createMock(
            ModuleBootstrapStageInterface::class
        );

        $stage1
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($context));

        $stage2
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($context));

        $pipeline = new ModuleBootstrapPipeline([
            $stage1,
            $stage2,
        ]);

        $pipeline->process($context);
    }

    /**
     * @param callable(ModuleBootstrapContext):void $callback
     */
    private function createCallbackStage(callable $callback): ModuleBootstrapStageInterface
    {
        return new class($callback) implements ModuleBootstrapStageInterface
        {
            /**
             * @var callable(ModuleBootstrapContext): void
             */
            private $callback;

            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }

            public function process(ModuleBootstrapContext $context): void
            {
                ($this->callback)($context);
            }
        };
    }

    //🔹 MBP-004 — test_process_with_empty_stage_collection()
    public function test_process_with_empty_stage_collection(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $pipeline = new ModuleBootstrapPipeline([]);

        // Act
        $pipeline->process($context);

        // Assert
        $this->assertFalse(
            $context->hasException()
        );

        $this->assertFalse(
            $context->hasDefinition()
        );

        $this->assertSame(
            '/modules/Blog/module.php',
            $context->manifestPath()
        );
    }

    //MBP-005 Certificar que si el ModuleBootstrapContext ya contiene una excepción antes de iniciar el pipeline, ningún stage debe ejecutarse.
    public function test_does_not_execute_any_stage_when_context_already_contains_exception(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $context->setException(
            new RuntimeException('Previous failure.')
        );

        $stage = $this->createMock(
            ModuleBootstrapStageInterface::class
        );

        $stage
            ->expects($this->never())
            ->method('process');

        $pipeline = new ModuleBootstrapPipeline([
            $stage,
        ]);

        // Act
        $pipeline->process($context);

        // Assert
        $this->assertTrue(
            $context->hasException()
        );
    }

    //MBP-006 Certificar que cada stage es ejecutado exactamente una vez.
    public function test_executes_each_stage_exactly_once(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $stage1 = $this->createMock(
            ModuleBootstrapStageInterface::class
        );

        $stage2 = $this->createMock(
            ModuleBootstrapStageInterface::class
        );

        $stage1
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($context));

        $stage2
            ->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($context));

        $pipeline = new ModuleBootstrapPipeline([
            $stage1,
            $stage2,
        ]);

        // Act
        $pipeline->process($context);
    }

    //MBP-007 Certificar que el constructor realmente cumple su contrato:
    // @param iterable<ModuleBootstrapStageInterface> $stages
    public function test_process_accepts_any_iterable_of_stages(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $executionOrder = [];

        $generator = (function () use (&$executionOrder) {
            yield $this->createCallbackStage(
                function () use (&$executionOrder): void {
                    $executionOrder[] = 'stage1';
                }
            );

            yield $this->createCallbackStage(
                function () use (&$executionOrder): void {
                    $executionOrder[] = 'stage2';
                }
            );
        })();

        $pipeline = new ModuleBootstrapPipeline(
            $generator
        );

        // Act
        $pipeline->process($context);

        // Assert
        $this->assertSame(
            ['stage1', 'stage2'],
            $executionOrder
        );
    }

    //Nueva certificación:
    public function test_stops_pipeline_when_context_is_skipped(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/DisabledModule/module.json'
        );

        $executionOrder = [];

        $stage1 = $this->createCallbackStage(
            function (ModuleBootstrapContext $context) use (&$executionOrder): void {
                $executionOrder[] = 'stage1';

                $context->markSkipped();
            }
        );

        $stage2 = $this->createCallbackStage(
            function () use (&$executionOrder): void {
                $executionOrder[] = 'stage2';
            }
        );

        $pipeline = new ModuleBootstrapPipeline([
            $stage1,
            $stage2,
        ]);

        $pipeline->process($context);

        $this->assertSame(
            ['stage1'],
            $executionOrder
        );

        $this->assertTrue(
            $context->isSkipped()
        );
    }
}
