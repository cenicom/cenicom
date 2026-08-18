<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\View;

use App\Core\Generator\Builders\ViewBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;
use App\Core\Generator\Presentation\Renderers\TableRenderer;
use App\Core\Generator\Support\StubManager;
use Tests\TestCase;

final class ViewBuilderTest extends TestCase
{
    public function test_build_generates_view_variables(): void
    {
        $builder = $this->createBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey(
            'title',
            $variables
        );

        $this->assertArrayHasKey(
            'description',
            $variables
        );

        $this->assertArrayHasKey(
            'model',
            $variables
        );

        $this->assertArrayHasKey(
            'modelClass',
            $variables
        );

        $this->assertArrayHasKey(
            'singular',
            $variables
        );

        $this->assertArrayHasKey(
            'plural',
            $variables
        );

        $this->assertArrayHasKey(
            'routePrefix',
            $variables
        );

        $this->assertArrayHasKey(
            'routeName',
            $variables
        );

        $this->assertArrayHasKey(
            'viewPrefix',
            $variables
        );

        $this->assertArrayHasKey(
            'table',
            $variables
        );

        $this->assertArrayHasKey(
            'fields',
            $variables
        );

        $this->assertArrayHasKey(
            'collection',
            $variables
        );

        $this->assertArrayHasKey(
            'columnCount',
            $variables
        );

        $this->assertArrayHasKey(
            'form_fields',
            $variables
        );

        $this->assertArrayHasKey(
            'table_columns',
            $variables
        );

        $this->assertArrayHasKey(
            'columns',
            $variables
        );
    }

    public function test_build_generates_domain_values(): void
    {
        $builder = $this->createBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertSame(
            'currencies',
            $variables['title']
        );

        $this->assertSame(
            'Currency module',
            $variables['description']
        );

        $this->assertSame(
            'currency',
            $variables['model']
        );

        $this->assertSame(
            'Currency',
            $variables['modelClass']
        );

        $this->assertSame(
            'currency',
            $variables['singular']
        );

        $this->assertSame(
            'currencies',
            $variables['plural']
        );

        $this->assertSame(
            'currencies',
            $variables['routePrefix']
        );

        $this->assertSame(
            'currencies',
            $variables['routeName']
        );

        $this->assertSame(
            'currencies',
            $variables['viewPrefix']
        );

        $this->assertSame(
            'currencies',
            $variables['table']
        );

        $this->assertSame(
            'currencies',
            $variables['collection']
        );
    }

    public function test_build_generates_rendered_presentation_variables(): void
    {
        $builder = $this->createBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertIsString(
            $variables['form_fields']
        );

        $this->assertIsString(
            $variables['table_columns']
        );

        $this->assertIsString(
            $variables['columns']
        );
    }

    private function createBuilder(): ViewBuilder
    {
        return new ViewBuilder(
            new PresentationFactory(),
            new ComponentRenderer(
                new StubManager()
            ),
            new TableRenderer(
                new StubManager()
            ),
            new ShowRenderer(
                new StubManager()
            ),
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
