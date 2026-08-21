<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionRegistrarInterface;
use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Crud\CrudPermissionRegistrar;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\DTO\PermissionDefinition;
use Mockery;
use PHPUnit\Framework\TestCase;

final class CrudPermissionRegistrarTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_implements_contract(): void
    {
        $service = new CrudPermissionRegistrar(
            Mockery::mock(CrudPermissionServiceInterface::class),
            Mockery::mock(PermissionRegistrarInterface::class),
        );

        self::assertInstanceOf(
            CrudPermissionRegistrarInterface::class,
            $service
        );
    }

    public function test_registers_permissions_from_resource(): void
    {
        $permissions = Mockery::mock(
            CrudPermissionServiceInterface::class
        );

        $registrar = Mockery::mock(
            PermissionRegistrarInterface::class
        );

        $permissions
            ->expects('permissions')
            ->with('users')
            ->andReturn([
                'users.view',
                'users.create',
            ]);

        $registrar
            ->expects('register')
            ->with('users.view', '', null)
            ->andReturn(
                new PermissionDefinition('users.view')
            );

        $registrar
            ->expects('register')
            ->with('users.create', '', null)
            ->andReturn(
                new PermissionDefinition('users.create')
            );

        $service = new CrudPermissionRegistrar(
            $permissions,
            $registrar,
        );

        self::assertSame(
            [
                'users.view',
                'users.create',
            ],
            $service->register('users')
        );
    }

    public function test_registers_multiple_permissions_with_module(): void
    {
        $permissions = Mockery::mock(
            CrudPermissionServiceInterface::class
        );

        $registrar = Mockery::mock(
            PermissionRegistrarInterface::class
        );

        $permissions
            ->expects('permissions')
            ->with('inventory')
            ->andReturn([
                'inventory.view',
                'inventory.update',
                'inventory.delete',
            ]);

        $registrar
            ->expects('register')
            ->with('inventory.view', '', 'Inventory')
            ->andReturn(
                new PermissionDefinition(
                    'inventory.view',
                    '',
                    'Inventory'
                )
            );

        $registrar
            ->expects('register')
            ->with('inventory.update', '', 'Inventory')
            ->andReturn(
                new PermissionDefinition(
                    'inventory.update',
                    '',
                    'Inventory'
                )
            );

        $registrar
            ->expects('register')
            ->with('inventory.delete', '', 'Inventory')
            ->andReturn(
                new PermissionDefinition(
                    'inventory.delete',
                    '',
                    'Inventory'
                )
            );

        $service = new CrudPermissionRegistrar(
            $permissions,
            $registrar,
        );

        self::assertSame(
            [
                'inventory.view',
                'inventory.update',
                'inventory.delete',
            ],
            $service->register(
                'inventory',
                'Inventory'
            )
        );
    }

    public function test_returns_empty_array_when_resource_has_no_permissions(): void
    {
        $permissions = Mockery::mock(
            CrudPermissionServiceInterface::class
        );

        $registrar = Mockery::mock(
            PermissionRegistrarInterface::class
        );

        $permissions
            ->expects('permissions')
            ->with('users')
            ->andReturn([]);

        $registrar
            ->shouldNotReceive('register');

        $service = new CrudPermissionRegistrar(
            $permissions,
            $registrar,
        );

        self::assertSame(
            [],
            $service->register('users')
        );
    }
}
