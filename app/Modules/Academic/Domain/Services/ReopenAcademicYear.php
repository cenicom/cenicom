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

final readonly class ReopenAcademicYear
{
    private const PERMISSION = 'academic_year.reopen';

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

        DB::transaction(function () use (
            $academicYearId,
            $actorId,
            $actorName,
        ): void {
            $institutionId = $this->years->institutionIdForYear(
                $academicYearId,
            );

            if ($institutionId === null) {
                throw new DomainException(
                    'El año lectivo indicado no existe.',
                );
            }

            if ($this->institutions->lockForUpdate($institutionId) === null) {
                throw new DomainException(
                    'La institución del año lectivo no existe.',
                );
            }

            $year = $this->years->findYearForUpdate($academicYearId);

            if ($year === null) {
                throw new DomainException(
                    'El año lectivo indicado no existe.',
                );
            }

            if ($year->status !== 'closed') {
                throw new DomainException(
                    'Solo se puede reabrir un año lectivo en estado closed.',
                );
            }

            if ($year->closed_at === null || $year->activated_at === null) {
                throw new DomainException(
                    'El año lectivo cerrado no conserva las fechas de ciclo de vida requeridas.',
                );
            }

            $closedAtBefore = (string) $year->closed_at;
            $activatedAtBefore = (string) $year->activated_at;
            $at = now();

            if (! $this->years->transitionClosedToActive(
                (string) $year->id,
                $at,
            )) {
                throw new DomainException(
                    'El año lectivo cambió de estado y no se pudo reabrir.',
                );
            }

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'academic_year.reopened',
                subject: new AuditSubject(
                    type: 'academic_year',
                    id: (string) $year->id,
                ),
                metadata: [
                    'institution_id' => (string) $year->institution_id,
                    'publication_version_id' => (string) $year->publication_version_id,
                    'cycle_code' => (string) $year->cycle_code,
                    'calendar_code' => (string) $year->calendar_code,
                    'status_before' => 'closed',
                    'status_after' => 'active',
                    'activated_at_preserved' => $activatedAtBefore,
                    'closed_at_before' => $closedAtBefore,
                    'closed_at_after' => null,
                    'reopened_at' => $at->toDateTimeString(),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }
}
