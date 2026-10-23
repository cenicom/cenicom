<?php

declare(strict_types=1);

namespace App\Modules\Academic\Repositories;

use Illuminate\Support\Facades\DB;
use stdClass;

final class AcademicYearLifecycleRepository
{
    public function institutionIdForYear(string $academicYearId): ?string
    {
        $value = DB::table('academic_years')
            ->where('id', $academicYearId)
            ->value('institution_id');

        return $value === null ? null : (string) $value;
    }

    public function findYearForUpdate(string $academicYearId): ?stdClass
    {
        return DB::table('academic_years')
            ->where('id', $academicYearId)
            ->lockForUpdate()
            ->first();
    }

    public function activationWindow(string $publicationVersionId): ?stdClass
    {
        return DB::table('official_calendar_publication_versions as version')
            ->join(
                'official_calendar_publications as publication',
                'publication.id',
                '=',
                'version.publication_id',
            )
            ->where('version.id', $publicationVersionId)
            ->where('version.verification_status', 'available')
            ->where('publication.identity_status', 'confirmed')
            ->whereNotNull('version.calendar_start_date')
            ->whereNotNull('version.calendar_end_date')
            ->first([
                'version.id as publication_version_id',
                'version.calendar_start_date',
                'version.calendar_end_date',
            ]);
    }

    public function officialWindow(string $publicationVersionId): ?stdClass
    {
        return DB::table('official_calendar_publication_versions as version')
            ->join(
                'official_calendar_publications as publication',
                'publication.id',
                '=',
                'version.publication_id',
            )
            ->where('version.id', $publicationVersionId)
            ->where('publication.identity_status', 'confirmed')
            ->whereNotNull('version.calendar_start_date')
            ->whereNotNull('version.calendar_end_date')
            ->first([
                'version.id as publication_version_id',
                'version.calendar_start_date',
                'version.calendar_end_date',
                'version.verification_status',
            ]);
    }

    /**
     * @return list<stdClass> Rows for prerequisite events that have not been completed
     *                        for this institution's academic-year assignment.
     */
    public function incompletePrerequisites(
        string $academicYearId,
        string $publicationVersionId,
        string $prerequisiteColumn,
    ): array {
        if (! in_array($prerequisiteColumn, ['activation_prerequisite', 'closure_prerequisite'], true)) {
            throw new \InvalidArgumentException('Columna de prerrequisito no permitida.');
        }

        return DB::table('academic_calendar_events as event')
            ->leftJoin('academic_year_event_completions as completion', function ($join) use ($academicYearId): void {
                $join->on('completion.event_id', '=', 'event.id')
                    ->where('completion.academic_year_id', '=', $academicYearId);
            })
            ->where('event.publication_version_id', $publicationVersionId)
            ->where("event.{$prerequisiteColumn}", true)
            ->whereNull('completion.id')
            ->orderBy('event.start_date')
            ->orderBy('event.code')
            ->get(['event.id', 'event.code', 'event.title', 'event.start_date', 'event.end_date'])
            ->all();
    }

    public function findEventInVersion(string $eventId, string $publicationVersionId): ?stdClass
    {
        return DB::table('academic_calendar_events')
            ->where('id', $eventId)
            ->where('publication_version_id', $publicationVersionId)
            ->first([
                'id',
                'publication_version_id',
                'code',
                'title',
                'activation_prerequisite',
                'closure_prerequisite',
            ]);
    }

    public function completionExists(string $academicYearId, string $eventId): bool
    {
        return DB::table('academic_year_event_completions')
            ->where('academic_year_id', $academicYearId)
            ->where('event_id', $eventId)
            ->exists();
    }

    public function recordCompletion(
        string $id,
        string $academicYearId,
        string $eventId,
        string $completedBy,
        mixed $completedAt,
    ): void {
        $now = now();

        DB::table('academic_year_event_completions')->insert([
            'id' => $id,
            'academic_year_id' => $academicYearId,
            'event_id' => $eventId,
            'completed_by' => $completedBy,
            'completed_at' => $completedAt,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function transition(
        string $academicYearId,
        string $targetStatus,
        mixed $transitionedAt,
    ): void {
        $values = [
            'status' => $targetStatus,
            'updated_at' => $transitionedAt,
        ];

        if ($targetStatus === 'active') {
            $values['activated_at'] = $transitionedAt;
        } elseif ($targetStatus === 'closed') {
            $values['closed_at'] = $transitionedAt;
        }

        DB::table('academic_years')
            ->where('id', $academicYearId)
            ->update($values);
    }
}
