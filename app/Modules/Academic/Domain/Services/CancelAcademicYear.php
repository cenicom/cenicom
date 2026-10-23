<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Repositories\AcademicYearCancellationRepository;
use App\Modules\Academic\Repositories\AcademicYearLifecycleRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class CancelAcademicYear
{
    private const PERMISSION = 'academic_year.cancel';
    private const MAX_REASON_LENGTH = 1000;
    private const TRANSACTION_ATTEMPTS = 3;

    public function __construct(
        private InstitutionRepositoryInterface $institutions,
        private AcademicYearLifecycleRepository $years,
        private AcademicYearCancellationRepository $cancellations,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    /** Cancel a draft academic year while preserving its historical record. */
    public function execute(string $academicYearId, string $reason): void
    {
        $this->authorizer->assertCan(self::PERMISSION);

        $normalizedReason = trim($reason);
        if ($normalizedReason === '') {
            throw new DomainException('La justificación de cancelación es obligatoria.');
        }

        if (mb_strlen($normalizedReason) > self::MAX_REASON_LENGTH) {
            throw new DomainException('La justificación de cancelación no puede superar 1000 caracteres.');
        }

        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use (
            $academicYearId,
            $normalizedReason,
            $actorId,
            $actorName,
        ): void {
            $institutionId = $this->years->institutionIdForYear($academicYearId);

            if ($institutionId === null) {
                throw new DomainException('El año lectivo indicado no existe.');
            }

            // Keep the same lock order as ACA-F01/F02: institution first, year second.
            if ($this->institutions->lockForUpdate($institutionId) === null) {
                throw new DomainException('La institución del año lectivo no existe.');
            }

            $year = $this->years->findYearForUpdate($academicYearId);
            if ($year === null) {
                throw new DomainException('El año lectivo indicado no existe.');
            }

            if ($year->status !== 'draft') {
                throw new DomainException(
                    'Solo se puede cancelar un año lectivo en estado draft; los años active o closed no se cancelan.',
                );
            }

            $at = now();
            if (! $this->cancellations->cancelDraft(
                academicYearId: (string) $year->id,
                reason: $normalizedReason,
                cancelledAt: $at,
            )) {
                throw new DomainException(
                    'El año lectivo dejó de estar en estado draft y no pudo cancelarse.',
                );
            }

            // Audit is in the same transaction so a failed audit rolls back cancellation.
            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'academic_year.cancelled',
                subject: new AuditSubject(
                    type: 'academic_year',
                    id: (string) $year->id,
                ),
                metadata: [
                    'institution_id' => (string) $year->institution_id,
                    'publication_version_id' => (string) $year->publication_version_id,
                    'cycle_code' => (string) $year->cycle_code,
                    'calendar_code' => (string) $year->calendar_code,
                    'status_before' => 'draft',
                    'status_after' => 'cancelled',
                    'cancellation_reason' => $normalizedReason,
                    'cancelled_at' => $at->toDateTimeString(),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, self::TRANSACTION_ATTEMPTS);
    }
}
