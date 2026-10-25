<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Repositories\OfficialCalendarPublicationIdentityRepository;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use JsonException;
use stdClass;

final readonly class ConfirmOfficialCalendarPublicationIdentity
{
    private const PERMISSION = 'official_calendar_publication.confirm_identity';

    public function __construct(
        private OfficialCalendarPublicationIdentityRepository $publications,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(string $publicationId): void
    {
        $this->authorizer->assertCan(self::PERMISSION);

        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();
        $fingerprint = null;

        try {
            DB::transaction(function () use (
                $publicationId,
                $actorId,
                $actorName,
                &$fingerprint,
            ): void {
                $publication = $this->publications->findForUpdate(
                    $publicationId,
                );

                if ($publication === null) {
                    throw new DomainException(
                        'La publicación del calendario oficial no existe.',
                    );
                }

                if (
                    $publication->identity_status !== 'unresolved'
                    || $publication->identity_fingerprint !== null
                ) {
                    throw new DomainException(
                        'Solo se puede confirmar una publicación con identidad unresolved y sin huella.',
                    );
                }

                if ($this->publications->hasImmutableVersions($publicationId)) {
                    throw new DomainException(
                        'No se puede confirmar la identidad: la publicación ya tiene versiones validadas o posteriores.',
                    );
                }

                $authority = $this->publications->findAuthorityIdentity(
                    (string) $publication->authority_id,
                );

                if ($authority === null) {
                    throw new DomainException(
                        'La publicación no tiene una autoridad reguladora válida.',
                    );
                }

                $identity = $this->canonicalIdentity(
                    $publication,
                    $authority,
                );

                $fingerprint = hash(
                    'sha256',
                    json_encode(
                        $identity,
                        JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                            | JSON_THROW_ON_ERROR,
                    ),
                );

                if (
                    $this->publications->fingerprintExistsForOtherPublication(
                        $fingerprint,
                        $publicationId,
                    )
                ) {
                    throw new DomainException(
                        'Ya existe otra publicación oficial con la misma identidad canónica.',
                    );
                }

                $at = now();

                if (! $this->publications->confirmIdentity(
                    $publicationId,
                    $fingerprint,
                    $at,
                )) {
                    throw new DomainException(
                        'La publicación cambió durante la confirmación o dejó de cumplir sus condiciones.',
                    );
                }

                $this->auditRecorder->record(new AuditEntry(
                    actor: new AuditActor(
                        id: $actorId,
                        name: $actorName,
                        authenticated: true,
                    ),
                    action: 'official_calendar_publication.identity_confirmed',
                    subject: new AuditSubject(
                        type: 'official_calendar_publication',
                        id: $publicationId,
                    ),
                    metadata: [
                        'authority_id' => (string) $publication->authority_id,
                        'identity_status_before' => 'unresolved',
                        'identity_status_after' => 'confirmed',
                        'identity_fingerprint' => $fingerprint,
                        'calendar_code' => $identity['calendar_code'],
                    ],
                    result: 'success',
                    occurredAt: $at->toDateTimeImmutable(),
                ));
            }, 3);
        } catch (QueryException $exception) {
            // La restricción UNIQUE de la BD protege también las carreras concurrentes.
            if (
                $fingerprint !== null
                && $this->publications->fingerprintExistsForOtherPublication(
                    $fingerprint,
                    $publicationId,
                )
            ) {
                throw new DomainException(
                    'Ya existe otra publicación oficial con la misma identidad canónica.',
                    previous: $exception,
                );
            }

            throw $exception;
        } catch (JsonException $exception) {
            throw new DomainException(
                'No se pudo construir la representación canónica de la identidad oficial.',
                previous: $exception,
            );
        }
    }

    /**
     * La representación canónica conserva el orden de sus claves.
     *
     * Las dimensiones de código se recortan, pero conservan su
     * capitalización, excepto el país y el calendario A/B.
     * El número documental vacío se trata como nulo.
     *
     * @return array{
     *     country_code: string,
     *     authority_code: string,
     *     document_type: string,
     *     document_number: ?string,
     *     document_year: ?int,
     *     reference_period: string,
     *     jurisdiction_code: string,
     *     regime_code: string,
     *     region_code: string,
     *     calendar_code: string
     * }
     */
    private function canonicalIdentity(
        stdClass $publication,
        stdClass $authority,
    ): array {
        $countryCode = strtoupper(trim((string) $authority->country_code));
        $authorityCode = trim((string) $authority->authority_code);
        $documentType = trim((string) $publication->document_type);
        $documentNumber = trim((string) ($publication->document_number ?? ''));
        $referencePeriod = trim((string) $publication->reference_period);
        $jurisdictionCode = trim((string) $publication->jurisdiction_code);
        $regimeCode = trim((string) $publication->regime_code);
        $regionCode = trim((string) $publication->region_code);
        $calendarCode = strtoupper(trim((string) ($publication->calendar_code ?? '')));

        if (preg_match('/^[A-Z]{2}$/D', $countryCode) !== 1) {
            throw new DomainException(
                'La autoridad reguladora debe tener un código de país ISO de dos letras.',
            );
        }

        $requiredFields = [
            'código de autoridad' => $authorityCode,
            'tipo de documento' => $documentType,
            'período de referencia' => $referencePeriod,
            'jurisdicción' => $jurisdictionCode,
            'régimen educativo' => $regimeCode,
            'región' => $regionCode,
        ];

        foreach ($requiredFields as $label => $value) {
            if ($value === '') {
                throw new DomainException(
                    sprintf('Falta el %s requerido para confirmar la identidad oficial.', $label),
                );
            }
        }

        if (! in_array($calendarCode, ['A', 'B'], true)) {
            throw new DomainException(
                'La publicación debe definir el calendario A o B antes de confirmar su identidad.',
            );
        }

        return [
            'country_code' => $countryCode,
            'authority_code' => $authorityCode,
            'document_type' => $documentType,
            'document_number' => $documentNumber === ''
                ? null
                : $documentNumber,
            'document_year' => $publication->document_year === null
                ? null
                : (int) $publication->document_year,
            'reference_period' => $referencePeriod,
            'jurisdiction_code' => $jurisdictionCode,
            'regime_code' => $regimeCode,
            'region_code' => $regionCode,
            'calendar_code' => $calendarCode,
        ];
    }
}
