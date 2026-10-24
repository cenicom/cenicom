<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;
use stdClass;

final class OfficialCalendarPublicationVersionRepository
{
    public function findForUpdate(string $versionId): ?stdClass
    {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->lockForUpdate()
            ->first();
    }

    public function transitionDraftToValidated(
        string $versionId,
        mixed $validatedAt,
    ): bool {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->where('verification_status', 'draft')
            ->whereNull('validated_at')
            ->whereNull('withdrawn_at')
            ->whereNotNull('calendar_start_date')
            ->whereNotNull('calendar_end_date')
            ->whereColumn('calendar_start_date', '<=', 'calendar_end_date')
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $validatedAt,
                'updated_at' => $validatedAt,
            ]) === 1;
    }
}
