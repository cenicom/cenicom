<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\DTO\CrudOperation;

final readonly class CrudPermissionResolver implements CrudPermissionResolverInterface
{
    public function permission(
        string $resource,
        CrudOperation $operation,
    ): string {
        return $resource.'.'.$operation->name();
    }
}
