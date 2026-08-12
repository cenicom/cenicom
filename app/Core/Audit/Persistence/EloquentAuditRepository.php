<?php

declare(strict_types=1);

namespace App\Core\Audit\Persistence;

use App\Core\Audit\Contracts\AuditRepositoryInterface;
use App\Core\Audit\DTO\AuditEntry;
use App\Models\AuditLog;

final class EloquentAuditRepository implements AuditRepositoryInterface
{
    public function store(AuditEntry $entry): void
    {
        AuditLog::query()->create([
            'actor_id' => $entry->actor->id,
            'actor_name' => $entry->actor->name,
            'actor_authenticated' => $entry->actor->authenticated,
            'action' => $entry->action,
            'subject_type' => $entry->subject?->type,
            'subject_id' => $entry->subject?->id,
            'metadata' => $entry->metadata,
            'result' => $entry->result,
            'occurred_at' => $entry->occurredAt,
        ]);
    }
}
