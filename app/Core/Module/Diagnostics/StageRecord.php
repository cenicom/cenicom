<?php

declare(strict_types=1);

namespace App\Core\Module\Diagnostics;

use Throwable;

final readonly class StageRecord
{
    public function __construct(
        public string $stage,

        public StageStatus $status,

        public float $startedAt,

        public ?float $finishedAt = null,

        public ?Throwable $exception = null,
    ) {
    }

    public function duration(): ?float
    {
        if ($this->finishedAt === null) {
            return null;
        }

        return $this->finishedAt - $this->startedAt;
    }

    public function withSuccess(float $finishedAt): self
    {
        return new self(
            stage: $this->stage,
            status: StageStatus::Success,
            startedAt: $this->startedAt,
            finishedAt: $finishedAt,
            exception: null,
        );
    }

    public function withFailure(
        Throwable $exception,
        float $finishedAt,
    ): self {
        return new self(
            stage: $this->stage,
            status: StageStatus::Failed,
            startedAt: $this->startedAt,
            finishedAt: $finishedAt,
            exception: $exception,
        );
    }

    public function withSkipped(float $finishedAt): self
    {
        return new self(
            stage: $this->stage,
            status: StageStatus::Skipped,
            startedAt: $this->startedAt,
            finishedAt: $finishedAt,
            exception: null,
        );
    }
}
