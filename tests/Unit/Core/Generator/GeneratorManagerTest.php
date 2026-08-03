<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\GeneratorManager;
use App\Core\Generator\Results\GeneratorResult;
use Tests\TestCase;

final class GeneratorManagerTest extends TestCase
{
    public function test_executes_registered_generators(): void
    {
        $first = $this->createMockGenerator();
        $second = $this->createMockGenerator();

        $manager = new GeneratorManager([
            $first,
            $second,
        ]);

        $module = $this->module();

        $result = $manager->generate($module);

        $this->assertInstanceOf(
            GeneratorResult::class,
            $result
        );

        $this->assertSame(
            2,
            $result->createdCount()
        );
    }


    public function test_stops_execution_when_generator_fails(): void
    {
        $failure = $this->createFailingGenerator();

        $success = $this->createMockGenerator();

        $manager = new GeneratorManager([
            $failure,
            $success,
        ]);

        $result = $manager->generate(
            $this->module()
        );

        $this->assertTrue(
            $result->hasErrors()
        );

        $this->assertSame(
            0,
            $result->createdCount()
        );
    }


    public function test_register_adds_generator(): void
    {
        $manager = new GeneratorManager();

        $generator = $this->createMockGenerator();

        $manager->register(
            $generator
        );

        $result = $manager->generate(
            $this->module()
        );

        $this->assertSame(
            1,
            $result->createdCount()
        );
    }


    private function createMockGenerator(): GeneratorInterface
    {
        return new class implements GeneratorInterface {

            public function supports(
                ModuleData $module
            ): bool {
                return true;
            }

            public function generate(
                ModuleData $module
            ): GeneratorResult {

                return GeneratorResult::success(
                    '/tmp/generated.php'
                );
            }
        };
    }


    private function createFailingGenerator(): GeneratorInterface
    {
        return new class implements GeneratorInterface {

            public function supports(
                ModuleData $module
            ): bool {
                return true;
            }

            public function generate(
                ModuleData $module
            ): GeneratorResult {

                return GeneratorResult::failure(
                    'Generation failed'
                );
            }
        };
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
