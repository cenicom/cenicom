<?php

declare(strict_types=1);

namespace App\Modules\Institution\Crud;

use App\Core\Crud\Contracts\CrudDefinitionInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;

final class InstitutionCrud implements CrudDefinitionInterface
{
    public function register(
        CrudRegistrarInterface $crud
    ): void {
        $crud->register(
            'institutions',
            \App\Http\Controllers\InstitutionController::class,
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::CREATE),
                new CrudOperation(CrudOperations::UPDATE),
                new CrudOperation(CrudOperations::DELETE),
            ],
        );
    }
}
