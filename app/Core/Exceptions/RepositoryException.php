<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class RepositoryException extends CoreException
{
    protected string $errorCode = 'REPOSITORY_ERROR';

    protected int $httpStatus = 500;
}
