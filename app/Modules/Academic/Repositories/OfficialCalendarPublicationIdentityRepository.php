<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;
use stdClass;

final class OfficialCalendarPublicationIdentityRepository
{
    public function findForUpdate(string $publicationId): ?stdClass
    {
        return DB::table('official_calendar_publications')
            ->where('id', $publicationId)
            ->lockForUpdate()
            ->first();
    }

    public function findAuthorityIdentity(string $authorityId): ?stdClass
    {
        return DB::table('academic_regulatory_authorities')
            ->select('id', 'country_code', 'authority_code')
            ->where('id', $authorityId)
            ->first();
    }

    public function hasImmutableVersions(string $publicationId): bool
    {
        return DB::table('official_calendar_publication_versions')
            ->where('publication_id', $publicationId)
            ->whereIn('verification_status', [
                'validated',
                'available',
                'withdrawn',
            ])
            ->exists();
    }

    public function fingerprintExistsForOtherPublication(
        string $fingerprint,
        string $publicationId,
    ): bool {
        return DB::table('official_calendar_publications')
            ->where('identity_fingerprint', $fingerprint)
            ->where('id', '<>', $publicationId)
            ->exists();
    }

    public function confirmIdentity(
        string $publicationId,
        string $fingerprint,
        mixed $updatedAt,
    ): bool {
        return DB::table('official_calendar_publications')
            ->where('id', $publicationId)
            ->where('identity_status', 'unresolved')
            ->whereNull('identity_fingerprint')
            ->update([
                'identity_status' => 'confirmed',
                'identity_fingerprint' => $fingerprint,
                'updated_at' => $updatedAt,
            ]) === 1;
    }
}
