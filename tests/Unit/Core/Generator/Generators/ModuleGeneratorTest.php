<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Pipeline\Contracts\PipelineInterface;
use Mockery;
use Tests\TestCase;

final class ModuleGeneratorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_delegates_generation_to_pipeline(): void
    {
        $pipeline = $this->createMock(PipelineInterface::class);

        $module = $this->module();

        $expected = GeneratorResult::success(
            '/tmp/generated.php'
        );

        $pipeline
            ->expects($this->once())
            ->method('process')
            ->with($module)
            ->willReturn($expected);

        $generator = new ModuleGenerator($pipeline);

        $result = $generator->generate($module);

        $this->assertSame(
            $expected,
            $result
        );
    }

    private function module(): ModuleData
    {
        return (new ModuleDataFactory())->create([
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
            'columns' => [],
        ]);
    }
}
