<?php

declare(strict_types=1);

$projectRoot = getcwd();
$relativePath = 'tests/Feature/Modules/Academic/AcademicYearLifecycleTest.php';
$path = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

if (!is_file($path)) {
    fwrite(STDERR, "ERROR: no existe {$path}. Ejecute el script desde la raíz del proyecto.\n");
    exit(1);
}

$original = file_get_contents($path);
if ($original === false) {
    fwrite(STDERR, "ERROR: no se pudo leer {$path}.\n");
    exit(1);
}

if (str_contains($original, 'test_audit_failure_rolls_back_milestone_completion')) {
    echo "SIN CAMBIOS: la prueba de rollback de auditoría del hito ya existe.\n";
    exit(0);
}

$newline = str_contains($original, "\r\n") ? "\r\n" : "\n";
$content = str_replace(["\r\n", "\r"], "\n", $original);

$completionAnchor = <<<'ANCHOR'
        $this->assertDatabaseHas('academic_year_event_completions', [
            'academic_year_id' => $yearId,
            'event_id' => $eventId,
            'completed_by' => 'aca-year02-lifecycle-actor',
        ], 'mysql');
ANCHOR;

$completionAssertions = <<<'CODE'
        $this->assertDatabaseHas('academic_year_event_completions', [
            'academic_year_id' => $yearId,
            'event_id' => $eventId,
            'completed_by' => 'aca-year02-lifecycle-actor',
        ], 'mysql');

        // ACA-F06: verify the milestone completion and its audit record are persisted together.
        $completion = DB::table('academic_year_event_completions')
            ->where('academic_year_id', $yearId)
            ->where('event_id', $eventId)
            ->first();
        $this->assertNotNull($completion);

        $audit = DB::table('audit_entries')
            ->where('action', 'academic_year.milestone_completed')
            ->where('subject_type', 'academic_year_event_completion')
            ->where('subject_id', $completion->id)
            ->first();

        $this->assertNotNull($audit, 'El cumplimiento del hito debe tener su entrada de auditoría.');
        $this->assertSame('success', $audit->result);
        $this->assertSame('aca-year02-lifecycle-actor', $audit->actor_id);
        $this->assertSame(1, (int) $audit->actor_authenticated);
        $this->assertNotSame('', (string) $audit->occurred_at);

        $metadata = json_decode((string) $audit->metadata, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($yearId, $metadata['academic_year_id']);
        $this->assertSame($versionId, $metadata['publication_version_id']);
        $this->assertSame($eventId, $metadata['event_id']);
        $this->assertSame('TEACHER_PREP', $metadata['event_code']);
        $this->assertTrue($metadata['activation_prerequisite']);
        $this->assertFalse($metadata['closure_prerequisite']);
CODE;

$anchorCount = substr_count($content, $completionAnchor);
if ($anchorCount !== 1) {
    fwrite(STDERR, "ERROR: el bloque de aserciones esperado aparece {$anchorCount} veces; no se modificó ningún archivo.\n");
    exit(1);
}

$methodAnchor = "    public function test_activation_requires_permission_and_authenticated_identity(): void\n    {";
if (substr_count($content, $methodAnchor) !== 1) {
    fwrite(STDERR, "ERROR: no se encontró exactamente una vez el método destino para insertar la prueba; no se modificó ningún archivo.\n");
    exit(1);
}

$content = str_replace($completionAnchor, $completionAssertions, $content);

$newTest = <<<'TEST'
    public function test_audit_failure_rolls_back_milestone_completion(): void
    {
        Carbon::setTestNow('2026-01-20 10:00:00');
        [$institutionId, $versionId] = $this->createInstitutionAndVersion('2026-01-10', '2026-12-15');
        $yearId = $this->createYear($institutionId, $versionId, '2026-M');
        $eventId = $this->createEvent($versionId, 'TEACHER_PREP', true, false);

        $recorder = \Mockery::mock(\App\Core\Audit\Contracts\AuditRecorderInterface::class);
        $recorder->shouldReceive('record')
            ->once()
            ->andThrow(new \RuntimeException('Fallo de auditoría de cumplimiento de hito.'));
        $this->app->instance(\App\Core\Audit\Contracts\AuditRecorderInterface::class, $recorder);

        try {
            app(CompleteAcademicYearEvent::class)->execute($yearId, $eventId);
            $this->fail('El fallo del registro de auditoría debía propagarse.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Fallo de auditoría de cumplimiento de hito.', $exception->getMessage());
        }

        $this->assertDatabaseMissing('academic_year_event_completions', [
            'academic_year_id' => $yearId,
            'event_id' => $eventId,
        ], 'mysql');
    }

TEST;

$content = str_replace($methodAnchor, $newTest . $methodAnchor, $content);
if ($content === $original) {
    fwrite(STDERR, "ERROR: el resultado no contiene cambios; no se escribió ningún archivo.\n");
    exit(1);
}

$content = str_replace("\n", $newline, $content);
if (file_put_contents($path, $content) === false) {
    fwrite(STDERR, "ERROR: no se pudo escribir {$path}.\n");
    exit(1);
}

echo "ACTUALIZADO: {$path}\n";
echo "Añadidas aserciones de auditoría del hito y prueba de rollback ante fallo del auditor.\n";
echo "Solo se modificó AcademicYearLifecycleTest.php; no se tocaron migraciones ni archivos de aplicación.\n";
