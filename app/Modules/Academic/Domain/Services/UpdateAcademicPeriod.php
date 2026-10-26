<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Domain\DTO\AcademicPeriodData;
use App\Modules\Academic\Repositories\AcademicPeriodRepository;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class UpdateAcademicPeriod
{
    private const PERMISSION = 'academic_period.update';

    public function __construct(
        private AcademicPeriodRepository $periods,
        private AcademicPeriodDataValidator $validator,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $periodId, AcademicPeriodData $data): void
    {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use (
            $periodId,
            $data,
            $actorId,
            $actorName,
        ): void {
            $publicationVersionId = $this->periods->publicationVersionIdForPeriod($periodId);

            if ($publicationVersionId === null) {
                throw new DomainException('El período académico indicado no existe.');
            }

            // Todas las escrituras de estructura bloquean primero la versión oficial.
            $version = $this->periods->findPublicationVersionForUpdate($publicationVersionId);

            if ($version === null) {
                throw new DomainException('La versión oficial del período no existe.');
            }

            if ($version->verification_status !== 'draft') {
                throw new DomainException('Solo se pueden administrar períodos mientras la versión oficial esté en draft.');
            }

            $period = $this->periods->findPeriodForUpdate($periodId);

            if ($period === null || (string) $period->publication_version_id !== $publicationVersionId) {
                throw new DomainException('El período académico cambió o dejó de existir.');
            }

            $values = $this->validator->validate($data, $version);

            if ($this->periods->findIdentityConflict(
                $publicationVersionId,
                $values['code'],
                $values['sequence_number'],
                $periodId,
            ) !== null) {
                throw new DomainException('El código o número de secuencia ya existe en otro período de esta versión oficial.');
            }

            if ($this->periods->findOverlap(
                $publicationVersionId,
                $values['start_date'],
                $values['end_date'],
                $periodId,
            ) !== null) {
                throw new DomainException('El intervalo actualizado se solapa con otro período de la misma versión oficial.');
            }

            if ($this->periods->hasEventsOutsidePeriod(
                $periodId,
                $publicationVersionId,
                $values['start_date'],
                $values['end_date'],
            )) {
                throw new DomainException('No se puede reducir o desplazar el período dejando eventos asociados fuera de sus fechas.');
            }

            $oldValues = $this->snapshot($period);
            $at = now();
            $this->periods->update($periodId, $values, $at);

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(id: $actorId, name: $actorName, authenticated: true),
                action: 'academic_period.updated',
                subject: new AuditSubject(type: 'academic_period', id: $periodId),
                metadata: [
                    'publication_version_id' => $publicationVersionId,
                    'status_of_publication_version' => 'draft',
                    'old_values' => $oldValues,
                    'new_values' => $values,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }

    /** @return array<string, mixed> */
    private function snapshot(object $period): array
    {
        return [
            'publication_version_id' => (string) $period->publication_version_id,
            'code' => (string) $period->code,
            'name' => (string) $period->name,
            'sequence_number' => (int) $period->sequence_number,
            'start_date' => (string) $period->start_date,
            'end_date' => (string) $period->end_date,
            'declared_duration' => $period->declared_duration === null
                ? null
                : (string) $period->declared_duration,
            'duration_unit' => $period->duration_unit === null
                ? null
                : (string) $period->duration_unit,
        ];
    }
}
