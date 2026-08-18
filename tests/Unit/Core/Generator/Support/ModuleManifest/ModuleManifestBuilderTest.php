<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\ModuleManifest;

use App\Core\Generator\Builders\ModuleManifestBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class ModuleManifestBuilderTest extends TestCase
{
    public function test_build_generates_manifest_variables(): void
    {
        $builder = new ModuleManifestBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey('name', $variables);
        $this->assertArrayHasKey('description', $variables);
        $this->assertArrayHasKey('model', $variables);
        $this->assertArrayHasKey('routePrefix', $variables);
        $this->assertArrayHasKey('routeName', $variables);
        $this->assertArrayHasKey('permissions', $variables);
        $this->assertArrayHasKey('menu', $variables);
        $this->assertArrayHasKey('api', $variables);
        $this->assertArrayHasKey('tests', $variables);
    }

    public function test_build_generates_expected_manifest_values(): void
    {
        $builder = new ModuleManifestBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertSame(
            'Currency',
            $variables['name']
        );

        $this->assertSame(
            'Currency module',
            $variables['description']
        );

        $this->assertSame(
            'Currency',
            $variables['model']
        );

        $this->assertSame(
            'currencies',
            $variables['routePrefix']
        );

        $this->assertSame(
            'currencies',
            $variables['routeName']
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
