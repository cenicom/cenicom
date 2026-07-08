<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use App\Core\Contracts\ExceptionInterface;
use RuntimeException;
use Throwable;

abstract class CoreException extends RuntimeException implements ExceptionInterface
{
    protected array $context = [];

    protected string $errorCode = 'CORE_ERROR';

    protected int $httpStatus = 500;

    protected bool $reportable = true;

    protected bool $loggable = true;

    public function __construct(
        string $message = '',
        array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            code: 0,
            previous: $previous
        );

        $this->context = $context;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }

    public function reportable(): bool
    {
        return $this->reportable;
    }

    public function loggable(): bool
    {
        return $this->loggable;
    }
}
