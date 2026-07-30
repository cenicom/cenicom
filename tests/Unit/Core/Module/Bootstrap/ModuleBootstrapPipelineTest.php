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
}
