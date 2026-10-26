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
use Illuminate\Support\Str;

final readonly class CreateAcademicPeriod
{
    private const PERMISSION = 'academic_period.create';

    public function __construct(
        private AcademicPeriodRepository $periods,
        private AcademicPeriodDataValidator $validator,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(
        string $publicationVersionId,
        AcademicPeriodData $data,
    ): string {
        $this->authorizer->assertCan(self::PERMISSION);
        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        return DB::transaction(function () use (
            $publicationVersionId,
            $data,
            $actorId,
            $actorName,
        ): string {
            $version = $this->periods->findPublicationVersionForUpdate($publicationVersionId);

            if ($version === null) {
                throw new DomainException('La versión oficial indicada no existe.');
            }

            if ($version->verification_status !== 'draft') {
                throw new DomainException('Solo se pueden administrar períodos mientras la versión oficial esté en draft.');
            }

            $values = $this->validator->validate($data, $version);

            if ($this->periods->findIdentityConflict(
                $publicationVersionId,
                $values['code'],
                $values['sequence_number'],
            ) !== null) {
                throw new DomainException('El código o número de secuencia ya existe en esta versión oficial.');
            }

            if ($this->periods->findOverlap(
                $publicationVersionId,
                $values['start_date'],
                $values['end_date'],
            ) !== null) {
                throw new DomainException('El intervalo del período se solapa con otro período de la misma versión oficial.');
            }

            $periodId = (string) Str::ulid();
            $at = now();

            $this->periods->create($periodId, $publicationVersionId, $values, $at);

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(id: $actorId, name: $actorName, authenticated: true),
                action: 'academic_period.created',
                subject: new AuditSubject(type: 'academic_period', id: $periodId),
                metadata: [
                    'publication_version_id' => $publicationVersionId,
                    'status_of_publication_version' => 'draft',
                    'old_values' => null,
                    'new_values' => $values,
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));

            return $periodId;
        }, 3);
    }
}
