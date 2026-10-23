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

final readonly class AcademicYearCloser
{
    private const PERMISSION = 'academic_year.close';

    public function __construct(
        private InstitutionRepositoryInterface $institutions,
        private AcademicYearLifecycleRepository $years,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $academicYearId): void
    {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use ($academicYearId, $actorId, $actorName): void {
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

            if ($year->status !== 'active') {
                throw new DomainException('Solo se puede cerrar un año lectivo en estado active.');
            }

            // Availability can change after assignment; historical active years still
            // need to close against the dates of their linked, confirmed publication.
            $window = $this->years->officialWindow((string) $year->publication_version_id);

            if ($window === null || $window->calendar_start_date > $window->calendar_end_date) {
                throw new DomainException('No hay un intervalo oficial confirmado y válido para cerrar este año.');
            }

            $today = now()->toDateString();
            if ($today <= (string) $window->calendar_end_date) {
                throw new DomainException(
                    'El cierre solo se permite después del fin oficial del calendario ('
                    . (string) $window->calendar_end_date . ').',
                );
            }

            $incomplete = $this->years->incompletePrerequisites(
                (string) $year->id,
                (string) $year->publication_version_id,
                'closure_prerequisite',
            );

            if ($incomplete !== []) {
                $codes = implode(', ', array_map(static fn ($event): string => (string) $event->code, $incomplete));
                throw new DomainException('Faltan prerrequisitos de cierre: ' . $codes . '.');
            }

            $at = now();
            $this->years->transition((string) $year->id, 'closed', $at);

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'academic_year.closed',
                subject: new AuditSubject(type: 'academic_year', id: (string) $year->id),
                metadata: [
                    'institution_id' => (string) $year->institution_id,
                    'publication_version_id' => (string) $year->publication_version_id,
                    'cycle_code' => (string) $year->cycle_code,
                    'calendar_code' => (string) $year->calendar_code,
                    'status_before' => 'active',
                    'status_after' => 'closed',
                    'calendar_start_date' => (string) $window->calendar_start_date,
                    'calendar_end_date' => (string) $window->calendar_end_date,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }
}
