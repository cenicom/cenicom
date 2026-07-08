<?php

declare(strict_types=1);

namespace App\Core\Exceptions\Validation;

use App\Core\Exceptions\CoreException;
use App\Enums\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;

final class InvalidDTOException extends CoreException
{
    protected string $errorCode = ExceptionCode::INVALID_DTO->value;

    protected int $httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;
}
