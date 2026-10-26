<?php

declare(strict_types=1);

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicYearCreateData;
use App\Modules\Academic\Domain\Services\AcademicYearCreator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

$arguments = array_slice($argv, 1);

if (count($arguments) !== 5) {
    fwrite(STDERR, "Argumentos insuficientes para el proceso ACA-F01.\n");
    exit(2);
}

[$institutionId, $versionId, $cycleCode, $barrierDirectory, $workerName] = $arguments;

try {
    $root = dirname(__DIR__, 2);

    require $root . '/vendor/autoload.php';

    $app = require $root . '/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    $config = $app->make('config');

    if (
        $config->get('app.env') !== 'testing'
        || $config->get('database.default') !== 'mysql'
    ) {
        throw new RuntimeException(
            'El proceso ACA-F01 requiere testing y la conexión mysql.',
        );
    }

    $mysql = $config->get('database.connections.mysql', []);

    $expected = [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3308',
        'database' => 'cenicom_aca_year02_isolated_test',
        'username' => 'aca_year02_test',
    ];

    foreach ($expected as $key => $value) {
        if ((string) ($mysql[$key] ?? '') !== $value) {
            throw new RuntimeException(
                'Configuración de base no autorizada: mysql.' . $key,
            );
        }
    }

    if (empty($mysql['password'] ?? null) || ! empty($mysql['url'] ?? null)) {
        throw new RuntimeException(
            'La conexión aislada no tiene la configuración de contraseña esperada.',
        );
    }

    $connection = DB::connection('mysql');
    $row = $connection->selectOne('SELECT DATABASE() AS database_name');

    if (
        (string) ($row->database_name ?? '')
        !== 'cenicom_aca_year02_isolated_test'
    ) {
        throw new RuntimeException(
            'El proceso rechazó la base de datos efectiva.',
        );
    }

    $identity = Mockery::mock(IdentityInterface::class);
    $identity->shouldReceive('id')->andReturn('aca-f01-' . $workerName);
    $identity->shouldReceive('name')->andReturn('ACA-F01 concurrent test');
    $identity->shouldReceive('authenticated')->andReturn(true);

    $authorization = Mockery::mock(AuthorizationServiceInterface::class);
    $authorization->shouldReceive('can')
        ->with($identity, 'academic_year.create')
        ->andReturn(true);

    $app->instance(IdentityInterface::class, $identity);
    $app->instance(AuthorizationServiceInterface::class, $authorization);

    /*
     * La pausa ocurre después de ejecutar la consulta de solapamiento,
     * antes de que el servicio pueda continuar hacia la inserción.
     * Solo existe en este proceso de prueba, nunca en código productivo.
     */
    DB::listen(static function (QueryExecuted $query): void {
        $sql = strtolower(str_replace(['`', '"'], '', $query->sql));

        if (
            str_contains($sql, 'academic_years as existing_year')
            && str_contains(
                $sql,
                'official_calendar_publication_versions as existing_version',
            )
        ) {
            usleep(2_000_000);
        }
    });

    if (! is_dir($barrierDirectory)) {
        throw new RuntimeException('No existe la barrera de sincronización.');
    }

    $readyFile = $barrierDirectory
        . DIRECTORY_SEPARATOR
        . 'ready-'
        . $workerName;

    if (file_put_contents($readyFile, 'ready') === false) {
        throw new RuntimeException('No se pudo señalar el proceso preparado.');
    }

    $deadline = microtime(true) + 45;

    while (! is_file($barrierDirectory . DIRECTORY_SEPARATOR . 'go')) {
        if (microtime(true) >= $deadline) {
            throw new RuntimeException('Tiempo agotado esperando la barrera.');
        }

        usleep(10_000);
    }

    try {
        $id = app(AcademicYearCreator::class)->execute(
            new AcademicYearCreateData(
                institutionId: $institutionId,
                publicationVersionId: $versionId,
                cycleCode: $cycleCode,
                calendarCode: 'A',
            ),
        );

        $result = [
            'result' => 'created',
            'id' => $id,
            'cycle_code' => $cycleCode,
        ];
    } catch (DomainException $exception) {
        $result = [
            'result' => 'rejected',
            'message' => $exception->getMessage(),
            'cycle_code' => $cycleCode,
        ];
    }

    echo json_encode($result, JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL,
    );

    echo json_encode([
        'result' => 'error',
        'message' => $exception->getMessage(),
    ], JSON_THROW_ON_ERROR);

    exit(2);
}
