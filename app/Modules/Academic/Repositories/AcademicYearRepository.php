<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Domain\ValueObjects\InstitutionAcademicProfile;
use App\Modules\Academic\Domain\ValueObjects\OfficialCalendarWindow;
use Illuminate\Support\Facades\DB;
use stdClass;

final class AcademicYearRepository
{
    public function findAvailableCalendarWindow(
        string $publicationVersionId,
    ): ?OfficialCalendarWindow {
        /** @var stdClass|null $row */
        $row = DB::table('official_calendar_publication_versions as version')
            ->join(
                'official_calendar_publications as publication',
                'publication.id',
                '=',
                'version.publication_id',
            )
            ->join(
                'academic_regulatory_authorities as authority',
                'authority.id',
                '=',
                'publication.authority_id',
            )
            ->where('version.id', $publicationVersionId)
            ->where('version.verification_status', 'available')
            ->where('publication.identity_status', 'confirmed')
            ->whereNotNull('version.calendar_start_date')
            ->whereNotNull('version.calendar_end_date')
            ->first([
                'version.calendar_start_date',
                'version.calendar_end_date',
                'publication.calendar_code',
                'authority.country_code',
                'authority.authority_code',
                'publication.jurisdiction_code',
                'publication.regime_code',
                'publication.region_code',
            ]);

        if ($row === null) {
            return null;
        }

        return new OfficialCalendarWindow(
            startDate: (string) $row->calendar_start_date,
            endDate: (string) $row->calendar_end_date,
            calendarCode: $row->calendar_code === null
                ? null
                : (string) $row->calendar_code,
            countryCode: (string) $row->country_code,
            authorityCode: (string) $row->authority_code,
            jurisdictionCode: (string) $row->jurisdiction_code,
            regimeCode: (string) $row->regime_code,
            regionCode: (string) $row->region_code,
        );
    }

    public function findInstitutionAcademicProfile(
        string $institutionId,
    ): ?InstitutionAcademicProfile {
        /** @var stdClass|null $row */
        $row = DB::table('institution_academic_profiles as profile')
            ->join('institutions', 'institutions.id', '=', 'profile.institution_id')
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

    /**
     * Cycle identity remains unique even when its prior row is cancelled.
     * This mirrors uq_ay_institution_cycle in the physical schema.
     */
    public function cycleExists(string $institutionId, string $cycleCode): bool
    {
        return DB::table('academic_years')
            ->where('institution_id', $institutionId)
            ->where('cycle_code', $cycleCode)
            ->exists();
    }

    public function hasOverlappingCalendar(
        string $institutionId,
        string $startDate,
        string $endDate,
    ): bool {
        return DB::table('academic_years as existing_year')
            ->join(
                'official_calendar_publication_versions as existing_version',
                'existing_version.id',
                '=',
                'existing_year.publication_version_id',
            )
            ->where('existing_year.institution_id', $institutionId)
            ->whereIn('existing_year.status', ['draft', 'active', 'closed'])
            // Intervals are inclusive: sharing an endpoint is an overlap.
            ->where('existing_version.calendar_start_date', '<=', $endDate)
            ->where('existing_version.calendar_end_date', '>=', $startDate)
            ->exists();
    }

    public function createDraft(
        string $id,
        AcademicYearCreateData $data,
    ): void {
        $now = now();

        DB::table('academic_years')->insert([
            'id' => $id,
            'institution_id' => $data->institutionId,
            'publication_version_id' => $data->publicationVersionId,
            'cycle_code' => $data->cycleCode,
            'calendar_code' => $data->calendarCode,
            'status' => 'draft',
            'activated_at' => null,
            'closed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
