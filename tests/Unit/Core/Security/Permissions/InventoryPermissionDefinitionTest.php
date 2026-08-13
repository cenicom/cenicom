<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Modules\Inventory\Security\InventoryPermissionDefinition;
use PHPUnit\Framework\TestCase;

final class InventoryPermissionDefinitionTest extends TestCase
{
    public function test_register_registers_inventory_permissions(): void
    {
        $registrar = $this->createMock(
            PermissionRegistrarInterface::class
        );

        $registrar
            ->expects(self::exactly(3))
            ->method('register')
            ->willReturnCallback(
                function (
                    string $name,
                    string $description = '',
                    ?string $module = null,
                ) use (&$permissions): \App\Core\Security\Permissions\DTO\PermissionDefinition {
                    $permissions[] = [
                        'name' => $name,
                        'description' => $description,
                        'module' => $module,
                    ];

                    return new \App\Core\Security\Permissions\DTO\PermissionDefinition(
                        name: $name,
                        description: $description,
                        module: $module,
                    );
                }
            );

        $permissions = [];

        $definition = new InventoryPermissionDefinition();

        $definition->register($registrar);

        self::assertSame(
            [
                [
                    'name' => 'inventory.products.view',
                    'description' => 'Permite consultar productos.',
                    'module' => 'inventory',
                ],
                [
                    'name' => 'inventory.categories.view',
                    'description' => 'Permite consultar categorías.',
                    'module' => 'inventory',
                ],
                [
                    'name' => 'inventory.movements.view',
                    'description' => 'Permite consultar movimientos.',
                    'module' => 'inventory',
                ],
            ],
            $permissions
        );
    }
}
