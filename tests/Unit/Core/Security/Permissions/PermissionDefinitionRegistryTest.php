<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\PermissionDefinitionRegistry;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionRegistryTest extends TestCase
{
    public function test_registry_starts_empty(): void
    {
        $registry = new PermissionDefinitionRegistry();

        self::assertSame([], $registry->definitions());
    }

    public function test_registers_definition(): void
    {
        $registry = new PermissionDefinitionRegistry();

        $definition = 'Tests\\Fixtures\\Permissions\\TestPermissionDefinition';

        $registry->add($definition);

        self::assertSame(
            [$definition],
            $registry->definitions()
        );
    }

    public function test_registers_multiple_definitions(): void
    {
        $registry = new PermissionDefinitionRegistry();

        $first = 'Tests\\Fixtures\\Permissions\\FirstPermissionDefinition';
        $second = 'Tests\\Fixtures\\Permissions\\SecondPermissionDefinition';

        $registry->add($first);
        $registry->add($second);

        self::assertSame(
            [$first, $second],
            $registry->definitions()
        );
    }

    public function test_clear_removes_all_definitions(): void
    {
        $registry = new PermissionDefinitionRegistry();

        $registry->add(
            'Tests\\Fixtures\\Permissions\\TestPermissionDefinition'
        );

        $registry->clear();

        self::assertSame([], $registry->definitions());
    }
}
