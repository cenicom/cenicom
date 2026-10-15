<?php

declare(strict_types=1);

use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use App\Modules\Campus\Infrastructure\Identity\CampusCodeSequence;
use Tests\Support\CampusConcurrency\RecordingCampusCodeSequence;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

$configPath = $argv[1] ?? '';

if ($configPath === '' || ! is_file($configPath)) {
    fwrite(STDERR, "Falta el archivo de configuración.\n");
    exit(2);
}

$config = json_decode(
    (string) file_get_contents($configPath),
    true,
    512,
    JSON_THROW_ON_ERROR,
);

$app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

config([
    'database.default' => 'mysql',
    'database.connections.mysql.database' => 'cenicom',
]);

DB::purge('mysql');
DB::setDefaultConnection('mysql');

$result = [
    'worker' => $config['worker'],
    'status' => 'error',
    'code' => null,
    'campus_id' => null,
    'exception' => null,
    'message' => null,
    'first_read_barrier_reached' => false,
];

try {
    if (DB::connection('mysql')->getDatabaseName() !== 'cenicom') {
        throw new RuntimeException(
            'El trabajador no está conectado a cenicom.',
        );
    }

    $readyPath = $config['ready_path'];
    $startPath = $config['start_path'];
    $resultPath = $config['result_path'];
    $barrierDirectory = $config['read_barrier_dir'];
    $worker = (int) $config['worker'];

    file_put_contents($readyPath, 'ready', LOCK_EX);

    $deadline = microtime(true) + 30;

    while (! is_file($startPath)) {
        if (microtime(true) >= $deadline) {
            throw new RuntimeException(
                'Tiempo agotado esperando el inicio.',
            );
        }

        usleep(10000);
    }

    /*
     * Instrumentación de prueba: sincroniza después de que cada
     * trabajador haya recibido su primera lectura de current_value.
     */
    $firstSequenceRead = false;

    DB::listen(
        function (QueryExecuted $query) use (
            &$firstSequenceRead,
            $barrierDirectory,
            $worker,
        ): void {
            if ($firstSequenceRead) {
                return;
            }

            $sql = strtolower($query->sql);

            if (
                ! str_contains($sql, 'campus_code_sequences')
                || ! preg_match(
                    '/select\s+[`"]?current_value[`"]?\s+from/i',
                    $query->sql,
                )
            ) {
                return;
            }

            $firstSequenceRead = true;

            $marker = $barrierDirectory
                . DIRECTORY_SEPARATOR
                . 'read-' . $worker;

            file_put_contents($marker, 'read', LOCK_EX);

            $otherWorker = $worker === 1 ? 2 : 1;

            $otherMarker = $barrierDirectory
                . DIRECTORY_SEPARATOR
                . 'read-' . $otherWorker;

            $deadline = microtime(true) + 30;

            while (! is_file($otherMarker)) {
                if (microtime(true) >= $deadline) {
                    throw new RuntimeException(
                        'Tiempo agotado esperando la primera lectura '
                            . 'del otro trabajador.',
                    );
                }

                usleep(10000);
            }
        },
    );

    require_once __DIR__ . '/RecordingCampusCodeSequence.php';

    $app->instance(
        CampusCodeSequenceInterface::class,
        new RecordingCampusCodeSequence(
            new CampusCodeSequence(),
            $config['sequence_trace_path'],
            (int) $config['worker'],
        ),
    );

    $campus = app(CampusCreationUseCase::class)->execute(
        new CampusCreateData(
            institutionId: $config['institution_id'],
            institutionShortCode: $config['institution_short_code'],
            shortCode: $config['campus_short_code'],
            name: $config['campus_name'],
            addressId: $config['address_id'],
            ministryCode: null,
        ),
    );

    $result['status'] = 'success';
    $result['code'] = $campus->code();
    $result['campus_id'] = $campus->id();
    $result['first_read_barrier_reached'] = $firstSequenceRead;
} catch (Throwable $exception) {
    $result['exception'] = $exception::class;
    $result['message'] = $exception->getMessage();
    $result['first_read_barrier_reached'] = $firstSequenceRead
        ?? false;
} finally {
    file_put_contents(
        $config['result_path'],
        json_encode(
            $result,
            JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR,
        ),
        LOCK_EX,
    );
}

exit($result['status'] === 'success' ? 0 : 1);
