<?php

declare(strict_types=1);

namespace App\Core\Audit\Persistence;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditSubject;
use App\Models\AuditLog;
use DateTimeImmutable;

final class EloquentAuditQuery implements AuditQueryInterface
{
    public function bySubject(
        AuditSubject $subject
    ): iterable {
        return AuditLog::query()
            ->where('subject_type', $subject->type)
            ->where('subject_id', $subject->id)
            ->get();
    }

    public function byAction(
        string $action
    ): iterable {
        return AuditLog::query()
            ->where('action', $action)
            ->get();
    }

    public function byActor(
        AuditActor $actor
    ): iterable {
        return AuditLog::query()
            ->where('actor_id', $actor->id)
            ->where('actor_name', $actor->name)
            ->where(
                'actor_authenticated',
                $actor->authenticated
            )
            ->get();
    }

    public function between(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): iterable {
        return AuditLog::query()
            ->whereBetween('occurred_at', [
                $from,
                $to,
            ])
            ->get();
    }
}

