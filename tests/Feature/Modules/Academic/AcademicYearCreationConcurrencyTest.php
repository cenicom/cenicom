<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\Support\AcademicYear02TestCase;

final class AcademicYearCreationConcurrencyTest extends AcademicYear02TestCase
{
    private ?string $institutionId = null;
    private ?string $authorityId = null;
    private ?string $publicationId = null;
    private ?string $versionId = null;

    protected function tearDown(): void
    {
        try {
            $this->cleanupFixtures();
        } finally {
            parent::tearDown();
        }
    }

    public function test_concurrent_overlapping_year_creations_are_serialized_by_institution_lock(): void
    {
        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            DB::connection('mysql')->getDatabaseName(),
        );

        $this->createCommittedFixtures();

        $results = $this->runConcurrentWorkers();

        $created = array_values(array_filter(
            $results,
            static fn(array $result): bool => ($result['result'] ?? null) === 'created',
        ));

        $rejected = array_values(array_filter(
            $results,
            static fn(array $result): bool => ($result['result'] ?? null) === 'rejected',
        ));

        $this->assertCount(
            1,
            $created,
            'Exactamente un proceso debe crear el año lectivo.',
        );

        $this->assertCount(
            1,
            $rejected,
            'El segundo proceso debe rechazar el intervalo solapado.',
        );

        $this->assertStringContainsString(
            'se solapa',
            mb_strtolower((string) ($rejected[0]['message'] ?? '')),
        );

        $years = DB::connection('mysql')
            ->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->get();

        $this->assertCount(1, $years);
        $this->assertSame('draft', $years[0]->status);

        $createdId = (string) $created[0]['id'];

