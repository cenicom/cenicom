<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ModuleGenerator;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Validation\GeneratorTestSuite;
use Tests\TestCase;

final class ModuleGeneratorTest extends TestCase
{
    public function test_generates_complete_module(): void
    {
        $generator = new ModuleGenerator(
            [
                new FakeSupportedGenerator(),
                new FakeSupportedGenerator(),
            ],
            new GeneratorTestSuite(),
        );

        $result = $generator->generate(
            $this->module()
        );

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertCount(
            2,
            $result->created()
        );
    }

    public function test_skips_unsupported_generators(): void
    {
        $generator = new ModuleGenerator(
            [
                new FakeUnsupportedGenerator(),
            ],
            new GeneratorTestSuite(),
        );

        $result = $generator->generate(
            $this->module()
        );

        $this->assertFalse(
            $result->hasCreatedFiles()
        );
    }

    public function test_merges_all_generator_results(): void
    {
        $generator = new ModuleGenerator(
            [
                new FakeSupportedGenerator(),
                new FakeSupportedGenerator(),
                new FakeSupportedGenerator(),
            ],
            new GeneratorTestSuite(),
        );

        $result = $generator->generate(
            $this->module()
        );

        $this->assertCount(
            3,
            $result->created()
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

/*
|--------------------------------------------------------------------------
| Fakes
|--------------------------------------------------------------------------
*/

final class FakeSupportedGenerator implements GeneratorInterface
{
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function generate(ModuleData $module): GeneratorResult
    {
        $path = tempnam(
            sys_get_temp_dir(),
            'cn-generator-'
        );

        file_put_contents(
            $path,
            '<?php // generated file'
        );

        return (new GeneratorResult())
            ->addCreated($path);
    }
}

final class FakeUnsupportedGenerator implements GeneratorInterface
{
    public function supports(ModuleData $module): bool
    {
        return false;
    }

    public function generate(ModuleData $module): GeneratorResult
    {
        return new GeneratorResult();
    }
}
