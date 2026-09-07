<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Factories;

use App\Core\Generator\Factories\ModuleDefinitionFactory;
use App\Core\Generator\Specifications\Contracts\SpecificationInterface;
use PHPUnit\Framework\TestCase;

final class ModuleDefinitionFactoryTest extends TestCase
{
    public function test_transports_navigation_from_specification(): void
    {
        $navigation = [
            'groups' => [
                [
                    'id' => 'catalogs',
                    'label' => 'Catalogs',
                    'icon' => 'bi-grid',
                    'order' => 10,
                ],
            ],
            'items' => [
                [
                    'id' => 'currencies',
                    'label' => 'Currencies',
                    'route' => 'currencies.index',
                    'permission' => 'currencies.view',
                    'icon' => 'bi-cash',
                    'order' => 10,
                    'group' => 'catalogs',
                ],
            ],
        ];

        $specification = $this->createMock(
            SpecificationInterface::class
        );

        $specification
            ->method('identity')
            ->willReturn([
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ]);

        $specification
            ->method('security')
            ->willReturn([]);

        $specification
            ->method('permissions')
            ->willReturn([]);

        $specification
            ->method('fields')
            ->willReturn([]);

        $specification
            ->method('columns')
            ->willReturn([]);

        $specification
            ->method('navigation')
            ->willReturn($navigation);

        $specification
            ->method('generation')
            ->willReturn([]);

        $specification
            ->method('metadata')
            ->willReturn([]);

        $factory = new ModuleDefinitionFactory();

        $definition = $factory->create($specification);

        self::assertArrayHasKey('navigation', $definition);
        self::assertSame($navigation, $definition['navigation']);
    }
}
