<?php

declare(strict_types=1);

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
//use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

$app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

config([
    'database.default' => 'mysql',
    'database.connections.mysql.database' => 'cenicom',
]);

DB::purge('mysql');
DB::setDefaultConnection('mysql');

const WORKER_COUNT = 2;
const WAIT_TIMEOUT_SECONDS = 30;

function assertDatabaseReady(): void
{
    $connection = DB::connection('mysql');

    if ($connection->getDatabaseName() !== 'cenicom') {
        throw new RuntimeException(
            'Ejecución cancelada: la base debe ser cenicom.',
        );
    }

    $tables = [
        'institutions',
        'addresses',
        'campuses',
        'campus_code_sequences',
    ];

    foreach ($tables as $table) {
        $row = $connection->selectOne(
            'SELECT ENGINE
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?',
            [$table],
        );

        if (
            $row === null
            || strtoupper((string) $row->ENGINE) !== 'INNODB'
        ) {
            throw new RuntimeException(
                "La tabla {$table} no existe o no usa InnoDB.",
            );
        }
    }
}

function uniqueInstitutionShortCode(): string
{
    do {
        $code = '';

        for ($i = 0; $i < 8; $i++) {
            $code .= chr(random_int(65, 90));
        }

        $exists = DB::connection('mysql')
            ->table('institutions')
            ->where('short_code', $code)
            ->exists();
    } while ($exists);

    return $code;
}

function createInstitution(string $label): object
{
    $institution = app(InstitutionCreatorInterface::class)->create(
        new InstitutionCreateData(
            name: 'Prueba aislada de concurrencia ' . $label,
            shortCode: uniqueInstitutionShortCode(),
        ),
    );

    return app(InstitutionRepositoryInterface::class)
        ->save($institution);
}

function createAddress(string $label): string
{
    $address = app(AddressServiceInterface::class)->create([
        'country_id' => 1,
        'state_id' => 1,
        'city_id' => 1,
        'address' => 'Prueba aislada de concurrencia ' . $label,
        'neighborhood' => 'Centro',
    ]);

    return (string) $address->getKey();
}

function waitForFiles(array $paths, int $timeout): void
{
    $deadline = microtime(true) + $timeout;

    do {
        $allReady = true;

        foreach ($paths as $path) {
            if (! is_file($path)) {
                $allReady = false;
                break;
            }
        }

        if ($allReady) {
            return;
        }

        usleep(10000);
    } while (microtime(true) < $deadline);

    throw new RuntimeException(
        'Tiempo agotado esperando que los dos trabajadores estén listos.',
    );
}

function removeDirectory(string $directory): void
{
    if (! is_dir($directory)) {
        return;
    }

    foreach (scandir($directory) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            @unlink($path);
        }
    }

    @rmdir($directory);
}

/**
 * Ejecuta dos procesos independientes para una misma institución.
 *
 * @return array<string, mixed>
 */
