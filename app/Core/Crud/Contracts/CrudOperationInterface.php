<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudOperationInterface
{
    public function name(): string;
}
