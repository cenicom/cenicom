<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Policies;


use App\Core\Generator\Support\Policies\PolicyBuilder;
use Tests\Support\GeneratorTestCase;

final class PolicyBuilderTest extends GeneratorTestCase
{
    public function test_builds_expected_policy_variables(): void
    {
        $module = $this->createModuleData();

        $variables = (new PolicyBuilder())->build($module);

        self::assertSame(
            $module->policyNamespace(),
            $variables['namespace'],
        );

        self::assertSame(
            implode("\n", [
                'use App\Models\User;',
                sprintf(
                    'use %s\\%s;',
                    $module->modelNamespace(),
                    $module->modelClass(),
                ),
            ]),
            $variables['imports'],
        );

        self::assertSame(
            $module->policyClass(),
            $variables['class'],
        );

        self::assertIsString($variables['methods']);
    }

    public function test_builds_all_expected_policy_methods(): void
    {
        $module = $this->createModuleData();

        $variables = (new PolicyBuilder())->build($module);

        $methods = $variables['methods'];

        foreach ([
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ] as $method) {
            self::assertStringContainsString(
                "public function {$method}",
                $methods,
            );
        }
    }

    public function test_builds_model_signatures_correctly(): void
    {
        $module = $this->createModuleData();

        $variables = (new PolicyBuilder())->build($module);

        $methods = $variables['methods'];

        $model = $module->modelClass();
        $variable = $module->variable();

        foreach ([
            'view',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ] as $method) {
            self::assertStringContainsString(
                "public function {$method}",
                $methods,
            );

            self::assertStringContainsString(
                "{$model} \${$variable}",
                $methods,
            );
        }
    }

    public function test_builds_user_parameter(): void
    {
        $module = $this->createModuleData();

        $variables = (new PolicyBuilder())->build($module);

        self::assertSame(
            7,
            substr_count(
                $variables['methods'],
                'User $user',
            ),
        );
    }

    public function test_builds_methods_returning_boolean(): void
    {
        $module = $this->createModuleData();

        $variables = (new PolicyBuilder())->build($module);

        $methods = $variables['methods'];

        self::assertSame(
            7,
            substr_count($methods, '): bool'),
        );

        self::assertSame(
            7,
            substr_count($methods, 'return true;'),
        );
    }
}
