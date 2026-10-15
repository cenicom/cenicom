<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Identity;

use JsonException;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class CampusCodeSequenceConcurrencyTest extends TestCase
{
    #[Test]
    public function concurrent_initialization_reserves_distinct_values(): void
    {
        $report = $this->runConcurrencyReport();

        $this->assertSame('cenicom', $report['database']);

        $scenario = $report['initialization_scenario'];

        $this->assertSame('initialization', $scenario['scenario']);
        $this->assertNull($scenario['initial_value']);
        $this->assertSuccessfulWorkers($scenario['results']);
        $this->assertSame(2, $scenario['persisted_count']);
        $this->assertSame(2, $scenario['unique_codes']);
        $this->assertSame(2, $scenario['final_counter']);
        $this->assertSame(12, $scenario['independent_counter']);
        $this->assertSame([1, 2], $this->returnedSequenceValues($scenario));

        $sequences = array_map(
            static fn(array $campus): int => (int) substr($campus['code'], -3),
            $scenario['persisted_campuses'],
        );

        sort($sequences, SORT_NUMERIC);

        $this->assertSame(
            2,
            $scenario['initialization_barrier_reached_workers'],
        );

        foreach ($scenario['results'] as $result) {
            $this->assertTrue(
                $result['initialization_read_barrier_reached'],
                "El trabajador {$result['worker']} no alcanzó la barrera de lectura.",
            );
        }
    }

    #[Test]
    public function concurrent_reservations_remain_unique_across_three_rounds(): void
    {
        $report = $this->runConcurrencyReport();

        $this->assertCount(3, $report['normal_scenarios']);

        foreach ($report['normal_scenarios'] as $scenario) {
            $this->assertSame(40, $scenario['initial_value']);
            $this->assertSuccessfulWorkers($scenario['results']);
            $this->assertSame(2, $scenario['persisted_count']);
            $this->assertSame(2, $scenario['unique_codes']);
            $this->assertSame(42, $scenario['final_counter']);
            $this->assertSame(12, $scenario['independent_counter']);
            $this->assertSame([41, 42], $this->returnedSequenceValues($scenario));

            $sequences = array_map(
                static fn(array $campus): int => (int) substr($campus['code'], -3),
                $scenario['persisted_campuses'],
            );

            sort($sequences, SORT_NUMERIC);

            $this->assertSame([41, 42], $sequences);
        }
    }

    #[Test]
    public function concurrent_reservations_respect_the_maximum_sequence_value(): void
    {
        $report = $this->runConcurrencyReport();
        $scenario = $report['boundary_scenario'];

        $this->assertSame('boundary', $scenario['scenario']);
        $this->assertSame(998, $scenario['initial_value']);
        $this->assertSame(1, $scenario['persisted_count']);
        $this->assertSame(1, $scenario['unique_codes']);
        $this->assertSame(999, $scenario['final_counter']);
        $this->assertSame(12, $scenario['independent_counter']);

        $successful = array_values(array_filter(
            $scenario['results'],
            static fn(array $result): bool => $result['status'] === 'success',
        ));

        $failed = array_values(array_filter(
            $scenario['results'],
            static fn(array $result): bool => $result['status'] === 'error',
        ));

        $this->assertCount(1, $successful);
        $this->assertCount(1, $failed);

        $this->assertStringEndsWith('-999', $successful[0]['code']);
        $this->assertSame(0, $successful[0]['exit_code']);

        $this->assertSame(RuntimeException::class, $failed[0]['exception']);
        $this->assertSame(
            'Campus code sequence has reached the maximum value of 999.',
            $failed[0]['message'],
        );
        $this->assertSame(1, $failed[0]['exit_code']);

        $this->assertSame([999], $this->returnedSequenceValues($scenario));

        $exhaustion = array_values(array_filter(
            $scenario['sequence_trace'],
            static fn(array $entry): bool => $entry['status'] === 'exception',
        ));

        $this->assertCount(1, $exhaustion);
        $this->assertSame(
            'Campus code sequence has reached the maximum value of 999.',
            $exhaustion[0]['message'],
        );
    }

    /**
     * Ejecuta el instrumento real de concurrencia en procesos independientes.
     *
     * @return array<string, mixed>
     */
    private function runConcurrencyReport(): array
    {
        $runner = base_path(
            'tests/Support/CampusConcurrency/CampusConcurrencyRunner.php',
        );

        $this->assertFileExists($runner);

        $pipes = [];

        $process = proc_open(
            [PHP_BINARY, $runner, '--rounds=3'],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path(),
        );

        $this->assertIsResource(
            $process,
            'No fue posible iniciar el ejecutor de concurrencia.',
        );

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $this->assertSame(
            0,
            $exitCode,
            "El ejecutor de concurrencia falló.\nSTDERR:\n{$stderr}\nSTDOUT:\n{$stdout}",
        );

        try {
            $report = json_decode(
                $stdout,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $exception) {
            $this->fail(
                'El ejecutor no devolvió JSON válido: '
                    . $exception->getMessage()
                    . "\nSTDOUT:\n{$stdout}",
            );
        }

        $this->assertIsArray($report);
        $this->assertSame('cenicom', $report['database']);
        $this->assertIsArray($report['initialization_scenario']);
        $this->assertCount(3, $report['normal_scenarios']);
        $this->assertIsArray($report['boundary_scenario']);

        return $report;
    }

    /**
     * @param array<int, array<string, mixed>> $results
     */
    private function assertSuccessfulWorkers(array $results): void
    {
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertSame('success', $result['status']);
            $this->assertSame(0, $result['exit_code']);
            $this->assertNotEmpty($result['code']);
            $this->assertNull($result['exception']);
        }
    }

    /**
     * @param array<string, mixed> $scenario
     * @return array<int, int>
     */
    private function returnedSequenceValues(array $scenario): array
    {
        $values = [];

        foreach ($scenario['sequence_trace'] as $entry) {
            if ($entry['status'] === 'returned') {
                $values[] = (int) $entry['value'];
            }
        }

        sort($values);

        return $values;
    }
}
