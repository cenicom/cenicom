<?php

declare(strict_types=1);

namespace App\Core\Module\Diagnostics;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

final readonly class StageTimeline implements IteratorAggregate, Countable
{
    /**
     * @param list<StageRecord> $records
     */
    public function __construct(
        private array $records,
    ) {
    }

    /**
     * @return list<StageRecord>
     */
    public function all(): array
    {
        return $this->records;
    }

    public function first(): ?StageRecord
    {
        return $this->records[0] ?? null;
    }

    public function last(): ?StageRecord
    {
        if ($this->records === []) {
            return null;
        }

        return $this->records[array_key_last($this->records)];
    }

    public function failurePoint(): ?StageRecord
    {
        foreach ($this->records as $record) {
            if ($record->status->isFailure()) {
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
        $total = 0.0;

        foreach ($this->records as $record) {
            $total += $record->duration() ?? 0.0;
        }

        return $total;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->records);
    }
}
