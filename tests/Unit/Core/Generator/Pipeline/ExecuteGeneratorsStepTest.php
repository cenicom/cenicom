<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\Contracts\GeneratorManagerInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\ExecuteGeneratorsStep;
use App\Core\Generator\Results\GeneratorResult;
use Mockery;
use Tests\TestCase;

final class ExecuteGeneratorsStepTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_step_implements_pipeline_contract(): void
    {
        $manager = Mockery::mock(
            GeneratorManagerInterface::class
        );

        $step = new ExecuteGeneratorsStep($manager);

        self::assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_executes_generator_manager(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($result);

        $step = new ExecuteGeneratorsStep($manager);

        $returned = $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ): GeneratorResult {
                return $result;
            },
        );

        self::assertSame(
            $result,
            $returned
        );
    }

    public function test_pipeline_continues_when_generation_is_successful(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($result);

        $called = false;

        $step = new ExecuteGeneratorsStep($manager);

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            },
        );

        self::assertTrue($called);
    }

    public function test_pipeline_stops_when_generation_fails(): void
    {
        $module = $this->module();

        $failure = GeneratorResult::failure('Failure');

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($failure);

        $called = false;

        $step = new ExecuteGeneratorsStep($manager);

        $returned = $step->handle(
            $module,
            new GeneratorResult(),
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            },
        );

        self::assertFalse($called);

        self::assertTrue(
            $returned->hasErrors()
        );
    }

    public function test_returns_same_failure_result(): void
    {
        $module = $this->module();

        $failure = GeneratorResult::failure('Error');

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($failure);

        $step = new ExecuteGeneratorsStep($manager);

        $returned = $step->handle(
            $module,
            new GeneratorResult(),
            function (
                ModuleData $module,
                GeneratorResult $result
            ): GeneratorResult {
                return $result;
            },
        );

        self::assertSame(
            $failure,
            $returned
        );
    }

    private function module(): ModuleData
    {
        $factory = new ModuleDataFactory();

        return $factory->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],
            'fields' => [],
        ]);
    }

    /** @test */
    public function test_preserves_same_module_instance(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $receivedModule = null;

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($result);

        $step = new ExecuteGeneratorsStep($manager);

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedModule): GeneratorResult {

                $receivedModule = $module;

                return $result;
            },
        );

        self::assertSame(
            $module,
            $receivedModule
        );
    }

    public function test_preserves_same_generator_result(): void
    {
        $module = $this->module();

        $result = new GeneratorResult();

        $receivedResult = null;

        $manager = Mockery::mock(GeneratorManagerInterface::class);

        $manager
            ->shouldReceive('generate')
            ->once()
            ->with($module)
            ->andReturn($result);

        $step = new ExecuteGeneratorsStep($manager);

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedResult): GeneratorResult {

                $receivedResult = $result;

                return $result;
            },
        );

        self::assertSame(
            $result,
            $receivedResult
        );
    }
}
