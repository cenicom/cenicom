<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\DTO\AuditEntry;

final readonly class AuditRecorder implements AuditRecorderInterface
{
    public function __construct(
        private AuditRepositoryInterface $repository,
    ) {
    }

    public function record(AuditEntry $entry): void
    {
        $this->repository->store($entry);
    }
}
