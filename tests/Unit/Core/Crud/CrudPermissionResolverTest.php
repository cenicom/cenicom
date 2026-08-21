<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\CrudPermissionResolver;
use App\Core\Crud\DTO\CrudOperation;
use PHPUnit\Framework\TestCase;

final class CrudPermissionResolverTest extends TestCase
{
    private CrudPermissionResolverInterface $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new CrudPermissionResolver();
    }

    public function test_implements_contract(): void
    {
        $this->assertInstanceOf(
            CrudPermissionResolverInterface::class,
            $this->resolver,
        );
    }

    public function test_resolves_view_permission(): void
    {
        $this->assertSame(
            'users.view',
            $this->resolver->permission(
                'users',
                new CrudOperation(CrudOperations::VIEW),
            ),
        );
    }

    public function test_resolves_create_permission(): void
    {
        $this->assertSame(
            'users.create',
            $this->resolver->permission(
                'users',
                new CrudOperation(CrudOperations::CREATE),
            ),
        );
    }

    public function test_resolves_update_permission(): void
    {
        $this->assertSame(
            'users.update',
            $this->resolver->permission(
                'users',
                new CrudOperation(CrudOperations::UPDATE),
            ),
        );
    }

    public function test_resolves_delete_permission(): void
    {
        $this->assertSame(
            'users.delete',
            $this->resolver->permission(
                'users',
                new CrudOperation(CrudOperations::DELETE),
            ),
        );
    }

    public function test_preserves_resource_namespace(): void
    {
        $this->assertSame(
            'inventory.products.view',
            $this->resolver->permission(
                'inventory.products',
                new CrudOperation(CrudOperations::VIEW),
            ),
        );
    }
}
