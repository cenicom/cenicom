<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Observer;

//use App\Core\Generator\Builders\BaseBuilder;
//use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Support\Observer\ObserverBuilder;
use Tests\Support\GeneratorTestCase;

final class ObserverBuilderTest extends GeneratorTestCase
{
    public function test_builds_expected_observer_variables(): void
    {
        $module = $this->createModuleData();

        $variables = (new ObserverBuilder())->build($module);

        self::assertArrayHasKey('namespace', $variables);
        self::assertArrayHasKey('imports', $variables);
        self::assertArrayHasKey('class', $variables);
        self::assertArrayHasKey('methods', $variables);

        self::assertSame(
            $module->observerNamespace(),
            $variables['namespace'],
        );

        self::assertSame(
            $module->observerClass(),
            $variables['class'],
        );
    }

    public function test_builds_expected_imports(): void
    {
        $module = $this->createModuleData();

        $variables = (new ObserverBuilder())->build($module);

        self::assertStringContainsString(
            'use ' . $module->qualifiedModel() . ';',
            $variables['imports'],
        );

        self::assertStringContainsString(
            'use Illuminate\\Database\\Eloquent\\Model;',
            $variables['imports'],
        );
    }

    public function test_builds_all_expected_observer_methods(): void
    {
        $module = $this->createModuleData();

        $variables = (new ObserverBuilder())->build($module);

        $methods = $variables['methods'];

        foreach ([
            'creating',
            'created',
            'updating',
            'updated',
            'deleting',
            'deleted',
            'restoring',
            'restored',
            'forceDeleted',
        ] as $method) {
            self::assertStringContainsString(
                "public function {$method}(",
                $methods,
            );
        }
    }

    public function test_builds_model_signatures_correctly(): void
    {
        $module = $this->createModuleData();

        $variables = (new ObserverBuilder())->build($module);

        $methods = $variables['methods'];

        self::assertStringContainsString(
            "{$module->modelClass()} \${$module->variable()}",
            $methods,
        );
    }

    public function test_builds_void_return_types(): void
    {
        $module = $this->createModuleData();

        $variables = (new ObserverBuilder())->build($module);

        $methods = $variables['methods'];

        self::assertSame(
            9,
            substr_count($methods, '): void'),
        );
    }
}
