<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Crud;

use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\CrudOperations;
use App\Modules\Institution\Crud\InstitutionCrud;
use PHPUnit\Framework\TestCase;

final class InstitutionCrudTest extends TestCase
{
    public function test_institution_crud_registers_expected_resource(): void
    {
        $registrar = new CrudRegistrar();

        $definition = new InstitutionCrud();

        $definition->register($registrar);

        self::assertSame(
            \App\Http\Controllers\InstitutionController::class,
            $registrar->controller('institutions')
        );
    }

    public function test_institution_crud_registers_expected_operations(): void
    {
        $registrar = new CrudRegistrar();

        $definition = new InstitutionCrud();

        $definition->register($registrar);

        self::assertTrue(
            $registrar->hasOperation(
                'institutions',
                CrudOperations::VIEW
            )
        );

        self::assertTrue(
            $registrar->hasOperation(
                'institutions',
                CrudOperations::CREATE
            )
        );

        self::assertTrue(
            $registrar->hasOperation(
                'institutions',
                CrudOperations::UPDATE
            )
        );

        self::assertTrue(
            $registrar->hasOperation(
                'institutions',
                CrudOperations::DELETE
            )
        );
    }

    public function test_institution_crud_registers_exactly_four_operations(): void
    {
        $registrar = new CrudRegistrar();

        $definition = new InstitutionCrud();

        $definition->register($registrar);

        $operations = $registrar->operations('institutions');

        self::assertCount(4, $operations);

        self::assertSame(
            [
                CrudOperations::VIEW,
                CrudOperations::CREATE,
                CrudOperations::UPDATE,
                CrudOperations::DELETE,
            ],
            array_map(
                static fn($operation): string => $operation->name(),
                $operations
            )
        );
    }
}
