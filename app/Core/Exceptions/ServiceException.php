<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class ServiceException extends CoreException
{
    protected string $errorCode = 'SERVICE_ERROR';

    protected int $httpStatus = 500;
}
