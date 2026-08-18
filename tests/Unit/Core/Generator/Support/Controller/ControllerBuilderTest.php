<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Controller;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Support\Controller\ControllerBuilder;
use Tests\TestCase;

final class ControllerBuilderTest extends TestCase
{
    public function test_build_generates_controller_variables(): void
    {
        $builder = new ControllerBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertCount(
            15,
            $variables
        );

        $this->assertSame(
            $this->module()->controllerNamespace(),
            $variables['namespace']
        );

        $this->assertSame(
            $this->module()->qualifiedServiceInterface(),
            $variables['qualifiedServiceInterface']
        );

        $this->assertSame(
            $this->module()->qualifiedStoreRequest(),
            $variables['qualifiedStoreRequest']
        );

        $this->assertSame(
            $this->module()->qualifiedUpdateRequest(),
            $variables['qualifiedUpdateRequest']
        );

        $this->assertSame(
            $this->module()->qualifiedModel(),
            $variables['qualifiedModel']
        );

        $this->assertSame(
            'Currency',
            $variables['model']
        );

        $this->assertSame(
            'CurrencyController',
            $variables['controller']
        );

        $this->assertSame(
            'CurrencyServiceInterface',
            $variables['serviceInterface']
        );

        $this->assertSame(
            'currencies',
            $variables['viewPrefix']
        );

        $this->assertSame(
            'currencies',
            $variables['pluralVariable']
        );

        $this->assertSame(
            'StoreCurrencyRequest',
            $variables['storeRequest']
        );

        $this->assertSame(
            'UpdateCurrencyRequest',
            $variables['updateRequest']
        );

        $this->assertSame(
            'currencies',
            $variables['routeName']
        );

        $this->assertSame(
            'currency',
            $variables['variable']
        );

        $this->assertSame(
            'Currency',
            $variables['displayName']
        );
    }

    public function test_build_generates_variables_required_by_controller_stub(): void
    {
        $builder = new ControllerBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $expectedKeys = [
            'namespace',
            'qualifiedServiceInterface',
            'qualifiedStoreRequest',
            'qualifiedUpdateRequest',
            'qualifiedModel',
            'model',
            'controller',
            'serviceInterface',
            'viewPrefix',
            'pluralVariable',
            'storeRequest',
            'updateRequest',
            'routeName',
            'variable',
            'displayName',
        ];

        $this->assertSame(
            $expectedKeys,
            array_keys($variables)
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
                ],

                [
                    'name' => 'symbol',
                    'type' => 'string',
                ],
            ],
        ]);
    }
}
