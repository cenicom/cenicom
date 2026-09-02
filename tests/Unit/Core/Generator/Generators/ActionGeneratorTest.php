<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Builders\ActionBuilder;
use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class ActionGeneratorTest extends GeneratorTestCase
{
    public function test_generates_action_file(): void
    {
        $generator = $this->createGenerator();

        $result = $generator->generate(
            $this->createModuleData()
        );

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertTrue(
            $result->hasCreatedFiles()
        );
    }

    public function test_generator_supports_any_module(): void
    {
        $generator = $this->createGenerator();

        $this->assertTrue(
            $generator->supports(
                $this->createModuleData()
            )
        );
    }

    public function test_generates_valid_action(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->actionPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final readonly class CurrencyAction',
            $content
        );

        $this->assertStringContainsString(
            'CurrencyServiceInterface',
            $content
        );

        $normalized = preg_replace(
            '/\s+/',
            ' ',
            $content
        );

        $this->assertIsString($normalized);

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\Model;',
            $content
        );

        $this->assertStringContainsString(
            'public function create( array $data ): Model',
            $normalized
        );

        $this->assertStringContainsString(
            'return $this->service->create($data);',
            $content
        );

        $normalized = preg_replace(
            '/\s+/',
            ' ',
            $content
        );

        $this->assertIsString($normalized);

        $this->assertStringContainsString(
            'return $this->service->update(',
            $content
        );

        $this->assertStringContainsString(
            '$id,',
            $content
        );

        $this->assertStringContainsString(
            '$data',
            $content
        );

        $this->assertStringContainsString(
            'public function update',
            $content
        );

        $this->assertStringContainsString(
            'int|string $id',
            $content
        );

        $this->assertStringContainsString(
            'array $data',
            $content
        );

        $this->assertStringContainsString(
            '): bool',
            $content
        );

        $this->assertStringContainsString(
            '$this->service->update(',
            $content
        );

        $this->assertStringContainsString(
            '$id',
            $content
        );

        $this->assertStringContainsString(
            '$data',
            $content
        );

        $this->assertStringContainsString(
            '$data',
            $content
        );

        $this->assertStringContainsString(
            'public function update( int|string $id, array $data ): bool',
            $normalized
        );

        $this->assertStringContainsString(
            'return $this->service->update( $id, $data );',
            $normalized
        );

        $this->assertStringContainsString(
            '$data',
            $content
        );

        $this->assertStringContainsString(
            'public function delete( int|string $id ): bool',
            $normalized
        );

        $this->assertStringContainsString(
            'return $this->service->delete( $id );',
            $normalized
        );
    }

    public function test_action_depends_on_module_service_interface(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $content = file_get_contents(
            $module->actionPath()
        );

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'use ' . $module->qualifiedServiceInterface() . ';',
            $content
        );

        $this->assertStringContainsString(
            'private ' . $module->serviceInterface() . ' $service',
            $content
        );
    }

    public function test_action_does_not_depend_on_repository(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $content = file_get_contents(
            $module->actionPath()
        );

        $this->assertNotFalse($content);

        $this->assertStringNotContainsString(
            'RepositoryInterface',
            $content
        );

        $this->assertStringNotContainsString(
            'BaseRepository',
            $content
        );
    }

    public function test_action_does_not_contain_transaction_logic(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $content = file_get_contents(
            $module->actionPath()
        );

        $this->assertNotFalse($content);

        $this->assertStringNotContainsString(
            'DB::',
            $content
        );

        $this->assertStringNotContainsString(
            'transaction(',
            $content
        );
    }

    public function test_action_does_not_extend_base_action(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $content = file_get_contents(
            $module->actionPath()
        );

        $this->assertNotFalse($content);

        $this->assertStringNotContainsString(
            'BaseAction',
            $content
        );

        $this->assertStringNotContainsString(
            'extends BaseAction',
            $content
        );
    }

    public function test_action_depends_only_on_service_and_eloquent_model(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $content = file_get_contents(
            $module->actionPath()
        );

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'use ' . $module->qualifiedServiceInterface() . ';',
            $content
        );

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\Model;',
            $content
        );

        $this->assertStringNotContainsString(
            'RepositoryInterface',
            $content
        );

        $this->assertStringNotContainsString(
            'BaseRepository',
            $content
        );

        $this->assertStringNotContainsString(
            'Controller',
            $content
        );

        $this->assertStringNotContainsString(
            'Request',
            $content
        );
    }

    private function createGenerator(): ActionGenerator
    {
        return new ActionGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new ActionBuilder(),
        );
    }
}
