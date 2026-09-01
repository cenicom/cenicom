<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Builders;

use App\Core\Generator\Builders\RouteBuilder;
use App\Core\Generator\Security\MiddlewareRegistry;
use App\Core\Generator\Support\MiddlewareResolver;
use Tests\Support\GeneratorTestCase;

final class RouteBuilderTest extends GeneratorTestCase
{
    public function test_builds_controller_and_route_variables(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],
            'generation' => [
                'routePrefix' => 'admin/tests',
                'routeName'   => 'admin.tests',
                'viewPrefix'  => 'tests',
            ],
        ]);

        $builder = $this->createBuilder();

        $variables = $builder->build($module);

        $this->assertSame(
            $module->qualifiedController(),
            $variables['qualifiedController']
        );

        $this->assertSame(
            $module->controllerClass(),
            $variables['controllerClass']
        );

        $this->assertSame(
            $module->plural(),
            $variables['plural']
        );

        $this->assertSame(
            $module->singular(),
            $variables['singular']
        );
    }

    public function test_route_resource_uses_route_prefix(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],
            'generation' => [
                'routePrefix' => 'admin/tests',
                'routeName'   => 'admin.tests',
                'viewPrefix'  => 'tests',
            ],
        ]);

        $builder = $this->createBuilder();

        $variables = $builder->build($module);

        $this->assertSame(
            'admin/tests',
            $variables['routeResource']
        );
    }

    public function test_route_names_use_route_name(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],
            'generation' => [
                'routePrefix' => 'admin/tests',
                'routeName'   => 'admin.tests',
                'viewPrefix'  => 'tests',
            ],
        ]);

        $builder = $this->createBuilder();

        $variables = $builder->build($module);

        $this->assertSame(
            'admin.tests',
            $variables['routeName']
        );
    }

    private function createBuilder(): RouteBuilder
    {
        return new RouteBuilder(
            new MiddlewareResolver(
                new MiddlewareRegistry(),
            ),
        );
    }
}
