<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Domain\DTO\AcademicCalendarEventData;
use App\Modules\Academic\Repositories\AcademicCalendarEventRepository;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class UpdateAcademicCalendarEvent
{
    private const PERMISSION = 'academic_calendar_event.update';

    public function __construct(
        private AcademicCalendarEventRepository $events,
        private AcademicCalendarEventDataValidator $validator,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $eventId, AcademicCalendarEventData $data): void
    {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use ($eventId, $data, $actorId, $actorName): void {
            $versionId = $this->events->publicationVersionIdForEvent($eventId);
            if ($versionId === null) {
                throw new DomainException('El evento del calendario académico indicado no existe.');
            }

            // Todas las escrituras estructurales bloquean primero la versión oficial.
            $version = $this->events->findPublicationVersionForUpdate($versionId);
            if ($version === null) {
                throw new DomainException('La versión oficial del evento no existe.');
            }
            if ($version->verification_status !== 'draft') {
                throw new DomainException('Solo se pueden administrar eventos mientras la versión oficial esté en draft.');
            }

            $event = $this->events->findEventForUpdate($eventId);
            if ($event === null || (string) $event->publication_version_id !== $versionId) {
                throw new DomainException('El evento cambió o dejó de existir.');
            }

            $period = $data->academicPeriodId === null
                ? null
                : $this->events->findPeriodForVersion($data->academicPeriodId, $versionId);
            $values = $this->validator->validate($data, $version, $period);

            if ($this->events->findCodeConflict($versionId, $values['code'], $eventId) !== null) {
                throw new DomainException('El código del evento ya existe en otro evento de esta versión oficial.');
            }

            $oldValues = $this->snapshot($event);
            $at = now();
            $this->events->update($eventId, $values, $at);

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(id: $actorId, name: $actorName, authenticated: true),
                action: 'academic_calendar_event.updated',
                subject: new AuditSubject(type: 'academic_calendar_event', id: $eventId),
                metadata: [
                    'publication_version_id' => $versionId,
                    'status_of_publication_version' => 'draft',
                    'old_values' => $oldValues,
                    'new_values' => $values,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }

    /** @return array<string, string|bool|null> */
    private function snapshot(object $event): array
    {
        return [
            'academic_period_id' => $event->academic_period_id === null
                ? null
                : (string) $event->academic_period_id,
            'code' => (string) $event->code,
            'event_type' => (string) $event->event_type,
            'title' => (string) $event->title,
            'audience' => (string) $event->audience,
            'start_date' => (string) $event->start_date,
            'end_date' => (string) $event->end_date,
            'is_officially_required' => (bool) $event->is_officially_required,
            'activation_prerequisite' => (bool) $event->activation_prerequisite,
            'closure_prerequisite' => (bool) $event->closure_prerequisite,
            'source_reference' => $event->source_reference === null
                ? null
                : (string) $event->source_reference,
        ];
    }
}
