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
use Illuminate\Support\Str;

final readonly class CreateAcademicCalendarEvent
{
    private const PERMISSION = 'academic_calendar_event.create';

    public function __construct(
        private AcademicCalendarEventRepository $events,
        private AcademicCalendarEventDataValidator $validator,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $publicationVersionId, AcademicCalendarEventData $data): string
    {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        return DB::transaction(function () use (
            $publicationVersionId,
            $data,
            $actorId,
            $actorName,
        ): string {
            $version = $this->events->findPublicationVersionForUpdate($publicationVersionId);
            if ($version === null) {
                throw new DomainException('La versión oficial indicada no existe.');
            }
            if ($version->verification_status !== 'draft') {
                throw new DomainException('Solo se pueden administrar eventos mientras la versión oficial esté en draft.');
            }

            $period = $data->academicPeriodId === null
                ? null
                : $this->events->findPeriodForVersion($data->academicPeriodId, $publicationVersionId);
            $values = $this->validator->validate($data, $version, $period);

            if ($this->events->findCodeConflict($publicationVersionId, $values['code']) !== null) {
                throw new DomainException('El código del evento ya existe en esta versión oficial.');
            }

            $eventId = (string) Str::ulid();
            $at = now();
            $this->events->create($eventId, $publicationVersionId, $values, $at);

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(id: $actorId, name: $actorName, authenticated: true),
                action: 'academic_calendar_event.created',
                subject: new AuditSubject(type: 'academic_calendar_event', id: $eventId),
                metadata: [
                    'publication_version_id' => $publicationVersionId,
                    'status_of_publication_version' => 'draft',
                    'old_values' => null,
                    'new_values' => $values,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));

            return $eventId;
        }, 3);
    }
}
