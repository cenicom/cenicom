<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\DTO;

use App\Core\Generator\DTO\PermissionDefinition;
use InvalidArgumentException;
use Tests\TestCase;

final class PermissionDefinitionTest extends TestCase
{
    public function test_constructor_preserves_required_values(): void
    {
        $permission = new PermissionDefinition(
            name: 'institution',
            action: 'view',
            permission: 'institution.view',
            group: 'institution',
        );

        self::assertSame('institution', $permission->name());
        self::assertSame('view', $permission->action());
        self::assertSame('institution.view', $permission->permission());
        self::assertSame('institution', $permission->group());
    }

    public function test_constructor_applies_expected_defaults(): void
    {
        $permission = new PermissionDefinition(
            name: 'institution',
            action: 'view',
            permission: 'institution.view',
            group: 'institution',
        );

        self::assertSame('web', $permission->guard());
        self::assertNull($permission->description());
        self::assertTrue($permission->enabled());
        self::assertTrue($permission->generatePolicy());
        self::assertTrue($permission->generateMiddleware());
        self::assertTrue($permission->generateMenu());
    }

    public function test_constructor_accepts_complete_configuration(): void
    {
        $permission = new PermissionDefinition(
            name: 'institution',
            action: 'approve',
            permission: 'institution.approve',
            group: 'institution',
            guard: 'api',
            description: 'Approve institutions',
            enabled: false,
            generatePolicy: false,
            generateMiddleware: false,
            generateMenu: false,
        );

        self::assertSame('api', $permission->guard());
        self::assertSame('Approve institutions', $permission->description());
        self::assertFalse($permission->enabled());
        self::assertFalse($permission->generatePolicy());
        self::assertFalse($permission->generateMiddleware());
        self::assertFalse($permission->generateMenu());
    }

    public function test_from_array_creates_definition_with_defaults(): void
    {
        $permission = PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
        ]);

        self::assertSame('institution', $permission->name());
        self::assertSame('view', $permission->action());
        self::assertSame('institution.view', $permission->permission());
        self::assertSame('institution', $permission->group());
        self::assertSame('web', $permission->guard());
        self::assertNull($permission->description());
        self::assertTrue($permission->enabled());
        self::assertTrue($permission->generatePolicy());
        self::assertTrue($permission->generateMiddleware());
        self::assertTrue($permission->generateMenu());
    }

    public function test_from_array_accepts_complete_configuration(): void
    {
        $permission = PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'approve',
            'permission' => 'institution.approve',
            'group' => 'institution',
            'guard' => 'api',
            'description' => 'Approve institutions',
            'enabled' => false,
            'generatePolicy' => false,
            'generateMiddleware' => false,
            'generateMenu' => false,
        ]);

        self::assertSame('api', $permission->guard());
        self::assertSame('Approve institutions', $permission->description());
        self::assertFalse($permission->enabled());
        self::assertFalse($permission->generatePolicy());
        self::assertFalse($permission->generateMiddleware());
        self::assertFalse($permission->generateMenu());
    }

    public function test_to_array_returns_all_definition_values(): void
    {
        $permission = new PermissionDefinition(
            name: 'institution',
            action: 'approve',
            permission: 'institution.approve',
            group: 'institution',
            guard: 'api',
            description: 'Approve institutions',
            enabled: false,
            generatePolicy: false,
            generateMiddleware: false,
            generateMenu: false,
        );

        self::assertSame([
            'name' => 'institution',
            'action' => 'approve',
            'permission' => 'institution.approve',
            'group' => 'institution',
            'guard' => 'api',
            'description' => 'Approve institutions',
            'enabled' => false,
            'generatePolicy' => false,
            'generateMiddleware' => false,
            'generateMenu' => false,
        ], $permission->toArray());
    }

    public function test_from_array_to_array_round_trip_preserves_definition(): void
    {
        $definition = [
            'name' => 'institution',
            'action' => 'approve',
            'permission' => 'institution.approve',
            'group' => 'institution',
            'guard' => 'api',
            'description' => 'Approve institutions',
            'enabled' => false,
            'generatePolicy' => false,
            'generateMiddleware' => true,
            'generateMenu' => false,
        ];

        $permission = PermissionDefinition::fromArray($definition);
        $restored = PermissionDefinition::fromArray($permission->toArray());

        self::assertSame($permission->toArray(), $restored->toArray());
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => '',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_empty_action(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => '',
            'permission' => 'institution.view',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_empty_permission(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => '',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_empty_group(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => '',
        ]);
    }

    public function test_rejects_invalid_guard_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
            'guard' => true,
        ]);
    }

    public function test_rejects_invalid_description_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
            'description' => 123,
        ]);
    }

    public function test_rejects_invalid_boolean_configuration(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
            'enabled' => 'false',
        ]);
    }

    public function test_rejects_missing_required_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'action' => 'view',
            'permission' => 'institution.view',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_missing_required_action(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'permission' => 'institution.view',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_missing_required_permission(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'group' => 'institution',
        ]);
    }

    public function test_rejects_missing_required_group(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PermissionDefinition::fromArray([
            'name' => 'institution',
            'action' => 'view',
            'permission' => 'institution.view',
        ]);
    }
}
