<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class BusinessRuleException extends CoreException
{
    protected string $errorCode = 'BUSINESS_RULE';

    protected int $httpStatus = 409;

    protected bool $reportable = false;
}
