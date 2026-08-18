<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Migration;

use App\Core\Generator\Builders\MigrationBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Processors\MigrationFieldProcessor;
use Tests\TestCase;

final class MigrationBuilderTest extends TestCase
{
    public function test_build_generates_migration_variables(): void
    {
        $builder = new MigrationBuilder(
            new MigrationFieldProcessor()
        );

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey(
            'table',
            $variables
        );

        $this->assertArrayHasKey(
            'columns',
            $variables
        );

        $this->assertArrayHasKey(
            'timestamps',
            $variables
        );

        $this->assertArrayHasKey(
            'softDeletes',
            $variables
        );

        $this->assertSame(
            'currencies',
            $variables['table']
        );
    }

    public function test_build_generates_columns(): void
    {
        $builder = new MigrationBuilder(
            new MigrationFieldProcessor()
        );

        $variables = $builder->build(
            $this->module()
        );

        $this->assertStringContainsString(
            "\$table->string('name')",
            $variables['columns']
        );

        $this->assertStringContainsString(
            "\$table->string('symbol')",
            $variables['columns']
        );
    }

    public function test_build_generates_timestamps(): void
    {
        $builder = new MigrationBuilder(
            new MigrationFieldProcessor()
        );

        $variables = $builder->build(
            $this->module()
        );

        $this->assertSame(
            '$table->timestamps();',
            $variables['timestamps']
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
    }
}
