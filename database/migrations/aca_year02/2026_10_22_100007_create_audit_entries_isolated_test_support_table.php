<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * TEST FIXTURE ONLY. Minimal schema required to exercise the existing AuditRecorder
 * against the isolated ACA-YEAR-02 database. Never deploy this migration to CENICOM.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (Schema::hasTable('audit_entries')) {
            throw new RuntimeException(
                'ACA-YEAR-02 audit fixture refused: audit_entries already exists; inspect its provenance.',
            );
        }

        Schema::create('audit_entries', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->string('actor_id', 100)->nullable();
            $table->string('actor_name', 255)->nullable();
            $table->boolean('actor_authenticated')->default(false);
            $table->string('action', 180);
            $table->string('subject_type', 100)->nullable();
            $table->string('subject_id', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->string('result', 40);
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->index(['subject_type', 'subject_id'], 'ix_aca_test_audit_subject');
            $table->index(['action', 'occurred_at'], 'ix_aca_test_audit_action_time');
            $table->comment('SOLO TEST ACA-YEAR-02; no es la migracion de auditoria del producto.');
        });
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        if (! Schema::hasTable('audit_entries')) {
            return;
        }

        if (DB::table('audit_entries')->exists()) {
            throw new RuntimeException(
                'ACA-YEAR-02 audit fixture rollback refused: audit_entries contains rows.',
            );
        }

        Schema::drop('audit_entries');
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: APP_ENV must be testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: default connection must be mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: configuration cache exists.');
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
                    'ACA-YEAR-02 audit fixture rejected: mysql.%s must be %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: DB_URL must be empty.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: DB_PASSWORD must be non-empty.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        if (($row->current_database ?? null) !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException('ACA-YEAR-02 audit fixture rejected: effective database is not authorized.');
        }
    }
};
