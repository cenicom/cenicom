<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionRegistryContractTest extends TestCase
{
    public function test_permission_definition_registry_interface_exists(): void
    {
        self::assertTrue(
            interface_exists(
                PermissionDefinitionRegistryInterface::class
            )
        );
    }

    public function test_permission_definition_registry_contract_declares_add_method(): void
    {
        self::assertTrue(
            method_exists(
                PermissionDefinitionRegistryInterface::class,
                'add'
            )
        );
    }

    public function test_permission_definition_registry_contract_declares_definitions_method(): void
    {
        self::assertTrue(
            method_exists(
                PermissionDefinitionRegistryInterface::class,
                'definitions'
            )
        );
    }

    public function test_permission_definition_registry_contract_declares_clear_method(): void
    {
        self::assertTrue(
            method_exists(
                PermissionDefinitionRegistryInterface::class,
                'clear'
            )
        );
    }
}
