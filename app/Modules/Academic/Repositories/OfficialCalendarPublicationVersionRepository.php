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
}
