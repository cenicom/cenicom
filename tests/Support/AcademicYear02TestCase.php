<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Contracts\Console\Kernel;
use RuntimeException;

abstract class AcademicYear02TestCase extends TestCase
{
    public function createApplication()
    {
        $projectRoot = dirname(__DIR__, 2);

        $app = require $projectRoot . '/bootstrap/app.php';

        $app->afterBootstrapping(
            RegisterProviders::class,
            function (Application $app): void {
                $config = $app->make('config');

                $mysql = $config->get(
                    'database.connections.mysql',
                    [],
                );

                $expectedConfig = [
                    'app.env' => 'testing',
                    'database.default' => 'mysql',
                ];

                $errors = [];

                foreach ($expectedConfig as $key => $expectedValue) {
                    if ($config->get($key) !== $expectedValue) {
                        $errors[] = $key;
                    }
                }

                $expectedMysql = [
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'port' => '3308',
                    'database' =>
                        AcademicYear02DatabaseGuard::EXPECTED_DATABASE,
                    'username' => 'aca_year02_test',
                ];

                foreach ($expectedMysql as $key => $expectedValue) {
                    $actualValue = $mysql[$key] ?? null;

                    if ((string) $actualValue !== $expectedValue) {
                        $errors[] = 'database.connections.mysql.' . $key;
                    }
                }

                if (empty($mysql['password'] ?? null)) {
                    $errors[] =
                        'database.connections.mysql.password';
                }

                if (! empty($mysql['url'] ?? null)) {
                    $errors[] = 'database.connections.mysql.url';
                }

                if ($errors !== []) {
                    throw new RuntimeException(
                        'ACA-YEAR-02 rechazó la configuración efectiva: '
                        . implode(', ', $errors),
                    );
                }

                /*
                 * Consulta de solo lectura. No ejecuta migraciones,
                 * escrituras ni pruebas funcionales.
                 */
                $row = $app->make('db')
                    ->connection('mysql')
                    ->selectOne(
                        'SELECT DATABASE() AS database_name',
                    );

                $actualDatabase = (string) (
                    $row->database_name ?? ''
                );

                AcademicYear02DatabaseGuard::assertAuthorizedDatabase(
                    $actualDatabase,
                );
            },
        );

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
