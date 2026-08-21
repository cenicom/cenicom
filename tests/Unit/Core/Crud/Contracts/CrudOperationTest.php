<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CrudOperationTest extends TestCase
{
    public function test_creates_operation(): void
    {
        $operation = new CrudOperation(
            CrudOperations::VIEW
        );

        self::assertSame(
            CrudOperations::VIEW,
            $operation->name()
        );
    }

    public function test_creates_view_operation(): void
    {
        $operation = new CrudOperation(
            CrudOperations::VIEW
        );

        self::assertSame(
            'view',
            $operation->name()
        );
    }

    public function test_creates_create_operation(): void
    {
        $operation = new CrudOperation(
            CrudOperations::CREATE
        );

        self::assertSame(
            'create',
            $operation->name()
        );
    }

    public function test_creates_update_operation(): void
    {
        $operation = new CrudOperation(
            CrudOperations::UPDATE
        );

        self::assertSame(
            'update',
            $operation->name()
        );
    }

    public function test_creates_delete_operation(): void
    {
        $operation = new CrudOperation(
            CrudOperations::DELETE
        );

        self::assertSame(
            'delete',
            $operation->name()
        );
    }

    public function test_supports_custom_operation(): void
    {
        $operation = new CrudOperation(
            'restore'
        );

        self::assertSame(
            'restore',
            $operation->name()
        );
    }

    public function test_rejects_empty_operation(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new CrudOperation('');
    }

    public function test_rejects_whitespace_only_operation(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new CrudOperation('   ');
    }
}
