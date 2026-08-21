<?php

declare(strict_types=1);

namespace App\Core\Crud\DTO;

use InvalidArgumentException;

final readonly class CrudOperation
{
    public function __construct(
        private string $name,
    ) {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                'CRUD operation name cannot be empty.'
            );
        }
    }

    public function name(): string
    {
        return $this->name;
    }
}
