<?php

declare(strict_types=1);

namespace App\Modules\Institution\Actions;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Entity\Institution;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;

final readonly class InstitutionAction
{
    public function __construct(
        private InstitutionCreatorInterface $creator,
        private InstitutionRepositoryInterface $repository,
        private InstitutionServiceInterface $service,
        private AuditRecorderInterface $auditRecorder,
        private IdentityInterface $identity,
    ) {
    }

    public function create(array $data): Institution
    {
        $officialRegistration = null;

        if (
            isset($data['officialRegistration'])
            && is_array($data['officialRegistration'])
        ) {
            $officialRegistration = new InstitutionOfficialRegistration(
                country: $data['officialRegistration']['country'],
                authority: $data['officialRegistration']['authority'],
                value: $data['officialRegistration']['value'],
            );
        }

        $createData = new InstitutionCreateData(
            name: $data['name'],
            officialRegistration: $officialRegistration,
        );

        $institution = $this->creator->create($createData);

        $persisted = $this->repository->save($institution);

        $this->auditRecorder->record(
            new AuditEntry(
                actor: new AuditActor(
                    id: $this->identity->id(),
                    name: $this->identity->name(),
                    authenticated: $this->identity->authenticated(),
                ),
                action: 'institution.created',
                subject: new AuditSubject(
                    type: 'institution',
                    id: $persisted->id(),
                ),
                metadata: [
                    'name' => $persisted->name(),
                ],
                result: 'success',
                occurredAt: new \DateTimeImmutable(),
            ),
        );

        return $persisted;
    }

    public function update(
        int|string $id,
        array $data
    ): bool {
        if (
            isset($data['officialRegistration'])
            && is_array($data['officialRegistration'])
        ) {
            $registration = $data['officialRegistration'];

            $data['official_registration_country'] = $registration['country'];
            $data['official_registration_authority'] = $registration['authority'];
            $data['official_registration_value'] = $registration['value'];

            unset($data['officialRegistration']);
        }

        return $this->service->update($id, $data);
    }

    public function delete(
        int|string $id
    ): bool {
        return $this->service->delete($id);
    }
}
