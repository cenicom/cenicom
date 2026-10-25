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

final readonly class WithdrawOfficialCalendarPublicationVersion
{
    private const PERMISSION = 'official_calendar_version.withdraw';

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

            $statusBefore = (string) $version->verification_status;

            if (
                ! in_array($statusBefore, ['validated', 'available'], true)
                || $version->validated_at === null
                || $version->withdrawn_at !== null
            ) {
                throw new DomainException(
                    'Solo se pueden retirar versiones oficiales en estado validated o available.',
                );
            }

            $at = now();

            if (! $this->versions->transitionToWithdrawn($versionId, $at)) {
                throw new DomainException(
                    'La versión cambió de estado o dejó de cumplir las condiciones de retiro.',
                );
            }

            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: 'official_calendar_version.withdrawn',
                subject: new AuditSubject(
                    type: 'official_calendar_publication_version',
                    id: $versionId,
                ),
                metadata: [
                    'publication_id' => (string) $version->publication_id,
                    'version_number' => (int) $version->version_number,
                    'status_before' => $statusBefore,
                    'status_after' => 'withdrawn',
                    'withdrawn_at' => $at->toDateTimeString(),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }
}
