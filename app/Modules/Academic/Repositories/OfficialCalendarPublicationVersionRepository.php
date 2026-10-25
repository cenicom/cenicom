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

    public function findPublicationForUpdate(
        string $publicationId,
    ): ?stdClass {
        return DB::table('official_calendar_publications')
            ->select(
                'id',
                'identity_status',
                'identity_fingerprint',
                'calendar_code',
            )
            ->where('id', $publicationId)
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
            ->whereNotNull('source_reference')
            ->whereRaw("TRIM(source_reference) <> ''")
            ->where(function ($query): void {
                $query->where(function ($query): void {
                    $query->whereNotNull('source_url')
                        ->whereRaw("TRIM(source_url) <> ''");
                })->orWhere(function ($query): void {
                    $query->whereNotNull('document_sha256')
                        ->whereRaw("TRIM(document_sha256) <> ''");
                });
            })
            ->whereExists(static function ($query): void {
                $query->selectRaw('1')
                    ->from('official_calendar_publications as publication')
                    ->whereColumn(
                        'publication.id',
                        'official_calendar_publication_versions.publication_id',
                    )
                    ->where('publication.identity_status', 'confirmed')
                    ->whereNotNull('publication.identity_fingerprint')
                    ->whereIn('publication.calendar_code', ['A', 'B']);
            })
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $validatedAt,
                'updated_at' => $validatedAt,
            ]) === 1;
    }

    public function transitionValidatedToAvailable(
        string $versionId,
        mixed $publishedAt,
    ): bool {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->where('verification_status', 'validated')
            ->whereNotNull('validated_at')
            ->whereNull('withdrawn_at')
            ->whereNotNull('calendar_start_date')
            ->whereNotNull('calendar_end_date')
            ->whereColumn('calendar_start_date', '<=', 'calendar_end_date')
            ->whereNotNull('source_reference')
            ->whereRaw("TRIM(source_reference) <> ''")
            ->where(function ($query): void {
                $query->where(function ($query): void {
                    $query->whereNotNull('source_url')
                        ->whereRaw("TRIM(source_url) <> ''");
                })->orWhere(function ($query): void {
                    $query->whereNotNull('document_sha256')
                        ->whereRaw("TRIM(document_sha256) <> ''");
                });
            })
            ->whereExists(static function ($query): void {
                $query->selectRaw('1')
                    ->from('official_calendar_publications as publication')
                    ->whereColumn(
                        'publication.id',
                        'official_calendar_publication_versions.publication_id',
                    )
                    ->where('publication.identity_status', 'confirmed')
                    ->whereNotNull('publication.identity_fingerprint')
                    ->whereIn('publication.calendar_code', ['A', 'B']);
            })
            ->update([
                'verification_status' => 'available',
                'updated_at' => $publishedAt,
            ]) === 1;
    }

    public function transitionToWithdrawn(
        string $versionId,
        mixed $withdrawnAt,
    ): bool {
        return DB::table('official_calendar_publication_versions')
            ->where('id', $versionId)
            ->whereIn('verification_status', ['validated', 'available'])
            ->whereNotNull('validated_at')
            ->whereNull('withdrawn_at')
            ->update([
                'verification_status' => 'withdrawn',
                'withdrawn_at' => $withdrawnAt,
                'updated_at' => $withdrawnAt,
            ]) === 1;
    }

    public function hasSuccessor(
        string $publicationId,
        string $versionId,
    ): bool {
        return DB::table('official_calendar_publication_versions')
            ->where('publication_id', $publicationId)
            ->where('supersedes_version_id', $versionId)
            ->exists();
    }

    public function latestVersionNumber(string $publicationId): int
    {
        return (int) DB::table('official_calendar_publication_versions')
            ->where('publication_id', $publicationId)
            ->max('version_number');
    }

    /**
     * Inserta una nueva versión en borrador sin actualizar la anterior.
     *
     * @param array{
     *     official_version_label: string|null,
     *     authority_name_snapshot: string,
     *     title: string,
     *     promulgated_on: string|null,
     *     calendar_start_date: string|null,
     *     calendar_end_date: string|null,
     *     source_url: string|null,
     *     source_reference: string|null,
     *     document_sha256: string|null
     * } $content
     */
    public function createDraftCorrection(
        string $id,
        string $publicationId,
        int $versionNumber,
        string $supersedesVersionId,
        array $content,
        mixed $at,
    ): void {
        DB::table('official_calendar_publication_versions')->insert([
            'id' => $id,
            'publication_id' => $publicationId,
            'version_number' => $versionNumber,
            'supersedes_version_id' => $supersedesVersionId,
            'official_version_label' => $content['official_version_label'],
            'authority_name_snapshot' => $content['authority_name_snapshot'],
            'title' => $content['title'],
            'promulgated_on' => $content['promulgated_on'],
            'calendar_start_date' => $content['calendar_start_date'],
            'calendar_end_date' => $content['calendar_end_date'],
            'source_url' => $content['source_url'],
            'source_reference' => $content['source_reference'],
            'document_sha256' => $content['document_sha256'],
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }
}
