<?php

declare(strict_types=1);

namespace App\Core\Exceptions\Business;

use App\Enums\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;

final class CannotDeleteException extends BusinessException
{
    protected string $errorCode = ExceptionCode::CANNOT_DELETE->value;

    protected int $httpStatus = Response::HTTP_CONFLICT;
}
