<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ACA-YEAR-02 test fixture ONLY.
 * This is not a product migration and must never be deployed to CENICOM.
 * It supplies only the target key needed by the FK smoke test in the isolated DB.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (Schema::hasTable('institutions')) {
            throw new RuntimeException(
                'ACA-YEAR-02 fixture rechazado: institutions ya existe en la base aislada; inspeccione su origen.'
            );
        }

        Schema::create('institutions', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->char('id', 26)->primary();
            $table->comment('SOLO TEST: soporte minimo para FK ACA-YEAR-02; no es tabla de producto.');
        });
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();
        Schema::dropIfExists('institutions');
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: APP_ENV debe ser testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: conexión por defecto debe ser mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: existe configuración cacheada.');
        }

        $config = (array) config('database.connections.mysql', []);
        $expected = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3308',
            'database' => 'cenicom_aca_year02_isolated_test',
            'username' => 'aca_year02_test',
        ];

        foreach ($expected as $key => $value) {
            $actual = $config[$key] ?? null;
            if ($key === 'port') {
                $actual = $actual === null ? null : (string) $actual;
            }
            if ($actual !== $value) {
                throw new RuntimeException(sprintf(
                    'ACA-YEAR-02 fixture rechazado: database.connections.mysql.%s debe ser %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: DB_URL debe estar vacío.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: DB_PASSWORD debe estar definida y no vacía.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        if (($row->current_database ?? null) !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException('ACA-YEAR-02 fixture rechazado: base efectiva no autorizada.');
        }
    }
};
