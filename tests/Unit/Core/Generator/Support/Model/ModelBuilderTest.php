<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Model;

use App\Core\Generator\Builders\ModelBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class ModelBuilderTest extends TestCase
{
    public function test_build_generates_model_variables(): void
    {
        $module = $this->module();

        $builder = new ModelBuilder();

        $variables = $builder->build($module);

        $this->assertSame(
            $module->modelNamespace(),
            $variables['namespace']
        );

        $this->assertSame(
            $module->modelClass(),
            $variables['model']
        );

        $this->assertSame(
            $module->description(),
            $variables['description']
        );

        $this->assertSame(
            $module->table(),
            $variables['table']
        );

        $this->assertArrayHasKey('fillable', $variables);
        $this->assertArrayHasKey('casts', $variables);
        $this->assertArrayHasKey('imports', $variables);
        $this->assertArrayHasKey('traits', $variables);
        $this->assertArrayHasKey('constants', $variables);
        $this->assertArrayHasKey('relationships', $variables);
        $this->assertArrayHasKey('scopes', $variables);
    }

    public function test_build_generates_expected_variables_for_model_stub(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module()
        );

        $expectedKeys = [
            'namespace',
            'model',
            'description',
            'table',
            'fillable',
            'casts',
            'imports',
            'traits',
            'constants',
            'relationships',
            'scopes',
        ];

        $this->assertSame(
            $expectedKeys,
            array_keys($variables)
        );
    }

    public function test_build_generates_fillable_columns(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module([
                'fields' => [
                    [
                        'name' => 'name',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'symbol',
                        'type' => 'string',
                    ],
                ],
            ])
        );

        $this->assertStringContainsString(
            "'name'",
            $variables['fillable']
        );

        $this->assertStringContainsString(
            "'symbol'",
            $variables['fillable']
        );
    }

    public function test_build_generates_default_model_imports(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module()
        );

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\Factories\HasFactory;',
            $variables['imports']
        );

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\Model;',
            $variables['imports']
        );
    }

    public function test_build_generates_has_factory_trait(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module()
        );

        $this->assertStringContainsString(
            'use HasFactory;',
            $variables['traits']
        );
    }

    public function test_build_generates_soft_delete_support(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module([
                'options' => [
                    'softDeletes' => true,
                ],
            ])
        );

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\SoftDeletes;',
            $variables['imports']
        );

        $this->assertStringContainsString(
            'use SoftDeletes;',
            $variables['traits']
        );
    }

    public function test_build_generates_uuid_support(): void
    {
        $variables = (new ModelBuilder())->build(
            $this->module([
                'options' => [
                    'uuid' => true,
                ],
            ])
        );

        $this->assertStringContainsString(
            'use Illuminate\Database\Eloquent\Concerns\HasUuids;',
            $variables['imports']
        );

        $this->assertStringContainsString(
            'use HasUuids;',
            $variables['traits']
        );
    }

    private function module(array $overrides = []): ModuleData
    {
        return (new ModuleDataFactory())->create(
            array_replace_recursive(
                [
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
                        ],
                        [
                            'name' => 'symbol',
                            'type' => 'string',
                        ],
                    ],
                ],
                $overrides
            )
        );
    }
}
