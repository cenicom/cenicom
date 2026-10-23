<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Repositories\AcademicYearLifecycleRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CompleteAcademicYearEvent
{
    private const PERMISSION = 'academic_year.complete_milestone';

    public function __construct(
        private InstitutionRepositoryInterface $institutions,
        private AcademicYearLifecycleRepository $years,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    /** Records that one scheduled prerequisite has actually been fulfilled. */
    public function execute(string $academicYearId, string $eventId): string
    {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        return DB::transaction(function () use ($academicYearId, $eventId, $actorId, $actorName): string {
            $institutionId = $this->years->institutionIdForYear($academicYearId);

            if ($institutionId === null) {
                throw new DomainException('El año lectivo indicado no existe.');
            }

            if ($this->institutions->lockForUpdate($institutionId) === null) {
                throw new DomainException('La institución del año lectivo no existe.');
            }

            $year = $this->years->findYearForUpdate($academicYearId);

            if ($year === null) {
                throw new DomainException('El año lectivo indicado no existe.');
            }

            if (! in_array($year->status, ['draft', 'active'], true)) {
                throw new DomainException('Solo se pueden registrar hitos en un año lectivo draft o active.');
            }

            $event = $this->years->findEventInVersion(
                $eventId,
                (string) $year->publication_version_id,
            );

            if ($event === null) {
                throw new DomainException('El hito no pertenece a la versión oficial asignada a este año lectivo.');
            }

            if (! (bool) $event->activation_prerequisite && ! (bool) $event->closure_prerequisite) {
                throw new DomainException('El hito no está declarado como prerrequisito de activación ni de cierre.');
            }

            if ($this->years->completionExists((string) $year->id, (string) $event->id)) {
                throw new DomainException('El cumplimiento de este hito ya está registrado.');
            }

            $completionId = (string) Str::ulid();
            $at = now();
            $this->years->recordCompletion(
                id: $completionId,
                publicationVersionId: (string) $year->publication_version_id,
                academicYearId: (string) $year->id,
                eventId: (string) $event->id,
                completedBy: $actorId,
                completedAt: $at,
            );

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'academic_year.milestone_completed',
                subject: new AuditSubject(type: 'academic_year_event_completion', id: $completionId),
                metadata: [
                    'academic_year_id' => (string) $year->id,
                    'publication_version_id' => (string) $year->publication_version_id,
                    'event_id' => (string) $event->id,
                    'event_code' => (string) $event->code,
                    'event_title' => (string) $event->title,
                    'activation_prerequisite' => (bool) $event->activation_prerequisite,
                    'closure_prerequisite' => (bool) $event->closure_prerequisite,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));

            return $completionId;
        }, 3);
    }
}
