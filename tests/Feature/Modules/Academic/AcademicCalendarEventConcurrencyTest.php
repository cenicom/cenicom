<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\Support\AcademicYear02TestCase;

final class AcademicCalendarEventConcurrencyTest extends AcademicYear02TestCase
{
    private string $authorityId;
    private string $publicationId;
    private string $versionId;

    public function test_concurrent_events_with_same_code_allow_exactly_one_success(): void
    {
        $this->assertSame('cenicom_aca_year02_isolated_test', DB::connection('mysql')->getDatabaseName());
        $this->createCommittedDraftPublicationVersion();

        try {
            $results = $this->runWorkers();
            $created = array_values(array_filter($results, fn (array $r): bool => ($r['status'] ?? null) === 'created'));
            $rejected = array_values(array_filter($results, fn (array $r): bool => ($r['status'] ?? null) === 'rejected'));

            $this->assertCount(1, $created, json_encode($results, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            $this->assertCount(1, $rejected, json_encode($results, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            $this->assertStringContainsString('ya existe', (string) ($rejected[0]['message'] ?? ''));

            $events = DB::table('academic_calendar_events')->where('publication_version_id', $this->versionId)->where('code', 'F08-COMMON')->get();
            $this->assertCount(1, $events);
            $this->assertSame(1, DB::table('audit_entries')
                ->where('subject_type', 'academic_calendar_event')->where('subject_id', $events[0]->id)
                ->where('action', 'academic_calendar_event.created')->where('result', 'success')->count());
            $this->assertSame(1, DB::table('audit_entries')
                ->where('subject_type', 'academic_calendar_event')->where('action', 'academic_calendar_event.created')
                ->where('result', 'success')
                ->whereIn('subject_id', DB::table('academic_calendar_events')->where('publication_version_id', $this->versionId)->select('id'))
                ->count());
        } finally {
            DB::table('audit_entries')->where('subject_type', 'academic_calendar_event')
                ->whereIn('subject_id', DB::table('academic_calendar_events')->where('publication_version_id', $this->versionId)->select('id'))->delete();
            DB::table('academic_calendar_events')->where('publication_version_id', $this->versionId)->delete();
            DB::table('official_calendar_publication_versions')->where('id', $this->versionId)->delete();
            DB::table('official_calendar_publications')->where('id', $this->publicationId)->delete();
            DB::table('academic_regulatory_authorities')->where('id', $this->authorityId)->delete();
        }
    }

    private function createCommittedDraftPublicationVersion(): void
    {
        $this->authorityId = (string) Str::ulid();
        $this->publicationId = (string) Str::ulid();
        $this->versionId = (string) Str::ulid();
        $now = now();

        DB::table('academic_regulatory_authorities')->insert([
            'id' => $this->authorityId, 'country_code' => 'CO',
            'authority_code' => 'F08-' . substr($this->authorityId, 0, 12),
            'official_name' => 'Autoridad de prueba ACA-F08', 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('official_calendar_publications')->insert([
            'id' => $this->publicationId, 'authority_id' => $this->authorityId,
            'document_type' => 'resolution', 'document_number' => 'F08-' . $this->publicationId,
            'document_year' => 2026, 'reference_period' => '2026', 'jurisdiction_code' => 'NATIONAL',
            'regime_code' => 'REGULAR', 'region_code' => 'NATIONAL', 'calendar_code' => 'A',
            'identity_status' => 'unresolved', 'identity_fingerprint' => null, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('official_calendar_publication_versions')->insert([
            'id' => $this->versionId, 'publication_id' => $this->publicationId, 'version_number' => 1,
            'supersedes_version_id' => null, 'official_version_label' => 'v1',
            'authority_name_snapshot' => 'Autoridad de prueba ACA-F08', 'title' => 'Calendario de prueba ACA-F08',
            'promulgated_on' => '2026-01-05', 'calendar_start_date' => '2026-01-10', 'calendar_end_date' => '2026-12-15',
            'source_url' => 'https://example.invalid/aca-f08', 'source_reference' => 'ACA-YEAR02-F08-FIXTURE',
            'document_sha256' => str_repeat('8', 64), 'verification_status' => 'draft',
            'validated_at' => null, 'withdrawn_at' => null, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function runWorkers(): array
    {
        $worker = base_path('tests/Support/AcademicCalendarEventConcurrencyWorker.php');
        if (!is_file($worker)) throw new RuntimeException("No existe el worker: {$worker}");
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'aca-f08-' . bin2hex(random_bytes(6));
        if (!mkdir($dir, 0700) && !is_dir($dir)) throw new RuntimeException("No se pudo crear {$dir}");
        $specs = [
            'worker_a' => ['Evento concurrente A', '2026-04-01', '2026-04-01'],
            'worker_b' => ['Evento concurrente B', '2026-04-02', '2026-04-02'],
        ];
        $running = [];
        try {
            foreach ($specs as $key => $spec) {
                $cmd = [PHP_BINARY, $worker, $this->versionId, 'F08-COMMON', ...$spec, $dir, $key];
                $p = proc_open($cmd, [0 => ['pipe','r'], 1 => ['pipe','w'], 2 => ['pipe','w']], $pipes, base_path());
                if (!is_resource($p)) throw new RuntimeException("No se pudo iniciar {$key}");
                fclose($pipes[0]); stream_set_blocking($pipes[1], false); stream_set_blocking($pipes[2], false);
                $running[$key] = ['p' => $p, 'out' => $pipes[1], 'err' => $pipes[2]];
            }
            $deadline = microtime(true) + 30;
            foreach (array_keys($specs) as $key) {
                while (!is_file("$dir/ready-$key")) {
                    if (microtime(true) > $deadline) throw new RuntimeException("Timeout esperando {$key}");
                    usleep(20000);
                }
            }
            file_put_contents("$dir/go", 'go');
            $results = [];
            foreach (array_keys($specs) as $key) {
                $item = $running[$key]; $out = ''; $err = ''; $deadline = microtime(true) + 45;
                do {
                    $out .= stream_get_contents($item['out']); $err .= stream_get_contents($item['err']);
                    $status = proc_get_status($item['p']);
                    if (!$status['running']) break;
                    if (microtime(true) > $deadline) throw new RuntimeException("Timeout ejecutando {$key}");
                    usleep(20000);
                } while (true);
                $out .= stream_get_contents($item['out']); $err .= stream_get_contents($item['err']);
                unset($running[$key]);

                foreach (['out', 'err'] as $streamKey) {
                    if (isset($item[$streamKey]) && is_resource($item[$streamKey])) {
                        fclose($item[$streamKey]);
                    }
                }

                if (isset($item['p']) && is_resource($item['p'])) {
                    proc_close($item['p']);
                }
                $decoded = json_decode(trim($out), true);
                if (!is_array($decoded) || ($decoded['status'] ?? '') === 'error') throw new RuntimeException("Worker {$key} falló. stdout={$out}; stderr={$err}");
                $results[] = $decoded;
            }
            return $results;
        } finally {
            @file_put_contents("$dir/go", 'go');
            foreach ($running as $item) {
                if (isset($item['p']) && is_resource($item['p'])) {
                    $status = proc_get_status($item['p']);

                    if ($status['running']) {
                        @proc_terminate($item['p']);
                    }

                    @proc_close($item['p']);
                }

                foreach (['out', 'err'] as $streamKey) {
                    if (isset($item[$streamKey]) && is_resource($item[$streamKey])) {
                        @fclose($item[$streamKey]);
                    }
                }
            }
            foreach (glob("$dir/*") ?: [] as $f) @unlink($f);
            @rmdir($dir);
        }
    }
}
