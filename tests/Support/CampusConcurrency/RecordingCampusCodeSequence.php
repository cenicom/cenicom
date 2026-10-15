<?php

declare(strict_types=1);

namespace Tests\Support\CampusConcurrency;

use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use App\Modules\Campus\Infrastructure\Identity\CampusCodeSequence;
use Throwable;

final readonly class RecordingCampusCodeSequence implements CampusCodeSequenceInterface
{
    public function __construct(
        private CampusCodeSequence $inner,
        private string $tracePath,
        private int $worker,
    ) {
    }

    public function next(string $institutionId): int
    {
        try {
            $value = $this->inner->next($institutionId);

            $this->record([
                'worker' => $this->worker,
                'institution_id' => $institutionId,
                'status' => 'returned',
                'value' => $value,
            ]);

            return $value;
        } catch (Throwable $exception) {
            $this->record([
                'worker' => $this->worker,
                'institution_id' => $institutionId,
                'status' => 'exception',
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @param array<string, int|string> $entry
     */
    private function record(array $entry): void
    {
        file_put_contents(
            $this->tracePath,
            json_encode(
                $entry,
                JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
            ) . PHP_EOL,
            FILE_APPEND | LOCK_EX,
        );
    }
}
