<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Factories;

use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\DTO\PermissionMatrix;
use App\Core\Generator\Factories\PermissionMatrixFactory;
use PHPUnit\Framework\TestCase;

final class PermissionMatrixFactoryTest extends TestCase
{
    public function test_build_returns_permission_matrix(): void
    {
        $matrix = PermissionMatrixFactory::build(
            'Institution'
        );

        $this->assertInstanceOf(
            PermissionMatrix::class,
            $matrix
        );
    }

    public function test_build_generates_crud_permissions(): void
    {
        $matrix = PermissionMatrixFactory::build(
            'Institution'
        );

        $permissions = $matrix->permissions();

        $this->assertCount(4, $permissions);

        $this->assertContainsOnlyInstancesOf(
            PermissionDefinition::class,
            $permissions
        );

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
            ],
            $permissionNames
        );
    }

    public function test_build_generates_custom_permissions(): void
    {
        $matrix = PermissionMatrixFactory::build(
            'Institution',
            [
                'approve',
                'export',
            ]
        );

        $permissions = $matrix->permissions();

        $this->assertCount(6, $permissions);

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
                'institution.approve',
                'institution.export',
            ],
            $permissionNames
        );
    }

    public function test_build_normalizes_module_name_and_custom_permissions(): void
    {
        $matrix = PermissionMatrixFactory::build(
            ' Institution ',
            [
                ' Approve ',
                'Export_Data',
            ]
        );

        $permissions = $matrix->permissions();

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
                'institution.approve',
                'institution.export-data',
            ],
            $permissionNames
        );
    }

    public function test_build_removes_duplicate_permissions(): void
    {
        $matrix = PermissionMatrixFactory::build(
            'Institution',
            [
                'view',
                'create',
                'VIEW',
                ' view ',
            ]
        );

        $permissions = $matrix->permissions();

        $permissionNames = array_map(
            static fn(PermissionDefinition $permission): string =>
                $permission->permission(),
            $permissions
        );

        $this->assertSame(
            [
                'institution.view',
                'institution.create',
                'institution.update',
                'institution.delete',
            ],
            $permissionNames
        );

        $this->assertCount(4, $permissions);
    }
}
