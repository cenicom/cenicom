<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Audit\DTO\AuditActor;
use App\Core\Audit\DTO\AuditEntry;
use App\Core\Audit\DTO\AuditSubject;
use App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile;
use App\Modules\Academic\Repositories\InstitutionAcademicProfileRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class ConfigureInstitutionAcademicProfile
{
    private const PERMISSION = 'institution_academic_profile.configure';

    public function __construct(
        private InstitutionRepositoryInterface $institutions,
        private InstitutionAcademicProfileRepository $profiles,
        private AcademicYearLifecycleAuthorizer $authorizer,
        private AuditRecorderInterface $auditRecorder,
    ) {
    }

    public function execute(
        string $institutionId,
        InstitutionAcademicProfile $requestedProfile,
    ): void {
        $this->authorizer->assertCan(self::PERMISSION);

        $actorId = $this->authorizer->actorId();
        $actorName = $this->authorizer->actorName();

        DB::transaction(function () use (
            $institutionId,
            $requestedProfile,
            $actorId,
            $actorName,
        ): void {
            if ($this->institutions->lockForUpdate($institutionId) === null) {
                throw new DomainException(
                    'No se puede configurar el perfil académico: la institución no existe.',
                );
            }

            $registeredCountry = $this->profiles
                ->officialRegistrationCountry($institutionId);

            $profile = new InstitutionAcademicProfile(
                countryCode: $requestedProfile->countryCode,
                authorityCode: $requestedProfile->authorityCode,
                jurisdictionCode: $requestedProfile->jurisdictionCode,
                regimeCode: $requestedProfile->regimeCode,
                regionCode: $requestedProfile->regionCode,
                calendarCode: $requestedProfile->calendarCode,
                registeredCountryCode: $registeredCountry,
            );

            if (
                $profile->registeredCountryCode !== null
                && $profile->registeredCountryCode !== $profile->countryCode
            ) {
                throw new DomainException(
                    'El país del perfil académico debe coincidir con el país del registro oficial de la institución.',
                );
            }

            $limits = [
                'authorityCode' => [ $profile->authorityCode, 80 ],
                'jurisdictionCode' => [ $profile->jurisdictionCode, 40 ],
                'regimeCode' => [ $profile->regimeCode, 40 ],
                'regionCode' => [ $profile->regionCode, 40 ],
            ];

            foreach ($limits as $field => [$value, $limit]) {
                if (mb_strlen($value) > $limit) {
                    throw new DomainException(sprintf(
                        'El campo %s supera el máximo permitido de %d caracteres.',
                        $field,
                        $limit,
                    ));
                }
            }

            if (! $this->profiles->authorityExists(
                $profile->countryCode,
                $profile->authorityCode,
            )) {
                throw new DomainException(
                    'La autoridad reguladora no existe para el país indicado.',
                );
            }

            $previous = $this->profiles->findForInstitution($institutionId);

            if (
                $previous !== null
                && $previous->calendarCode !== $profile->calendarCode
                && $this->profiles->hasActiveOrClosedAcademicYear($institutionId)
            ) {
                throw new DomainException(
                    'No se puede cambiar el calendario A/B mientras existan años lectivos activos o cerrados.',
                );
            }

            $this->profiles->save($institutionId, $profile);

            $at = now();
            $this->auditRecorder->record(new AuditEntry(
                actor: new AuditActor(
                    id: $actorId,
                    name: $actorName,
                    authenticated: true,
                ),
                action: $previous === null
                    ? 'institution_academic_profile.configured'
                    : 'institution_academic_profile.updated',
                subject: new AuditSubject(
                    type: 'institution_academic_profile',
                    id: $institutionId,
                ),
                metadata: [
                    'institution_id' => $institutionId,
                    'profile_before' => $previous === null
                        ? null
                        : $this->snapshot($previous),
                    'profile_after' => $this->snapshot($profile),
                ],
                result: 'success',
                occurredAt: $at->toDateTimeImmutable(),
            ));
        }, 3);
    }

    /**
     * @return array<string, string>
     */
    private function snapshot(InstitutionAcademicProfile $profile): array
    {
        return [
            'country_code' => $profile->countryCode,
            'authority_code' => $profile->authorityCode,
            'jurisdiction_code' => $profile->jurisdictionCode,
            'regime_code' => $profile->regimeCode,
            'region_code' => $profile->regionCode,
            'calendar_code' => $profile->calendarCode,
        ];
    }
}
