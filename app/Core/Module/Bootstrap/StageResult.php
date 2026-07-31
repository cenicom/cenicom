<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\Bootstrap\StageResultInterface;

final readonly class StageResult implements StageResultInterface
{
    public function __construct(
        private bool $success,
        private mixed $data = null,
        private ?string $message = null,
        private array $warnings = [],
        private array $errors = [],
    ) {
    }

    public static function success(
        mixed $data = null,
        ?string $message = null,
        array $warnings = []
    ): self {
        return new self(
            true,
            $data,
            $message,
            $warnings
        );
    }

    public static function failure(
        string $message,
        array $errors = []
    ): self {
        return new self(
            false,
            null,
            $message,
            [],
            $errors
        );
    }

    public function successful(): bool
    {
        return $this->success;
    }

    public function failed(): bool
    {
        return !$this->success;
    }

    public function data(): mixed
    {
        return $this->data;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
