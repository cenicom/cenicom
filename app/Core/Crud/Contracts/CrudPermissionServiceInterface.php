<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudPermissionServiceInterface
{
    /**
     * @return array<int, string>
     */
    public function permissions(
        string $resource
    ): array;
}
