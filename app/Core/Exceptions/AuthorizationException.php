<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class AuthorizationException extends CoreException
{
    protected string $errorCode = 'AUTHORIZATION_ERROR';

    protected int $httpStatus = 403;

    protected bool $reportable = false;
}
