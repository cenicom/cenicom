<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class EntityNotFoundException extends CoreException
{
    protected string $errorCode = 'ENTITY_NOT_FOUND';

    protected int $httpStatus = 404;

    protected bool $reportable = false;
}
