<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\RequestGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Request\RequestBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Illuminate\Support\Facades\File;
use Tests\Support\GeneratorTestCase;


final class RequestGeneratorTest extends GeneratorTestCase
{
    public function test_generates_request_files(): void
    {
        $generator = $this->createGenerator();

        $module = (new ModuleDataFactory())->create([
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

            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                ],
                [
                    'name' => 'symbol',
                    'type' => 'string',
                    'required' => true,
                ],
            ],
        ]);

        $result = $generator->generate($module);

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertEquals(
            2,
            $result->createdCount()
        );
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(
            app_path('Http/Requests/Currency')
        );

        parent::tearDown();
    }
    public function test_generator_supports_any_module(): void
    {
        $generator = $this->createGenerator();

        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],

            'generation' => [
                'routePrefix' => 'tests',
                'routeName' => 'tests',
                'viewPrefix' => 'tests',
            ],
        ]);

        $this->assertTrue(
            $generator->supports($module)
        );
    }

    public function test_generates_valid_requests(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertCount(2, $result->created());

        $store = file_get_contents($result->created()[0]);
        $update = file_get_contents($result->created()[1]);

        $this->assertStringContainsString(
            'class StoreCurrencyRequest',
            $store
        );

        $this->assertStringContainsString(
            'class UpdateCurrencyRequest',
            $update
        );

        $this->assertStringContainsString(
            "'name'",
            $store
        );

        $this->assertStringContainsString(
            "'symbol'",
            $store
        );
    }

    private function createGenerator(): RequestGenerator
    {
        return new RequestGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new RequestBuilder(),
        );
    }
}
