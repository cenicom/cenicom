<?php

declare(strict_types=1);

namespace Tests\Fixtures\Crud;

use App\Core\Crud\Contracts\CrudDefinitionInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;

final class TestCrudDefinition implements CrudDefinitionInterface
{
    public function register(
        CrudRegistrarInterface $crud
    ): void {
        $crud->register(
            'tests',
            TestCrudController::class
        );
    }
}
