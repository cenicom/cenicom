<?php

declare(strict_types=1);

namespace App\Core\Exceptions\Business;

use App\Core\Exceptions\CoreException;
use Symfony\Component\HttpFoundation\Response;

abstract class BusinessException extends CoreException
{
    protected string $errorCode = 'BUSINESS_ERROR';

    protected int $httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;
}
