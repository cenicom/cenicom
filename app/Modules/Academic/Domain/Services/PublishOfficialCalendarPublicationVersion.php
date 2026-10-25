<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Repositories\OfficialCalendarPublicationVersionRepository;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class PublishOfficialCalendarPublicationVersion
{
    private const PERMISSION = 'official_calendar_version.publish';

    public function __construct(
        private OfficialCalendarPublicationVersionRepository $versions,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $versionId): void
    {
        $this->authorizer->assertCan(self::PERMISSION);

        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use (
            $versionId,
            $actorId,
            $actorName,
        ): void {
            $version = $this->versions->findForUpdate($versionId);

            if ($version === null) {
                throw new DomainException(
                    'La versión del calendario oficial no existe.',
                );
            }

            if (
                $version->verification_status !== 'validated'
                || $version->validated_at === null
                || $version->withdrawn_at !== null
            ) {
                throw new DomainException(
                    'Solo se pueden publicar versiones oficiales en estado validated.',
                );
            }

            $publication = $this->versions->findPublicationForUpdate(
                (string) $version->publication_id,
            );

            if (
                $publication === null
                || $publication->identity_status !== 'confirmed'
                || $publication->identity_fingerprint === null
                || ! in_array($publication->calendar_code, ['A', 'B'], true)
            ) {
                throw new DomainException(
                    'La publicación debe tener identidad confirmada y calendario A/B resuelto para publicar la versión.',
                );
            }

            $startDate = $version->calendar_start_date;
            $endDate = $version->calendar_end_date;

            if (
                $startDate === null
                || $endDate === null
                || (string) $startDate === ''
                || (string) $endDate === ''
                || (string) $startDate > (string) $endDate
            ) {
                throw new DomainException(
                    'La versión debe conservar fechas oficiales de inicio y fin válidas para publicarse.',
                );
            }

            $sourceReference = trim((string) $version->source_reference);
            $sourceUrl = trim((string) $version->source_url);
            $documentSha256 = trim((string) $version->document_sha256);

            if (
                $sourceReference === ''
                || ($sourceUrl === '' && $documentSha256 === '')
            ) {
                throw new DomainException(
                    'La versión requiere una referencia documental y una URL oficial o huella SHA-256 para publicarse.',
                );
            }

            $at = now();

            if (! $this->versions->transitionValidatedToAvailable(
                $versionId,
                $at,
            )) {
                throw new DomainException(
                    'La versión cambió de estado o dejó de cumplir las condiciones de publicación.',
                );
            }

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'official_calendar_version.published',
                subject: new AuditSubject(
                    type: 'official_calendar_publication_version',
                    id: $versionId,
                ),
                metadata: [
                    'publication_id' => (string) $version->publication_id,
                    'publication_identity_status' => 'confirmed',
                    'publication_calendar_code' => (string) $publication->calendar_code,
                    'version_number' => (int) $version->version_number,
                    'status_before' => 'validated',
                    'status_after' => 'available',
                    'source_locator_type' => $sourceUrl !== ''
                        ? 'url'
                        : 'sha256',
                    'published_at' => $at->toDateTimeString(),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }
}
