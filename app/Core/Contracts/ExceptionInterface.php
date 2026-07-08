<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface ExceptionInterface
{
    public function context(): array;

    public function errorCode(): string;

    public function httpStatus(): int;

    public function reportable(): bool;

    public function loggable(): bool;
}