function runScenario(int $initialValue, string $label): array
{
    $institution = null;
    $sentinelInstitution = null;
    $addressId = null;
    $processes = [];
    $directory = sys_get_temp_dir()
        . DIRECTORY_SEPARATOR
        . 'cenicom-campus-concurrency-'
        . bin2hex(random_bytes(8));

    mkdir($directory, 0700, true);

    try {
        $institution = createInstitution($label);
        $sentinelInstitution = createInstitution($label . '-independiente');
        $addressId = createAddress($label);

        $institutionId = $institution->id();
        $institutionCode = $institution->shortCode();
        $sentinelId = $sentinelInstitution->id();

        DB::connection('mysql')
            ->table('campus_code_sequences')
            ->insert([
                'institution_id' => $institutionId,
                'current_value' => $initialValue,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::connection('mysql')
            ->table('campus_code_sequences')
            ->insert([
                'institution_id' => $sentinelId,
                'current_value' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $startPath = $directory . DIRECTORY_SEPARATOR . 'start';

        // CAMBIAR: barrera posterior a la primera lectura del contador
        $readBarrierDirectory = $directory
            . DIRECTORY_SEPARATOR
            . 'read-barrier';

        mkdir($readBarrierDirectory, 0700, true);

        $workerPath = __DIR__
            . DIRECTORY_SEPARATOR
            . 'CampusConcurrencyWorker.php';

        for ($i = 1; $i <= WORKER_COUNT; $i++) {
            $workerDirectory = $directory
                . DIRECTORY_SEPARATOR
                . 'worker-' . $i;

            mkdir($workerDirectory, 0700, true);

            $configPath = $workerDirectory
                . DIRECTORY_SEPARATOR . 'config.json';

            $readyPath = $workerDirectory
                . DIRECTORY_SEPARATOR . 'ready';

            $resultPath = $workerDirectory
                . DIRECTORY_SEPARATOR . 'result.json';

            $campusShortCode = $i === 1 ? 'SEDEUNO' : 'SEDEDOS';

            $config = [
                'worker' => $i,
                'institution_id' => $institutionId,
                'institution_short_code' => $institutionCode,
                'campus_short_code' => $campusShortCode,
                'campus_name' => "Sede de concurrencia {$label} {$i}",
                'address_id' => $addressId,
                'ready_path' => $readyPath,
                'start_path' => $startPath,
                'result_path' => $resultPath,
                'read_barrier_dir' => $readBarrierDirectory,
                'sequence_trace_path' => $workerDirectory
                    . DIRECTORY_SEPARATOR
                    . 'sequence-trace.jsonl',
            ];

            file_put_contents(
                $configPath,
                json_encode($config, JSON_THROW_ON_ERROR),
                LOCK_EX,
            );

            $pipes = [];

            $process = proc_open(
                [PHP_BINARY, $workerPath, $configPath],
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes,
                dirname(__DIR__, 3),
            );

            if (! is_resource($process)) {
                throw new RuntimeException(
                    "No fue posible iniciar el trabajador {$i}.",
                );
            }

            fclose($pipes[0]);

            $processes[] = [
                'worker' => $i,
                'process' => $process,
                'stdout' => $pipes[1],
                'stderr' => $pipes[2],
                'result_path' => $resultPath,
                'ready_path' => $readyPath,
                'trace_path' => $config['sequence_trace_path'],
            ];
        }

        waitForFiles(
            array_column($processes, 'ready_path'),
            WAIT_TIMEOUT_SECONDS,
        );

        // Ambos procesos están listos: se libera la barrera.
        file_put_contents($startPath, 'start', LOCK_EX);

        $results = [];

        foreach ($processes as $entry) {
            $stdout = stream_get_contents($entry['stdout']);
            $stderr = stream_get_contents($entry['stderr']);

            fclose($entry['stdout']);
            fclose($entry['stderr']);

            $exitCode = proc_close($entry['process']);

            $result = is_file($entry['result_path'])
                ? json_decode(
                    (string) file_get_contents($entry['result_path']),
                    true,
                    512,
                    JSON_THROW_ON_ERROR,
                )
                : [
                    'worker' => $entry['worker'],
                    'status' => 'error',
                    'exception' => 'WorkerResultMissing',
                    'message' => 'El trabajador no produjo resultado.',
                ];

            $result['exit_code'] = $exitCode;

            if ($stdout !== '') {
                $result['stdout'] = trim($stdout);
            }

            if ($stderr !== '') {
                $result['stderr'] = trim($stderr);
            }

            $results[] = $result;
        }

        $campuses = DB::connection('mysql')
            ->table('campuses')
            ->where('institution_id', $institutionId)
            ->get(['id', 'code', 'short_code']);

        $counter = DB::connection('mysql')
            ->table('campus_code_sequences')
            ->where('institution_id', $institutionId)
            ->value('current_value');

        $sentinelCounter = DB::connection('mysql')
            ->table('campus_code_sequences')
            ->where('institution_id', $sentinelId)
            ->value('current_value');

        $codes = $campuses->pluck('code')->all();

        $sequenceTrace = [];

        foreach ($processes as $entry) {
            $tracePath = $entry['trace_path'];

            if (! is_file($tracePath)) {
                $sequenceTrace[] = [
                    'worker' => $entry['worker'],
                    'error' => 'No se generó el archivo de traza.',
                ];

                continue;
            }

            $lines = file(
                $tracePath,
                FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES,
            );

            foreach ($lines as $line) {
                $sequenceTrace[] = json_decode(
                    $line,
                    true,
                    512,
                    JSON_THROW_ON_ERROR,
                );
            }
        }

        return [
            'scenario' => $label,
            'initial_value' => $initialValue,
            'results' => $results,
            'persisted_campuses' => $campuses->toArray(),
            'persisted_count' => $campuses->count(),
            'unique_codes' => count(array_unique($codes)),
            'final_counter' => (int) $counter,
            'independent_counter' => (int) $sentinelCounter,
            'sequence_trace' => $sequenceTrace,
        ];
    } finally {
        // No dejar procesos activos si falla la barrera o una lectura.
        foreach ($processes as $entry) {
            if (is_resource($entry['process'])) {
                @proc_terminate($entry['process']);
                @fclose($entry['stdout']);
                @fclose($entry['stderr']);
                @proc_close($entry['process']);
            }
        }

        if ($institution !== null) {
            $institutionId = $institution->id();

            DB::connection('mysql')->table('campuses')
                ->where('institution_id', $institutionId)->delete();

            DB::connection('mysql')->table('campus_code_sequences')
                ->where('institution_id', $institutionId)->delete();
        }

        if ($sentinelInstitution !== null) {
            DB::connection('mysql')->table('campus_code_sequences')
                ->where('institution_id', $sentinelInstitution->id())
                ->delete();
        }

        if ($addressId !== null) {
            DB::connection('mysql')->table('institution_addresses')
                ->where('address_id', $addressId)->delete();

            DB::connection('mysql')->table('addresses')
                ->where('id', $addressId)->delete();
        }

        foreach ([$institution, $sentinelInstitution] as $created) {
            if ($created !== null) {
                DB::connection('mysql')->table('institution_addresses')
                    ->where('institution_id', $created->id())->delete();

                DB::connection('mysql')->table('institutions')
                    ->where('id', $created->id())->delete();
            }
        }

        removeDirectory($directory);
    }
}

try {
    assertDatabaseReady();

    $rounds = 3;

    foreach ($argv as $argument) {
        if (preg_match('/^--rounds=(\d+)$/', $argument, $matches)) {
            $rounds = (int) $matches[1];
        }
    }

    if ($rounds < 1 || $rounds > 20) {
        throw new InvalidArgumentException(
            'El número de rondas debe estar entre 1 y 20.',
        );
    }

    $report = [
        'database' => DB::connection('mysql')->getDatabaseName(),
        'normal_scenarios' => [],
        'boundary_scenario' => null,
    ];

    for ($round = 1; $round <= $rounds; $round++) {
        $report['normal_scenarios'][] = runScenario(
            40,
            'normal-' . $round,
        );
    }

    $report['boundary_scenario'] = runScenario(
        998,
        'boundary',
    );

    echo json_encode(
        $report,
        JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR,
    ), PHP_EOL;
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL,
    );

    exit(1);
}
