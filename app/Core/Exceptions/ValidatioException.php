<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class ValidationException extends CoreException
{
    protected string $errorCode = 'VALIDATION_ERROR';

    protected int $httpStatus = 422;

    protected bool $reportable = false;
}
