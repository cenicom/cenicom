<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;

final readonly class CrudPermissionService implements CrudPermissionServiceInterface
{
    public function __construct(
        private CrudRegistrarInterface $registrar,
        private CrudPermissionResolverInterface $resolver,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function permissions(
        string $resource
    ): array {
        return array_map(
            fn($operation): string => $this->resolver->permission(
                $resource,
                $operation,
            ),
            $this->registrar->operations($resource),
        );
    }
}
