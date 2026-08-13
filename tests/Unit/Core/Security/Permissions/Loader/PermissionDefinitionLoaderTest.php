<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions\Loader;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use App\Modules\Institution\Security\InstitutionPermissionDefinition;
use App\Modules\Inventory\Security\InventoryPermissionDefinition;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionLoaderTest extends TestCase
{
    public function test_load_registers_module_definitions(): void
    {
        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $definitions = [];

        $registry
            ->expects(self::exactly(2))
            ->method('add')
            ->willReturnCallback(
                function (string $definition) use (&$definitions): void {
                    $definitions[] = $definition;
                }
            );

        $loader = new PermissionDefinitionLoader(
            $registry
        );

        $loader->load();

        self::assertSame(
            [
                InstitutionPermissionDefinition::class,
                InventoryPermissionDefinition::class,
            ],
            $definitions
        );
    }

    public function test_loader_accepts_registry_contract(): void
    {
        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $loader = new PermissionDefinitionLoader(
            $registry
        );

        self::assertInstanceOf(
            PermissionDefinitionLoader::class,
            $loader
        );
    }
}
