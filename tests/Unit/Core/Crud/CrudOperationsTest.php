<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\CrudOperations;
use PHPUnit\Framework\TestCase;

final class CrudOperationsTest extends TestCase
{
    public function test_view_constant(): void
    {
        self::assertSame(
            'view',
            CrudOperations::VIEW
        );
    }

    public function test_create_constant(): void
    {
        self::assertSame(
            'create',
            CrudOperations::CREATE
        );
    }

    public function test_update_constant(): void
    {
        self::assertSame(
            'update',
            CrudOperations::UPDATE
        );
    }

    public function test_delete_constant(): void
    {
        self::assertSame(
            'delete',
            CrudOperations::DELETE
        );
    }
}
