<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;


use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\Steps\ValidateModuleStep;
use App\Core\Generator\Results\GeneratorResult;
use Tests\TestCase;

final class ValidateModuleStepTest extends TestCase
{
    private function createModule(
        string $name = 'Currency',
        string $plural = 'Currencies'
    ): ModuleData {

        $factory = app(ModuleDataFactory::class);

        return $factory->create([
            'identity' => [
                'name' => $name,
                'singular' => strtolower($name),
                'plural' => $plural,
                'table' => strtolower($plural),
                'description' => 'Test module',
            ],
            'fields' => [],
        ]);
    }

    /*
    public function test_invalid_module_returns_error(): void
    {
        $step = new ValidateModuleStep();

        $factory = new ModuleDataFactory();

        $module = $factory->create([
            'identity' => [
                'name' => '',
            ],
        ]);

        $result = $step->execute($module);

        $this->assertTrue(
            $result->hasErrors()
        );
    }
*/

    public function test_step_implements_pipeline_contract(): void
    {
        $step = new ValidateModuleStep();

        $this->assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_valid_module_calls_next(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $called = false;

        $returned = $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            }
        );

        self::assertTrue($called);

        self::assertSame(
            $result,
            $returned
        );

        self::assertFalse(
            $returned->hasErrors()
        );
    }

    public function test_invalid_module_stops_pipeline(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule('');

        $result = new GeneratorResult();

        $called = false;

        $returned = $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            }
        );

        self::assertFalse($called);

        self::assertTrue(
            $returned->hasErrors()
        );
    }

    public function test_preserves_same_module_instance(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $receivedModule = null;

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedModule): GeneratorResult {

                $receivedModule = $module;

                return $result;
            }
        );

        self::assertSame(
            $module,
            $receivedModule
        );
    }

    public function test_preserves_same_generator_result(): void
    {
        $step = new ValidateModuleStep();

        $module = $this->createModule();

        $result = new GeneratorResult();

        $receivedResult = null;

        $step->handle(
            $module,
            $result,
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$receivedResult): GeneratorResult {

                $receivedResult = $result;

                return $result;
            }
        );

        self::assertSame(
            $result,
            $receivedResult
        );
    }
}
