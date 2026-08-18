<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\FeatureTest;

use App\Core\Generator\Builders\FeatureTestBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class FeatureTestBuilderTest extends TestCase
{
    public function test_build_generates_feature_test_variables(): void
    {
        $module = $this->module();

        $variables = (new FeatureTestBuilder())->build($module);

        $this->assertArrayHasKey('namespace', $variables);
        $this->assertArrayHasKey('featureTest', $variables);
        $this->assertArrayHasKey('model', $variables);
        $this->assertArrayHasKey('qualifiedModel', $variables);
        $this->assertArrayHasKey('route', $variables);
        $this->assertArrayHasKey('viewPrefix', $variables);

        $this->assertSame(
            $module->testNamespace(),
            $variables['namespace']
        );

        $this->assertSame(
            $module->featureTestClass(),
            $variables['featureTest']
        );

        $this->assertSame(
            $module->modelClass(),
            $variables['model']
        );

        $this->assertSame(
            $module->qualifiedModel(),
            $variables['qualifiedModel']
        );

        $this->assertSame(
            $module->routeName(),
            $variables['route']
        );

        $this->assertSame(
            $module->viewPrefix(),
            $variables['viewPrefix']
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
        ]);
    }
}
