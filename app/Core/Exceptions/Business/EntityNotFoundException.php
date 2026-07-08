<?php

declare(strict_types=1);

namespace App\Core\Exceptions\Business;

use App\Enums\ExceptionCode;
use Symfony\Component\HttpFoundation\Response;

final class EntityNotFoundException extends BusinessException
{
    protected string $errorCode = ExceptionCode::ENTITY_NOT_FOUND->value;

    protected int $httpStatus = Response::HTTP_NOT_FOUND;
}
