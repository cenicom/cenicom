<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Registry;

use App\Core\View\Registry\ViewDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class ViewDefinitionRegistryTest extends TestCase
{
    public function test_add_registers_a_definition(): void
    {
        $registry = new ViewDefinitionRegistry();

        $registry->add('App\\Modules\\Institution\\View\\InstitutionView');

        self::assertSame(
            [
                'App\\Modules\\Institution\\View\\InstitutionView',
            ],
            $registry->definitions()
        );
    }

    public function test_definitions_returns_registered_definitions(): void
    {
        $registry = new ViewDefinitionRegistry();

        $definitions = [
            'App\\Modules\\Institution\\View\\InstitutionView',
            'App\\Modules\\Inventory\\View\\InventoryView',
        ];

        foreach ($definitions as $definition) {
            $registry->add($definition);
        }

        self::assertSame(
            $definitions,
            $registry->definitions()
        );
    }

    public function test_clear_removes_all_definitions(): void
    {
        $registry = new ViewDefinitionRegistry();

        $registry->add(
            'App\\Modules\\Institution\\View\\InstitutionView'
        );

        $registry->clear();

        self::assertSame([], $registry->definitions());
    }
}
