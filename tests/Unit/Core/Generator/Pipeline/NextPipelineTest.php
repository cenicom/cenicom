<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\NextPipeline;
use App\Core\Generator\Results\GeneratorResult;
use Closure;
use Mockery;
use Tests\TestCase;


final class NextPipelineTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_invokes_pipeline_step(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $next = static fn (
            ModuleData $module,
            GeneratorResult $result
        ): GeneratorResult => $result;

        $step = Mockery::mock(PipelineStepInterface::class);

        $step
            ->shouldReceive('handle')
            ->once()
            ->with(
                $module,
                $result,
                Mockery::type(Closure::class)
            )
            ->andReturn($result);

        $pipeline = new NextPipeline(
            $step,
            $next,
        );

        self::assertSame(
            $result,
            $pipeline($module, $result),
        );
    }

    public function test_returns_same_generator_result(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $step = Mockery::mock(PipelineStepInterface::class);

        $step
            ->shouldReceive('handle')
            ->once()
            ->andReturn($result);

        $pipeline = new NextPipeline(
            $step,
            static fn (
                ModuleData $module,
                GeneratorResult $result
            ): GeneratorResult => $result,
        );

        self::assertSame(
            $result,
            $pipeline($module, $result),
        );
    }

    public function test_passes_same_module_instance(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $step = Mockery::mock(PipelineStepInterface::class);

        $step
            ->shouldReceive('handle')
            ->once()
            ->withArgs(function (
                ModuleData $receivedModule,
                GeneratorResult $receivedResult,
                Closure $next
            ) use ($module, $result): bool {
                return $receivedModule === $module
                    && $receivedResult === $result;
            })
            ->andReturn($result);

        $pipeline = new NextPipeline(
            $step,
            static fn (
                ModuleData $module,
                GeneratorResult $result
            ): GeneratorResult => $result,
        );

        $pipeline($module, $result);

        self::assertTrue(true);
    }

    private function module(): ModuleData
    {
        return (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Product',
                'singular' => 'Product',
                'plural' => 'Products',
                'table' => 'products',
                'description' => 'Products',
            ],
            'generation' => [],
            'fields' => [],
        ]);
    }
}