        $this->assertSame(
            1,
            DB::connection('mysql')
                ->table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->where('subject_id', $createdId)
                ->where('action', 'academic_year.created')
                ->where('result', 'success')
                ->count(),
        );
    }

    private function createCommittedFixtures(): void
    {
        $db = DB::connection('mysql');
        $now = now();

        $this->institutionId = (string) Str::ulid();
        $this->authorityId = (string) Str::ulid();
        $this->publicationId = (string) Str::ulid();
        $this->versionId = (string) Str::ulid();

        $authorityCode = 'ACA-F01-' . substr($this->institutionId, 0, 8);

        $db->table('institutions')->insert([
            'id' => $this->institutionId,
            'code' => 'ACA-F01-' . substr($this->institutionId, 0, 12),
            'short_code' => 'ACA' . strtr(
                strtoupper(bin2hex(random_bytes(6))),
                '0123456789ABCDEF',
                'ABCDEFGHIJKLMNOP',
            ),
            'name' => 'Institución concurrente ACA-F01',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('academic_regulatory_authorities')->insert([
            'id' => $this->authorityId,
            'country_code' => 'CO',
            'authority_code' => $authorityCode,
            'official_name' => 'Autoridad concurrente ACA-F01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('official_calendar_publications')->insert([
            'id' => $this->publicationId,
            'authority_id' => $this->authorityId,
            'document_type' => 'RESOLUTION',
            'document_number' => 'F01-' . substr($this->publicationId, -8),
            'document_year' => 2026,
            'reference_period' => '2026',
            'jurisdiction_code' => 'ACA_TEST',
            'regime_code' => 'GENERAL',
            'region_code' => 'TEST_REGION',
            'calendar_code' => 'A',
            'identity_status' => 'confirmed',
            'identity_fingerprint' => hash('sha256', $this->publicationId),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('official_calendar_publication_versions')->insert([
            'id' => $this->versionId,
            'publication_id' => $this->publicationId,
            'version_number' => 1,
            'supersedes_version_id' => null,
            'official_version_label' => null,
            'authority_name_snapshot' => 'Autoridad concurrente ACA-F01',
            'title' => 'Calendario concurrente ACA-F01',
            'promulgated_on' => '2026-01-01',
            'calendar_start_date' => '2026-01-01',
            'calendar_end_date' => '2026-12-31',
            'source_url' => 'https://example.invalid/aca-f01-concurrency',
            'source_reference' => 'Fixture de concurrencia ACA-F01',
            'document_sha256' => null,
            'verification_status' => 'draft',
            'validated_at' => null,
            'withdrawn_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'validated',
                'validated_at' => $now,
                'updated_at' => $now,
            ]);

        $db->table('official_calendar_publication_versions')
            ->where('id', $this->versionId)
            ->update([
                'verification_status' => 'available',
                'updated_at' => $now,
            ]);

        $db->table('institution_academic_profiles')->insert([
            'institution_id' => $this->institutionId,
            'country_code' => 'CO',
            'authority_code' => $authorityCode,
            'jurisdiction_code' => 'ACA_TEST',
            'regime_code' => 'GENERAL',
            'region_code' => 'TEST_REGION',
            'calendar_code' => 'A',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function runConcurrentWorkers(): array
    {
        if (! function_exists('proc_open')) {
            $this->markTestSkipped('proc_open no está disponible en este entorno.');
        }

        if ($this->institutionId === null || $this->versionId === null) {
            throw new RuntimeException('Los fixtures de concurrencia no están preparados.');
        }

        $directory = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'aca-f01-concurrency-'
            . bin2hex(random_bytes(6));

        if (! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('No se pudo crear la barrera temporal.');
        }

        $goFile = $directory . DIRECTORY_SEPARATOR . 'go';
        $workerFile = base_path(
            'tests/Support/AcademicYearCreationConcurrencyWorker.php',
        );

        $definitions = [
            'worker_a' => 'RACE-A',
            'worker_b' => 'RACE-B',
        ];

        $workers = [];

        try {
            foreach ($definitions as $name => $cycleCode) {
                $descriptors = [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ];

                $command = [
                    PHP_BINARY,
                    $workerFile,
                    $this->institutionId,
                    $this->versionId,
                    $cycleCode,
                    $directory,
                    $name,
                ];

                $process = proc_open(
                    $command,
                    $descriptors,
                    $pipes,
                    base_path(),
                    null,
                    ['bypass_shell' => true],
                );

                if (! is_resource($process)) {
                    throw new RuntimeException(
                        'No se pudo iniciar ' . $name . '.',
                    );
                }

                fclose($pipes[0]);

                $workers[$name] = [
                    'process' => $process,
                    'pipes' => $pipes,
                    'exit_code' => null,
                ];
            }

            // Ambos procesos deben estar preparados antes de recibir la señal.
            $startupDeadline = microtime(true) + 30;

            while (true) {
                $allReady = true;

                foreach (array_keys($definitions) as $name) {
                    if (! is_file(
                        $directory . DIRECTORY_SEPARATOR . 'ready-' . $name,
                    )) {
                        $allReady = false;
                        break;
                    }
                }

                if ($allReady) {
                    break;
                }

                if (microtime(true) >= $startupDeadline) {
                    throw new RuntimeException(
                        'Tiempo agotado esperando a los dos procesos preparados.',
                    );
                }

                foreach ($workers as $name => $worker) {
                    $status = proc_get_status($worker['process']);

                    if (
                        ! $status['running']
                        && ! is_file(
                            $directory . DIRECTORY_SEPARATOR . 'ready-' . $name,
                        )
                    ) {
                        throw new RuntimeException(
                            $name . ' terminó antes de prepararse.',
                        );
                    }
                }

                usleep(10_000);
            }

            file_put_contents($goFile, 'go');

            $deadline = microtime(true) + 35;

            while (true) {
                $allExited = true;

                foreach ($workers as &$worker) {
                    if ($worker['exit_code'] !== null) {
                        continue;
                    }

                    $status = proc_get_status($worker['process']);

                    if ($status['running']) {
                        $allExited = false;
                    } else {
                        $worker['exit_code'] = $status['exitcode'];
                    }
                }

                unset($worker);

                if ($allExited) {
                    break;
                }

                if (microtime(true) >= $deadline) {
                    throw new RuntimeException(
                        'Tiempo agotado esperando los procesos concurrentes.',
                    );
                }

                usleep(10_000);
            }

            $results = [];

            foreach ($workers as $name => &$worker) {
                $stdout = stream_get_contents($worker['pipes'][1]);
                $stderr = stream_get_contents($worker['pipes'][2]);

                fclose($worker['pipes'][1]);
                fclose($worker['pipes'][2]);

                $worker['pipes'] = [];

                proc_close($worker['process']);
                $worker['process'] = null;

                if (
                    $worker['exit_code'] !== 0
                    && $worker['exit_code'] !== -1
                ) {
                    throw new RuntimeException(
                        $name
                            . ' terminó con código '
                            . $worker['exit_code']
                            . '. stdout='
                            . trim((string) $stdout)
                            . '; stderr='
                            . trim((string) $stderr),
                    );
                }

                $result = json_decode(
                    trim((string) $stdout),
                    true,
                    512,
                    JSON_THROW_ON_ERROR,
                );

                if (! is_array($result) || ($result['result'] ?? null) === 'error') {
                    throw new RuntimeException(
                        $name . ' informó un error: '
                            . trim((string) $stdout)
                            . '; stderr='
                            . trim((string) $stderr),
                    );
                }

                $result['worker'] = $name;
                $results[] = $result;
            }

            unset($worker);

            return $results;
        } finally {
            // Libera los auxiliares incluso si falla una aserción o el arranque.
            if (! is_file($goFile)) {
                @file_put_contents($goFile, 'go');
            }

            foreach ($workers as $worker) {
                $process = $worker['process'] ?? null;

                if (is_resource($process)) {
                    $status = proc_get_status($process);

                    if ($status['running']) {
                        proc_terminate($process);
                    }

                    foreach (($worker['pipes'] ?? []) as $pipe) {
                        if (is_resource($pipe)) {
                            fclose($pipe);
                        }
                    }

                    proc_close($process);
                }
            }

            foreach (glob($directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
                @unlink($file);
            }

            @rmdir($directory);
        }
    }

    private function cleanupFixtures(): void
    {
        if ($this->institutionId === null) {
            return;
        }

        $db = DB::connection('mysql');

        $yearIds = $db->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->pluck('id')
            ->all();

        if ($yearIds !== []) {
            $db->table('audit_entries')
                ->where('subject_type', 'academic_year')
                ->whereIn('subject_id', $yearIds)
                ->delete();
        }

        $db->table('academic_years')
            ->where('institution_id', $this->institutionId)
            ->delete();

        $db->table('institution_academic_profiles')
            ->where('institution_id', $this->institutionId)
            ->delete();

        $db->table('institutions')
            ->where('id', $this->institutionId)
            ->delete();
    }
}
