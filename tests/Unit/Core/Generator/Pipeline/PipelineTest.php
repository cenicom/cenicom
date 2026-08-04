<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Pipeline\Pipeline;
use App\Core\Generator\Results\GeneratorResult;
use Closure;
use Tests\TestCase;

final class PipelineTest extends TestCase
{
    private ModuleDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ModuleDataFactory();
    }

    public function test_executes_all_pipeline_steps(): void
    {
        $calls = [];

        $step1 = new class($calls) implements PipelineStepInterface {

            public function __construct(
                private array &$calls,
            ) {}

            public function handle(
                ModuleData $module,
                GeneratorResult $result,
                Closure $next,
            ): GeneratorResult {

                $this->calls[] = 'step1';

                return $next(
                    $module,
                    $result,
                );
            }
        };

        $step2 = new class($calls) implements PipelineStepInterface {

            public function __construct(
                private array &$calls,
            ) {}

            public function handle(
                ModuleData $module,
                GeneratorResult $result,
                Closure $next,
            ): GeneratorResult {

                $this->calls[] = 'step2';

                return $next(
                    $module,
                    $result,
                );
            }
        };

        $pipeline = new Pipeline([
            $step1,
            $step2,
        ]);

        $pipeline->process($this->module());

        self::assertSame(
            ['step1', 'step2'],
            $calls
        );
    }

    public function test_returns_generator_result(): void
    {
        $pipeline = new Pipeline([]);

        $result = $pipeline->process(
            $this->module()
        );

        self::assertInstanceOf(
            GeneratorResult::class,
            $result
        );
    }

    public function test_pipeline_with_no_steps_returns_empty_result(): void
    {
        $pipeline = new Pipeline([]);

        $result = $pipeline->process(
            $this->module()
        );

        self::assertFalse(
            $result->hasErrors()
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
