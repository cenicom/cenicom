<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DATABASE = 'cenicom_aca_year02_product_integration_test';
    private const USER = 'aca_year02_int01_test';

    public function up(): void
    {
        $this->assertIntegrationTarget();

        $existing = DB::select(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME <> 'migrations'",
        );
        if ($existing !== []) {
            throw new RuntimeException(
                'INT-01 baseline detenido: la base no está vacía fuera de la tabla migrations; inspeccione el esquema antes de continuar.',
            );
        }

        Schema::create('institutions', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->char('id', 26)->primary();
            $table->string('code', 20)->unique();
            $table->string('short_code', 15)->unique();
            $table->string('name', 255);
            $table->string('official_registration_country', 2)->nullable();
            $table->string('official_registration_authority', 150)->nullable();
            $table->string('official_registration_value', 100)->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });

        Schema::create('audit_entries', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('actor_id')->nullable();
            $table->string('actor_name');
            $table->boolean('actor_authenticated');
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->string('subject_id')->nullable();
            $table->json('metadata');
            $table->string('result');
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->index(['actor_id', 'occurred_at']);
            $table->index(['action', 'occurred_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        $this->assertIntegrationTarget();

        foreach ([
            'academic_regulatory_authorities',
            'official_calendar_publications',
            'institution_academic_profiles',
            'official_calendar_publication_versions',
            'academic_years',
            'academic_periods',
            'academic_calendar_events',
            'academic_year_event_completions',
        ] as $table) {
            if (Schema::hasTable($table)) {
                throw new RuntimeException(
                    'INT-01 baseline rollback detenido: primero debe revisar/revertir el esquema académico de validación; no se eliminarán tablas base automáticamente.',
                );
            }
        }

        if (Schema::hasTable('audit_entries') && DB::table('audit_entries')->exists()) {
            throw new RuntimeException(
                'INT-01 baseline rollback detenido: audit_entries contiene datos; preserve la evidencia y revise manualmente.',
            );
        }

        Schema::dropIfExists('audit_entries');
        Schema::dropIfExists('institutions');
    }

    private function assertIntegrationTarget(): void
    {
        if (app()->environment() !== 'testing') {
            throw new RuntimeException('INT-01 baseline bloqueado: APP_ENV debe ser testing.');
        }

        if (
            config('database.default') !== 'mysql'
            || (string) config('database.connections.mysql.host') !== '127.0.0.1'
            || (string) config('database.connections.mysql.port') !== '3308'
            || (string) config('database.connections.mysql.database') !== self::DATABASE
            || (string) config('database.connections.mysql.username') !== self::USER
            || trim((string) config('database.connections.mysql.url')) !== ''
            || trim((string) config('database.connections.mysql.password')) === ''
            || is_file(base_path('bootstrap/cache/config.php'))
        ) {
            throw new RuntimeException('INT-01 baseline bloqueado: configuración no coincide con la base/usuario de integración autorizados.');
        }

        $identity = DB::connection('mysql')->selectOne(
            'SELECT DATABASE() AS database_name, CURRENT_USER() AS effective_account',
        );

        $effectiveAccount = strtolower((string) ($identity->effective_account ?? ''));
        $allowedAccounts = [
            strtolower(self::USER.'@127.0.0.1'),
            strtolower(self::USER.'@localhost'),
        ];

        if (
            ($identity->database_name ?? null) !== self::DATABASE
            || ! in_array($effectiveAccount, $allowedAccounts, true)
        ) {
            throw new RuntimeException('INT-01 baseline bloqueado: el servidor no confirma la base y cuenta de integración esperadas.');
        }
    }
};
