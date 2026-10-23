<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Repositories\AcademicYearRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DateTimeImmutable;

final readonly class AcademicYearCreator
{
    private const PERMISSION = 'academic_year.create';
    private const TRANSACTION_ATTEMPTS = 3;

    public function __construct(
        private InstitutionRepositoryInterface $institutions,
        private AcademicYearRepository $years,
        private OfficialCalendarCompatibilityValidator $compatibility,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
        private IdentityInterface $identity,
    ) {
    }

    /**
     * Creates a draft after locking the institution and validating its official interval.
     * The returned value is the new academic_years ULID.
     */
    public function execute(AcademicYearCreateData $data): string
    {
        // Authorization must precede the transaction and every persistent write.
        $this->authorizer->assertCan(self::PERMISSION);

        return DB::transaction(function () use ($data): string {
            // The institution row is the serialization point for concurrent creates.
            $institution = $this->institutions->lockForUpdate($data->institutionId);

            if ($institution === null) {
                throw new DomainException('La institución indicada no existe.');
            }

            $window = $this->years->findAvailableCalendarWindow(
                $data->publicationVersionId,
            );

            if ($window === null) {
                throw new DomainException(
                    'La versión oficial debe estar disponible, tener identidad confirmada y fechas válidas.',
                );
            }

            $institutionProfile = $this->years->findInstitutionAcademicProfile(
                $data->institutionId,
            );

            if ($institutionProfile === null) {
                throw new DomainException(
                    'La institución debe tener un perfil académico canónico antes de asignar una publicación oficial.',
                );
            }

            $this->compatibility->assertCompatible(
                institution: $institutionProfile,
                publication: $window,
                requestedCalendarCode: $data->calendarCode,
            );

            if ($this->years->cycleExists($data->institutionId, $data->cycleCode)) {
                throw new DomainException(
                    'La institución ya tiene un registro para este código de ciclo, incluso si fue cancelado.',
                );
            }

            if ($this->years->hasOverlappingCalendar(
                institutionId: $data->institutionId,
                startDate: $window->startDate,
                endDate: $window->endDate,
            )) {
                throw new DomainException(
                    sprintf(
                        'El calendario %s–%s se solapa con otro año lectivo de la institución.',
                        $window->startDate,
                        $window->endDate,
                    ),
                );
            }

            $id = (string) Str::ulid();
            $this->years->createDraft($id, $data);

            // Audit persistence is deliberately inside the same transaction:
            // an audit failure must roll back the academic-year insert.
            $this->auditRecorder->record(
                new AuditEntry(
                    actor: new AuditActor(
                        id: $this->identity->id(),
                        name: $this->identity->name(),
                        authenticated: $this->identity->authenticated(),
                    ),
                    action: 'academic_year.created',
                    subject: new AuditSubject(
                        type: 'academic_year',
                        id: $id,
                    ),
                    metadata: [
                        'institution_id' => $data->institutionId,
                        'publication_version_id' => $data->publicationVersionId,
                        'cycle_code' => $data->cycleCode,
                        'calendar_code' => $data->calendarCode,
                        'compatibility_country_code' => $institutionProfile->countryCode,
                        'compatibility_authority_code' => $institutionProfile->authorityCode,
                        'compatibility_jurisdiction_code' => $institutionProfile->jurisdictionCode,
                        'compatibility_regime_code' => $institutionProfile->regimeCode,
                        'compatibility_region_code' => $institutionProfile->regionCode,
                        'calendar_start_date' => $window->startDate,
                        'calendar_end_date' => $window->endDate,
                        'status' => 'draft',
                    ],
                    result: 'success',
                    occurredAt: new DateTimeImmutable(),
                ),
            );

            return $id;
        }, self::TRANSACTION_ATTEMPTS);
    }
}
