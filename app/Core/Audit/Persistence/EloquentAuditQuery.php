<?php

declare(strict_types=1);

namespace App\Core\Audit\Persistence;

use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntryData;
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
            ->get()
            ->map(
                fn (AuditLog $audit): AuditEntryData => $this->toData($audit)
            );
    }

    public function byAction(
        string $action
    ): iterable {
        return AuditLog::query()
            ->where('action', $action)
            ->get()
            ->map(
                fn (AuditLog $audit): AuditEntryData => $this->toData($audit)
            );
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
            ->get()
            ->map(
                fn (AuditLog $audit): AuditEntryData => $this->toData($audit)
            );
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
            ->get()
            ->map(
                fn (AuditLog $audit): AuditEntryData => $this->toData($audit)
            );
    }

    public function find(int $id): ?AuditEntryData
    {
        $audit = AuditLog::query()->find($id);

        if ($audit === null) {
            return null;
        }

        return $this->toData($audit);
    }

    private function toData(AuditLog $audit): AuditEntryData
    {
        return new AuditEntryData(
            actorId: $audit->actor_id,
            actorName: $audit->actor_name,
            actorAuthenticated: $audit->actor_authenticated,
            action: $audit->action,
            subjectType: $audit->subject_type,
            subjectId: $audit->subject_id,
            metadata: $audit->metadata,
            result: $audit->result,
            occurredAt: DateTimeImmutable::createFromInterface(
                $audit->occurred_at
            ),
            createdAt: DateTimeImmutable::createFromInterface(
                $audit->created_at
            ),
            updatedAt: DateTimeImmutable::createFromInterface(
                $audit->updated_at
            ),
        );
    }
}
