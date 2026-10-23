<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\AcademicYear02TestCase;

final class OfficialCalendarPublicationVersionImmutabilityTest extends AcademicYear02TestCase
{
    private string $authorityId;
    private string $publicationId;
    private string $versionId;
    private int $transactionLevelBefore = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $this->transactionLevelBefore = DB::connection('mysql')->transactionLevel();
        DB::connection('mysql')->beginTransaction();

        $this->authorityId = (string) Str::ulid();
        $this->publicationId = (string) Str::ulid();
        $this->versionId = (string) Str::ulid();

        $now = now();

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $this->authorityId,
            'country_code' => 'CO',
            'authority_code' => 'ACA-F04-TEST-'.$this->authorityId,
            'official_name' => 'Autoridad regulatoria de prueba ACA-F04',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('official_calendar_publications')->insert([
            'id' => $this->publicationId,
            'authority_id' => $this->authorityId,
            'document_type' => 'resolution',
            'document_number' => 'ACA-F04-'.$this->publicationId,
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR',
            'region_code' => 'NATIONAL',
            'calendar_code' => 'A',
            'identity_status' => 'unresolved',
            'identity_fingerprint' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insertVersion($this->versionId, 1, null, 'draft');
    }

    protected function tearDown(): void
    {
        try {
            if (DB::connection('mysql')->getDatabaseName() === 'cenicom_aca_year02_isolated_test') {
                while (DB::connection('mysql')->transactionLevel() > $this->transactionLevelBefore) {
                    DB::connection('mysql')->rollBack();
                }
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_draft_version_can_be_edited_before_validation(): void
    {
        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['title' => 'Título corregido en borrador', 'updated_at' => now()]);

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'title' => 'Título corregido en borrador',
            'verification_status' => 'draft',
        ], 'mysql');
    }

    public function test_validated_version_content_cannot_be_changed(): void
    {
        $this->transitionToValidated();

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['title' => 'Título alterado después de validar']));

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'title' => 'Calendario académico oficial de prueba',
            'verification_status' => 'validated',
        ], 'mysql');
    }

    public function test_available_version_content_cannot_be_changed(): void
    {
        $this->transitionToAvailable();

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['calendar_start_date' => '2026-02-01']));

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'calendar_start_date' => '2026-01-15',
            'verification_status' => 'available',
        ], 'mysql');
    }

    public function test_withdrawn_version_content_cannot_be_changed(): void
    {
        $this->transitionToAvailable();
        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'withdrawn',
                'withdrawn_at' => now()->toDateTimeString(),
                'updated_at' => now(),
            ]);

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['source_reference' => 'referencia reemplazada']));

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'source_reference' => 'ACA-F04 fuente de prueba',
            'verification_status' => 'withdrawn',
        ], 'mysql');
    }

    public function test_validated_version_can_be_made_available_without_rewriting_its_content(): void
    {
        $this->transitionToValidated();

        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'available',
                'updated_at' => now(),
            ]);

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'title' => 'Calendario académico oficial de prueba',
            'verification_status' => 'available',
        ], 'mysql');
    }

    public function test_available_version_can_be_withdrawn_once_without_rewriting_its_content(): void
    {
        $this->transitionToAvailable();
        $withdrawnAt = now()->toDateTimeString();

        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'withdrawn',
                'withdrawn_at' => $withdrawnAt,
                'updated_at' => now(),
            ]);

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'withdrawn',
            'withdrawn_at' => $withdrawnAt,
            'title' => 'Calendario académico oficial de prueba',
        ], 'mysql');

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['withdrawn_at' => now()->addDay()->toDateTimeString()]));
    }

    public function test_validated_version_cannot_be_deleted(): void
    {
        $this->transitionToValidated();

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->delete());

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'validated',
        ], 'mysql');
    }

    public function test_available_version_cannot_be_deleted(): void
    {
        $this->transitionToAvailable();

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->delete());

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'available',
        ], 'mysql');
    }

    public function test_validated_version_cannot_return_to_draft(): void
    {
        $this->transitionToValidated();

        $this->assertAcaF04Rejects(fn () => DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update(['verification_status' => 'draft']));

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'verification_status' => 'validated',
        ], 'mysql');
    }

    public function test_a_correction_is_added_as_a_new_version_without_changing_the_predecessor(): void
    {
        $this->transitionToAvailable();
        $newVersionId = (string) Str::ulid();

        $this->insertVersion($newVersionId, 2, $this->versionId, 'draft', 'Corrección del calendario académico');

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'version_number' => 1,
            'title' => 'Calendario académico oficial de prueba',
            'verification_status' => 'available',
        ], 'mysql');

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $newVersionId,
            'publication_id' => $this->publicationId,
            'version_number' => 2,
            'supersedes_version_id' => $this->versionId,
            'title' => 'Corrección del calendario académico',
            'verification_status' => 'draft',
        ], 'mysql');
    }

    public function test_every_official_content_field_is_locked_after_validation(): void
    {
        $this->transitionToValidated();

        $changes = [
            'id' => (string) Str::ulid(),
            'publication_id' => (string) Str::ulid(),
            'version_number' => 2,
            'supersedes_version_id' => (string) Str::ulid(),
            'official_version_label' => 'v2',
            'authority_name_snapshot' => 'Autoridad reemplazada',
            'title' => 'Título reemplazado',
            'promulgated_on' => '2026-02-02',
            'calendar_start_date' => '2026-02-01',
            'calendar_end_date' => '2026-12-01',
            'source_url' => 'https://example.invalid/correction',
            'source_reference' => 'referencia reemplazada',
            'document_sha256' => str_repeat('b', 64),
            'validated_at' => now()->addDay()->toDateTimeString(),
            'created_at' => now()->addDay()->toDateTimeString(),
        ];

        foreach ($changes as $column => $value) {
            $this->assertAcaF04Rejects(
                fn () => DB::table('official_calendar_publication_versions')
                    ->where('id', $this->versionId)
                    ->update([$column => $value]),
                'La columna '.$column.' no debe cambiar en una versión validada.',
            );
        }

        $this->assertDatabaseHas('official_calendar_publication_versions', [
            'id' => $this->versionId,
            'version_number' => 1,
            'title' => 'Calendario académico oficial de prueba',
            'calendar_start_date' => '2026-01-15',
            'calendar_end_date' => '2026-11-20',
            'verification_status' => 'validated',
        ], 'mysql');
    }

    private function transitionToValidated(): void
    {
        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'validated',
                'validated_at' => now()->toDateTimeString(),
                'updated_at' => now(),
            ]);
    }

    private function transitionToAvailable(): void
    {
        $this->transitionToValidated();

        DB::table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'available',
                'updated_at' => now(),
            ]);
    }

    private function insertVersion(
        string $id,
        int $versionNumber,
        ?string $supersedesVersionId,
        string $status,
        string $title = 'Calendario académico oficial de prueba',
    ): void {
        $now = now();
        $validatedAt = in_array($status, ['validated', 'available', 'withdrawn'], true)
            ? $now->toDateTimeString()
            : null;
        $withdrawnAt = $status === 'withdrawn' ? $now->toDateTimeString() : null;

        DB::table('official_calendar_publication_versions')->insert([
            'id' => $id,
            'publication_id' => $this->publicationId,
            'version_number' => $versionNumber,
            'supersedes_version_id' => $supersedesVersionId,
            'official_version_label' => 'v'.$versionNumber,
            'authority_name_snapshot' => 'Autoridad regulatoria de prueba ACA-F04',
            'title' => $title,
            'promulgated_on' => '2026-01-05',
            'calendar_start_date' => '2026-01-15',
            'calendar_end_date' => '2026-11-20',
            'source_url' => 'https://example.invalid/aca-f04-source',
            'source_reference' => 'ACA-F04 fuente de prueba',
            'document_sha256' => str_repeat('a', 64),
            'verification_status' => $status,
            'validated_at' => $validatedAt,
            'withdrawn_at' => $withdrawnAt,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function assertAcaF04Rejects(callable $operation, string $message = ''): void
    {
        try {
            $operation();
        } catch (QueryException $exception) {
            $this->assertStringContainsString(
                'ACA-F04',
                $exception->getMessage(),
                $message !== '' ? $message : 'La operación debía ser rechazada por el guard de inmutabilidad ACA-F04.',
            );

            return;
        }

        $this->fail(
            $message !== ''
                ? $message
                : 'La operación debió ser rechazada por el guard de inmutabilidad ACA-F04.',
        );
    }
}
