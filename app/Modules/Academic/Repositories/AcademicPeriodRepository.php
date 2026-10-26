<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;
use stdClass;

final class AcademicPeriodRepository
{
    public function findPublicationVersionForUpdate(string $versionId): ?stdClass
    {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->lockForUpdate()
            ->first([
                'id',
                'verification_status',
                'calendar_start_date',
                'calendar_end_date',
            ]);
    }

    public function publicationVersionIdForPeriod(string $periodId): ?string
    {
        $value = DB::table('academic_periods')
            ->where('id', $periodId)
            ->value('publication_version_id');

        return $value === null ? null : (string) $value;
    }

    public function findPeriodForUpdate(string $periodId): ?stdClass
    {
        return DB::table('academic_periods')
            ->where('id', $periodId)
            ->lockForUpdate()
            ->first();
    }

    public function findIdentityConflict(
        string $publicationVersionId,
        string $code,
        int $sequenceNumber,
        ?string $exceptPeriodId = null,
    ): ?stdClass {
        $query = DB::table('academic_periods')
            ->where('publication_version_id', $publicationVersionId)
            ->where(function ($query) use ($code, $sequenceNumber): void {
                $query->where('code', $code)
                    ->orWhere('sequence_number', $sequenceNumber);
            });

        if ($exceptPeriodId !== null) {
            $query->where('id', '<>', $exceptPeriodId);
        }

        return $query->first(['id', 'code', 'sequence_number']);
    }

    public function findOverlap(
        string $publicationVersionId,
        string $startDate,
        string $endDate,
        ?string $exceptPeriodId = null,
    ): ?stdClass {
        $query = DB::table('academic_periods')
            ->where('publication_version_id', $publicationVersionId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate);

        if ($exceptPeriodId !== null) {
            $query->where('id', '<>', $exceptPeriodId);
        }

        return $query->orderBy('sequence_number')->first([
            'id',
            'code',
            'start_date',
            'end_date',
        ]);
    }

    public function hasEventsOutsidePeriod(
        string $periodId,
        string $publicationVersionId,
        string $startDate,
        string $endDate,
    ): bool {
        return DB::table('academic_calendar_events')
            ->where('academic_period_id', $periodId)
            ->where('publication_version_id', $publicationVersionId)
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->where('start_date', '<', $startDate)
                    ->orWhere('end_date', '>', $endDate);
            })
            ->exists();
    }

    /**
     * @param array{
     *     code: string,
     *     name: string,
     *     sequence_number: int,
     *     start_date: string,
     *     end_date: string,
     *     declared_duration: string|null,
     *     duration_unit: string|null
     * } $values
     */
    public function create(
        string $id,
        string $publicationVersionId,
        array $values,
        mixed $at,
    ): void {
        DB::table('academic_periods')->insert([
            'id' => $id,
            'publication_version_id' => $publicationVersionId,
            ...$values,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    /**
     * @param array{
     *     code: string,
     *     name: string,
     *     sequence_number: int,
     *     start_date: string,
     *     end_date: string,
     *     declared_duration: string|null,
     *     duration_unit: string|null
     * } $values
     */
    public function update(string $periodId, array $values, mixed $at): void
    {
        DB::table('academic_periods')
            ->where('id', $periodId)
            ->update([
                ...$values,
                'updated_at' => $at,
            ]);
    }
}
