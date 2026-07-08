<?php

declare(strict_types=1);

namespace App\Core\Exceptions\Business;

use App\Enums\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;

final class InvalidOperationException extends BusinessException
{
    protected string $errorCode = ExceptionCode::INVALID_OPERATION->value;

    protected int $httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;
}
