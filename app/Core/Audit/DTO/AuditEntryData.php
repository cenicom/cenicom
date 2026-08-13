<?php

declare(strict_types=1);

namespace App\Core\Audit\DTO;

use DateTimeImmutable;

final readonly class AuditEntryData
{
    public function __construct(
        public ?string $actorId,
        public ?string $actorName,
        public bool $actorAuthenticated,
        public string $action,
        public ?string $subjectType,
        public ?string $subjectId,
        public array $metadata,
        public string $result,
        public DateTimeImmutable $occurredAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}
