<?php

declare(strict_types=1);

namespace App\Core\Crud\Enums;

enum CrudOperationType: string
{
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
