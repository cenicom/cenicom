<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;
use stdClass;

final class AcademicCalendarEventRepository
{
    public function findPublicationVersionForUpdate(string $versionId): ?stdClass
    {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->lockForUpdate()
            ->first(['id', 'verification_status', 'calendar_start_date', 'calendar_end_date']);
    }

    public function publicationVersionIdForEvent(string $eventId): ?string
    {
        $value = DB::table('academic_calendar_events')
            ->where('id', $eventId)
            ->value('publication_version_id');

        return $value === null ? null : (string) $value;
    }

    public function findEventForUpdate(string $eventId): ?stdClass
    {
        return DB::table('academic_calendar_events')
            ->where('id', $eventId)
            ->lockForUpdate()
            ->first();
    }

    public function findPeriodForVersion(string $periodId, string $versionId): ?stdClass
    {
        return DB::table('academic_periods')
            ->where('id', $periodId)
            ->where('publication_version_id', $versionId)
            ->first(['id', 'publication_version_id', 'start_date', 'end_date']);
    }

    public function findCodeConflict(
        string $versionId,
        string $code,
        ?string $exceptEventId = null,
    ): ?stdClass {
        $query = DB::table('academic_calendar_events')
            ->where('publication_version_id', $versionId)
            ->where('code', $code);

        if ($exceptEventId !== null) {
            $query->where('id', '<>', $exceptEventId);
        }

        return $query->first(['id', 'code']);
    }

    /** @param array<string, mixed> $values */
    public function create(string $id, string $versionId, array $values, mixed $at): void
    {
        DB::table('academic_calendar_events')->insert([
            'id' => $id,
            'publication_version_id' => $versionId,
            ...$values,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    /** @param array<string, mixed> $values */
    public function update(string $eventId, array $values, mixed $at): void
    {
        DB::table('academic_calendar_events')
            ->where('id', $eventId)
            ->update([...$values, 'updated_at' => $at]);
    }
}
