<?php

declare(strict_types=1);

namespace App\Core\Activity\Infrastructure;

use App\Core\Activity\Contracts\RecentActivityReaderInterface;
use App\Core\Activity\DTO\ActivityReadModel;
use App\Core\Audit\Contracts\AuditQueryInterface;
use App\Core\Audit\DTO\AuditEntryData;


final readonly class AuditRecentActivityReader implements RecentActivityReaderInterface
{
    /**
     * Business activities allowed in the Dashboard activity feed.
     *
     * @var list<string>
     */
    private const BUSINESS_ACTIONS = [
        'enrollment.created',
        'payment.recorded',
        'course.created',
        'user.updated',
        'report.generated',
        'institution.created',
        'institution.updated',
    ];

    public function __construct(
        private AuditQueryInterface $auditQuery,
    ) {
    }

    /**
     * @return iterable<ActivityReadModel>
     */
    public function recent(int $limit): iterable
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException(
                'Recent activity limit must be greater than zero.'
            );
        }

        /** @var list<ActivityReadModel> $activities */
        $activities = [];

        foreach (self::BUSINESS_ACTIONS as $action) {
            foreach ($this->auditQuery->byAction($action) as $entry) {
                $activities[] = $this->toReadModel($entry);
            }
        }

        usort(
            $activities,
            static fn (
                ActivityReadModel $left,
                ActivityReadModel $right,
            ): int => $right->occurredAt <=> $left->occurredAt,
        );

        return array_slice($activities, 0, $limit);
    }

    private function toReadModel(
        AuditEntryData $entry,
    ): ActivityReadModel {
        return new ActivityReadModel(
            eventType: $entry->action,
            occurredAt: $entry->occurredAt,
            actorId: $entry->actorId,
            actorName: $entry->actorName ?? 'Sistema',
            actorAuthenticated: $entry->actorAuthenticated,
            subjectType: $entry->subjectType,
            subjectId: $entry->subjectId,
            data: $entry->metadata,
            result: $entry->result,
        );
    }
}
