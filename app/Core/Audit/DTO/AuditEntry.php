<?php

declare(strict_types=1);

namespace App\Core\Audit\DTO;

final readonly class AuditEntry
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public AuditActor $actor,
        public string $action,
        public ?AuditSubject $subject,
        public array $metadata,
        public string $result,
        public \DateTimeImmutable $occurredAt,
    ) {
    }
}
