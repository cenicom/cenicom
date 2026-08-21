<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\DTO\CrudOperation;

interface CrudPermissionResolverInterface
{
    public function permission(
        string $resource,
        CrudOperation $operation,
    ): string;
}
