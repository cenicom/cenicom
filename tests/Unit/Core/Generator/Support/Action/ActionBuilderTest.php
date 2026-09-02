<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Action;

use App\Core\Generator\Builders\ActionBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use Tests\TestCase;

final class ActionBuilderTest extends TestCase
{
    public function test_build_generates_action_variables(): void
    {
        $builder = new ActionBuilder();

        $variables = $builder->build(
            $this->module()
        );

        $this->assertArrayHasKey('namespace', $variables);

        $this->assertArrayHasKey('action', $variables);

        $this->assertArrayHasKey(
            'qualifiedServiceInterface',
            $variables
        );
        $this->assertArrayHasKey(
            'serviceInterface',
            $variables
        );

        $this->assertSame(
            $this->module()->actionNamespace(),
            $variables['namespace']
        );

        $this->assertSame(
            'CurrencyAction',
            $variables['action']
        );

        $this->assertSame(
            $this->module()->qualifiedServiceInterface(),
            $variables['qualifiedServiceInterface']
        );

        $this->assertSame(
            'CurrencyServiceInterface',
            $variables['serviceInterface']
        );

        $this->assertSame(
            [
                'namespace',
                'action',
                'qualifiedServiceInterface',
                'serviceInterface',
            ],
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
        ]);
    }
}
