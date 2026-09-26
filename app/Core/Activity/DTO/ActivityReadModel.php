<?php

declare(strict_types=1);

namespace App\Core\Activity\DTO;

use DateTimeImmutable;

final readonly class ActivityReadModel
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public string $eventType,
        public DateTimeImmutable $occurredAt,
        public ?string $actorId,
        public string $actorName,
        public bool $actorAuthenticated,
        public ?string $subjectType,
        public ?string $subjectId,
        public array $data,
        public string $result,
    ) {}
}
