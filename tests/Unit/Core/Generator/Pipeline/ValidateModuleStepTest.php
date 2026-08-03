<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Pipeline;

use App\Core\Generator\Contracts\PipelineStepInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Pipeline\Steps\ValidateModuleStep;
use PHPUnit\Framework\TestCase;

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
                'plural' => $plural,
            ],
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
}
