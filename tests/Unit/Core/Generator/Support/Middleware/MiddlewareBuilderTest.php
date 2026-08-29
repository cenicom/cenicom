<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Middleware;

use App\Core\Generator\Support\Middleware\MiddlewareBuilder;
use Tests\Support\GeneratorTestCase;

final class MiddlewareBuilderTest extends GeneratorTestCase
{
    public function test_builds_expected_middleware_variables(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new MiddlewareBuilder();

        $variables = $builder->build($module);

        self::assertSame(
            $module->middlewareNamespace(),
            $variables['namespace'],
        );

        self::assertSame(
            $module->middlewareClass(),
            $variables['class'],
        );

        self::assertArrayHasKey('imports', $variables);
        self::assertArrayHasKey('methods', $variables);
    }

    public function test_builds_expected_imports(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new MiddlewareBuilder();

        $variables = $builder->build($module);

        self::assertStringContainsString(
            'use Closure;',
            $variables['imports'],
        );

        self::assertStringContainsString(
            'use Illuminate\Http\Request;',
            $variables['imports'],
        );

        self::assertStringContainsString(
            'use Symfony\Component\HttpFoundation\Response;',
            $variables['imports'],
        );
    }

    public function test_builds_expected_middleware_class_name(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new MiddlewareBuilder();

        $variables = $builder->build($module);

        self::assertSame(
            'CurrencyMiddleware',
            $variables['class'],
        );
    }

    public function test_builds_authorization_using_view_permission(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new MiddlewareBuilder();

        $variables = $builder->build($module);

        self::assertStringContainsString(
            "can('currency.view')",
            $variables['methods'],
        );

        self::assertStringContainsString(
            'abort(403);',
            $variables['methods'],
        );

        self::assertStringContainsString(
            'return $next($request);',
            $variables['methods'],
        );
    }

    public function test_builds_handle_method(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new MiddlewareBuilder();

        $variables = $builder->build($module);

        self::assertStringContainsString(
            'public function handle(Request $request, Closure $next): Response',
            $variables['methods'],
        );
    }
}
