<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Domain\DTO\OfficialCalendarPublicationVersionCorrectionData;
use App\Modules\Academic\Repositories\OfficialCalendarPublicationVersionRepository;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

final readonly class CorrectOfficialCalendarPublicationVersion
{
    private const PERMISSION = 'official_calendar_version.correct';

    private const TRANSACTION_ATTEMPTS = 3;

    public function __construct(
        private OfficialCalendarPublicationVersionRepository $versions,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    /**
     * Crea una versión sucesora en draft y devuelve su ULID.
     */
    public function execute(
        string $versionId,
        OfficialCalendarPublicationVersionCorrectionData $data,
    ): string {
        $this->authorizer->assertCan(self::PERMISSION);

        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        return DB::transaction(function () use (
            $versionId,
            $data,
            $actorId,
            $actorName,
        ): string {
            $previous = $this->versions->findForUpdate($versionId);

            if ($previous === null) {
                throw new DomainException(
                    'La versión del calendario oficial no existe.',
                );
            }

            if (
                ! in_array(
                    $previous->verification_status,
                    ['validated', 'available', 'withdrawn'],
                    true,
                )
                || $previous->validated_at === null
            ) {
                throw new DomainException(
                    'Solo se puede corregir una versión oficial previamente validada.',
                );
            }

            $publicationId = (string) $previous->publication_id;

            // Punto de serialización para las operaciones de una publicación.
            $publication = $this->versions->findPublicationForUpdate(
                $publicationId,
            );

            if (
                $publication === null
                || $publication->identity_status !== 'confirmed'
                || $publication->identity_fingerprint === null
                || ! in_array($publication->calendar_code, ['A', 'B'], true)
            ) {
                throw new DomainException(
                    'La publicación debe conservar su identidad confirmada, huella y calendario A/B.',
                );
            }

            $latestVersionNumber = $this->versions->latestVersionNumber(
                $publicationId,
            );

            if (
                $this->versions->hasSuccessor($publicationId, $versionId)
                || $latestVersionNumber !== (int) $previous->version_number
            ) {
                throw new DomainException(
                    'Solo se puede corregir la versión más reciente que aún no tenga sucesora.',
                );
            }

            if ($latestVersionNumber >= 65535) {
                throw new DomainException(
                    'No se puede crear otra versión: se alcanzó el límite del número de versión.',
                );
            }

            $newValues = $data->toContentArray();

            $this->assertValidContent($newValues);

            $oldValues = $this->snapshot($previous);
            $newVersionId = (string) Str::ulid();
            $newVersionNumber = $latestVersionNumber + 1;
            $at = now();

            $this->versions->createDraftCorrection(
                id: $newVersionId,
                publicationId: $publicationId,
                versionNumber: $newVersionNumber,
                supersedesVersionId: $versionId,
                content: $newValues,
                at: $at,
            );

            // El alta y la auditoría forman una sola unidad transaccional.
            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'official_calendar_version.corrected',
                subject: new AuditSubject(
                    type: 'official_calendar_publication_version',
                    id: $newVersionId,
                ),
                metadata: [
                    'publication_id' => $publicationId,
                    'publication_identity_status' => 'confirmed',
                    'publication_calendar_code' => (string) $publication->calendar_code,
                    'previous_version_id' => $versionId,
                    'previous_version_number' => (int) $previous->version_number,
                    'new_version_id' => $newVersionId,
                    'new_version_number' => $newVersionNumber,
                    'status_before' => (string) $previous->verification_status,
                    'status_after' => 'draft',
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'created_at' => $at->toDateTimeString(),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));

            return $newVersionId;
        }, self::TRANSACTION_ATTEMPTS);
    }

    /**
     * @param array<string, string|null> $content
     */
    private function assertValidContent(array $content): void
    {
        if (trim($content['authority_name_snapshot']) === '') {
            throw new DomainException(
                'La nueva versión debe conservar el nombre de la autoridad.',
            );
        }

        if (trim($content['title']) === '') {
            throw new DomainException(
                'La nueva versión debe tener un título.',
            );
        }

        if (
            strlen($content['authority_name_snapshot']) > 180
            || strlen($content['title']) > 500
            || strlen((string) $content['official_version_label']) > 80
            || strlen((string) $content['source_reference']) > 200
            || strlen((string) $content['source_url']) > 2048
        ) {
            throw new DomainException(
                'Uno de los campos de la nueva versión excede la longitud permitida.',
            );
        }

        foreach ([
            'promulgated_on',
            'calendar_start_date',
            'calendar_end_date',
        ] as $field) {
            $date = $content[$field];

            if ($date !== null && ! $this->isValidDate($date)) {
                throw new DomainException(
                    "La fecha {$field} debe tener formato válido YYYY-MM-DD.",
                );
            }
        }

        $start = $content['calendar_start_date'];
        $end = $content['calendar_end_date'];

        if (($start === null) !== ($end === null)) {
            throw new DomainException(
                'Las fechas de inicio y fin deben indicarse juntas o dejarse ambas vacías.',
            );
        }

        if ($start !== null && $end !== null && $start > $end) {
            throw new DomainException(
                'La fecha de inicio no puede ser posterior a la fecha de fin.',
            );
        }

        $sha256 = $content['document_sha256'];

        if ($sha256 !== null && strlen($sha256) !== 64) {
            throw new DomainException(
                'La huella documental SHA-256 debe contener 64 caracteres.',
            );
        }
    }

    private function isValidDate(string $date): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches) !== 1) {
            return false;
        }

        return checkdate(
            (int) $matches[2],
            (int) $matches[3],
            (int) $matches[1],
        );
    }

    /**
     * @return array<string, string|null>
     */
    private function snapshot(stdClass $version): array
    {
        return [
            'official_version_label' => $version->official_version_label,
            'authority_name_snapshot' => $version->authority_name_snapshot,
            'title' => $version->title,
            'promulgated_on' => $version->promulgated_on,
            'calendar_start_date' => $version->calendar_start_date,
            'calendar_end_date' => $version->calendar_end_date,
            'source_url' => $version->source_url,
            'source_reference' => $version->source_reference,
            'document_sha256' => $version->document_sha256,
        ];
    }
}
