<?php

declare(strict_types=1);

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Modules\Academic\Domain\DTO\AcademicCalendarEventData;
use App\Modules\Academic\Domain\Services\CreateAcademicCalendarEvent;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

try {
    [$versionId, $code, $title, $start, $end, $dir, $worker] = array_pad(array_slice($argv, 1), 7, '');
    $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    $mysql = config('database.connections.mysql');

    if (app()->environment() !== 'testing' || config('database.default') !== 'mysql'
        || ($mysql['driver'] ?? null) !== 'mysql' || (string) ($mysql['host'] ?? '') !== '127.0.0.1'
        || (string) ($mysql['port'] ?? '') !== '3308' || ($mysql['database'] ?? null) !== 'cenicom_aca_year02_isolated_test'
        || ($mysql['username'] ?? null) !== 'aca_year02_test' || trim((string) ($mysql['password'] ?? '')) === '' || !empty($mysql['url'])
        || DB::connection('mysql')->getDatabaseName() !== 'cenicom_aca_year02_isolated_test'
        || DB::connection('mysql')->selectOne('SELECT DATABASE() AS db')->db !== 'cenicom_aca_year02_isolated_test') {
        throw new RuntimeException('La configuración efectiva no cumple el aislamiento ACA-YEAR-02.');
    }

    $identity = Mockery::mock(IdentityInterface::class);
    $identity->shouldReceive('id')->andReturn('aca-f08-' . $worker);
    $identity->shouldReceive('name')->andReturn('ACA-F08 concurrent event test');
    $identity->shouldReceive('authenticated')->andReturn(true);
    $auth = Mockery::mock(AuthorizationServiceInterface::class);
    $auth->shouldReceive('can')->with($identity, 'academic_calendar_event.create')->andReturn(true);
    app()->instance(IdentityInterface::class, $identity);
    app()->instance(AuthorizationServiceInterface::class, $auth);

    touch("$dir/ready-$worker");
    $deadline = microtime(true) + 45;
    while (!is_file("$dir/go")) {
        if (microtime(true) > $deadline) throw new RuntimeException('Timeout esperando barrera.');
        usleep(20000);
    }

    try {
        $id = app(CreateAcademicCalendarEvent::class)->execute($versionId, new AcademicCalendarEventData(
            code: $code, eventType: 'PEDAGOGICAL_DAY', title: $title, audience: 'ALL',
            startDate: $start, endDate: $end, academicPeriodId: null,
            isOfficiallyRequired: false, activationPrerequisite: false, closurePrerequisite: false, sourceReference: null,
        ));
        echo json_encode(['status' => 'created', 'id' => $id, 'code' => $code], JSON_THROW_ON_ERROR);
    } catch (DomainException $e) {
        echo json_encode(['status' => 'rejected', 'code' => $code, 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
    }
} catch (Throwable $e) {
    fwrite(STDERR, $e::class . ': ' . $e->getMessage() . PHP_EOL);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
    exit(2);
}
