<?php

declare(strict_types=1);

namespace App\Core\Module\Diagnostics;

use LogicException;
use Throwable;

final class FailureTrace
{
    /**
     * @var array<string, StageRecord>
     */
    private array $records = [];

    public function start(string $stage): void
    {
        if ($stage === '') {
            throw new LogicException('Stage name cannot be empty.');
        }

        if (isset($this->records[$stage])) {
            throw new LogicException(
                sprintf('Stage [%s] has already been started.', $stage)
            );
        }

        $this->records[$stage] = new StageRecord(
            stage: $stage,
            status: StageStatus::Running,
            startedAt: microtime(true),
        );
    }

    public function finish(string $stage): void
    {
        $record = $this->requireStage($stage);

        if ($record->status !== StageStatus::Running) {
            throw new LogicException(
                sprintf(
                    'Stage [%s] cannot be finished because it is %s.',
                    $stage,
                    $record->status->value,
                )
            );
        }

        $this->records[$stage] = $record->withSuccess(
            microtime(true),
        );
    }

    public function fail(
        string $stage,
        Throwable $exception,
    ): void {
        $record = $this->requireStage($stage);

        if ($record->status !== StageStatus::Running) {
            throw new LogicException(
                sprintf(
                    'Stage [%s] cannot fail because it is %s.',
                    $stage,
                    $record->status->value,
                )
            );
        }

        $this->records[$stage] = $record->withFailure(
            exception: $exception,
            finishedAt: microtime(true),
        );
    }

    public function skip(string $stage): void
    {
        $record = $this->requireStage($stage);

        if ($record->status !== StageStatus::Running) {
            throw new LogicException(
                sprintf(
                    'Stage [%s] cannot be skipped because it is %s.',
                    $stage,
                    $record->status->value,
                )
            );
        }

        $this->records[$stage] = $record->withSkipped(
            microtime(true),
        );
    }

    /**
     * @return list<StageRecord>
     */
    public function timeline(): array
    {
        return array_values($this->records);
    }

    public function current(): ?StageRecord
    {
        if ($this->records === []) {
            return null;
        }

        $records = array_values($this->records);

        return $records[array_key_last($records)];
    }

    public function failurePoint(): ?StageRecord
    {
        foreach ($this->records as $record) {
            if ($record->status === StageStatus::Failed) {
                return $record;
            }
        }

        return null;
    }

    public function hasFailures(): bool
    {
        return $this->failurePoint() !== null;
    }

    public function totalDuration(): float
    {
        $duration = 0.0;

        foreach ($this->records as $record) {
            $duration += $record->duration() ?? 0.0;
        }

        return $duration;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function hasStage(string $stage): bool
    {
        return isset($this->records[$stage]);
    }

    public function stage(string $stage): ?StageRecord
    {
        return $this->records[$stage] ?? null;
    }

    public function clear(): void
    {
        $this->records = [];
    }

    private function requireStage(string $stage): StageRecord
    {
        if (! isset($this->records[$stage])) {
            throw new LogicException(
                sprintf('Stage [%s] has not been started.', $stage)
            );
        }

        return $this->records[$stage];
    }
}
