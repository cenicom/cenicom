<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile;
use Illuminate\Support\Facades\DB;

final class InstitutionAcademicProfileRepository
{
    public function findForInstitution(
        string $institutionId,
    ): ?InstitutionAcademicProfile {
        $row = DB::table('institution_academic_profiles as profile')
            ->join(
                'institutions',
                'institutions.id',
                '=',
                'profile.institution_id',
            )
            ->where('profile.institution_id', $institutionId)
            ->first([
                'profile.country_code',
                'profile.authority_code',
                'profile.jurisdiction_code',
                'profile.regime_code',
                'profile.region_code',
                'profile.calendar_code',
                'institutions.official_registration_country',
            ]);

        if ($row === null) {
            return null;
        }

        return new InstitutionAcademicProfile(
            countryCode: (string) $row->country_code,
            authorityCode: (string) $row->authority_code,
            jurisdictionCode: (string) $row->jurisdiction_code,
            regimeCode: (string) $row->regime_code,
            regionCode: (string) $row->region_code,
            calendarCode: (string) $row->calendar_code,
            registeredCountryCode: $row->official_registration_country === null
                ? null
                : (string) $row->official_registration_country,
        );
    }

    public function officialRegistrationCountry(
        string $institutionId,
    ): ?string {
        $country = DB::table('institutions')
            ->where('id', $institutionId)
            ->value('official_registration_country');

        return $country === null ? null : (string) $country;
    }

    public function authorityExists(
        string $countryCode,
        string $authorityCode,
    ): bool {
        return DB::table('academic_regulatory_authorities')
            ->where('country_code', $countryCode)
            ->where('authority_code', $authorityCode)
            ->exists();
    }

    public function hasActiveOrClosedAcademicYear(
        string $institutionId,
    ): bool {
        return DB::table('academic_years')
            ->where('institution_id', $institutionId)
            ->whereIn('status', ['active', 'closed'])
            ->exists();
    }

    public function save(
        string $institutionId,
        InstitutionAcademicProfile $profile,
    ): void {
        $table = DB::table('institution_academic_profiles');
        $exists = (clone $table)
            ->where('institution_id', $institutionId)
            ->exists();

        $now = now();

        $attributes = [
            'country_code' => $profile->countryCode,
            'authority_code' => $profile->authorityCode,
            'jurisdiction_code' => $profile->jurisdictionCode,
            'regime_code' => $profile->regimeCode,
            'region_code' => $profile->regionCode,
            'calendar_code' => $profile->calendarCode,
            'updated_at' => $now,
        ];

        if ($exists) {
            $table->where('institution_id', $institutionId)
                ->update($attributes);

            return;
        }

        $table->insert([
            'institution_id' => $institutionId,
            ...$attributes,
            'created_at' => $now,
        ]);
    }
}
