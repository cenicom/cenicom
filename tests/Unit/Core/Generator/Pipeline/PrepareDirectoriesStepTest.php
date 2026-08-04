<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\Steps\PrepareDirectoriesStep;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\Contracts\FileWriterInterface;
use App\Core\Generator\Support\FileWriter;
use RuntimeException;
use Tests\TestCase;

final class PrepareDirectoriesStepTest extends TestCase
{
    private ModuleDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ModuleDataFactory();
    }

    public function test_step_implements_pipeline_contract(): void
    {
        $step = new PrepareDirectoriesStep(
            new FileWriter()
        );

        self::assertInstanceOf(
            PipelineStepInterface::class,
            $step
        );
    }

    public function test_existing_directories_calls_next(): void
    {
        $called = false;

        $writer = $this->createMock(FileWriterInterface::class);

        $writer
            ->expects(self::exactly(count($this->module()->directories())))
            ->method('ensureDirectory');

        $step = new PrepareDirectoriesStep($writer);

        $result = $step->handle(
            $this->module(),
            new GeneratorResult(),
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$called): GeneratorResult {

                $called = true;

                return $result;
            }
        );

        self::assertTrue($called);
        self::assertFalse($result->hasErrors());
    }

    public function test_directory_creation_failure_stops_pipeline(): void
    {
        $called = false;

        $writer = $this->createMock(FileWriterInterface::class);

        $writer
            ->method('ensureDirectory')
            ->willThrowException(
                new RuntimeException('Directory error')
            );

        $step = new PrepareDirectoriesStep($writer);

        $result = $step->handle(
            $this->module(),
            new GeneratorResult(),
            function () use (&$called): GeneratorResult {

                $called = true;

                return new GeneratorResult();
            }
        );

        self::assertFalse($called);
        self::assertTrue($result->hasErrors());
    }

    public function test_preserves_same_module_instance(): void
    {
        $received = null;

        $writer = $this->createMock(FileWriterInterface::class);

        $step = new PrepareDirectoriesStep($writer);

        $module = $this->module();

        $step->handle(
            $module,
            new GeneratorResult(),
            function (
                ModuleData $module,
                GeneratorResult $result
            ) use (&$received): GeneratorResult {

                $received = $module;

                return $result;
            }
        );

        self::assertSame(
            $module,
            $received
        );
    }

    public function test_preserves_same_generator_result(): void
    {
        $received = null;

        $writer = $this->createMock(FileWriterInterface::class);

        $step = new PrepareDirectoriesStep($writer);

        $result = new GeneratorResult();

        $step->handle(
            $this->module(),
            $result,
            function (
                ModuleData $module,
                GeneratorResult $generatorResult
            ) use (&$received): GeneratorResult {

                $received = $generatorResult;

                return $generatorResult;
            }
        );

        self::assertSame(
            $result,
            $received
        );
    }

    private function module(): ModuleData
    {
        return $this->factory->create([
            'identity' => [
                'name' => 'Customer',
                'singular' => 'customer',
                'plural' => 'customers',
                'table' => 'customers',
                'description' => 'Customer module',
            ],
            'fields' => [],
        ]);
    }
}
