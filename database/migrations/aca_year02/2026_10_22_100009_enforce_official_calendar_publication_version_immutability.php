<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const UPDATE_TRIGGER = 'trg_ocpv_guard_immutable_update';
    private const DELETE_TRIGGER = 'trg_ocpv_guard_immutable_delete';

    public function up(): void
    {
        $this->assertIsolatedDatabase();

        if (! Schema::connection('mysql')->hasTable('official_calendar_publication_versions')) {
            throw new \RuntimeException(
                'ACA-F04 rechazado: falta official_calendar_publication_versions; aplique primero las migraciones ACA-YEAR-02 anteriores.',
            );
        }

        // These named triggers belong exclusively to this isolated test migration.
        // Replacing them makes recovery from a partially applied test migration deterministic.
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::UPDATE_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::DELETE_TRIGGER);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ocpv_guard_immutable_update
            BEFORE UPDATE ON official_calendar_publication_versions
            FOR EACH ROW
            BEGIN
                IF OLD.verification_status IN ('validated', 'available', 'withdrawn') THEN
                    IF NOT (
                        NEW.id <=> OLD.id
                        AND NEW.publication_id <=> OLD.publication_id
                        AND NEW.version_number <=> OLD.version_number
                        AND NEW.supersedes_version_id <=> OLD.supersedes_version_id
                        AND NEW.official_version_label <=> OLD.official_version_label
                        AND NEW.authority_name_snapshot <=> OLD.authority_name_snapshot
                        AND NEW.title <=> OLD.title
                        AND NEW.promulgated_on <=> OLD.promulgated_on
                        AND NEW.calendar_start_date <=> OLD.calendar_start_date
                        AND NEW.calendar_end_date <=> OLD.calendar_end_date
                        AND NEW.source_url <=> OLD.source_url
                        AND NEW.source_reference <=> OLD.source_reference
                        AND NEW.document_sha256 <=> OLD.document_sha256
                        AND NEW.validated_at <=> OLD.validated_at
                        AND NEW.created_at <=> OLD.created_at
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ACA-F04: el contenido de una version validada es inmutable; cree una nueva version.';
                    END IF;

                    IF NEW.verification_status = 'draft' THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ACA-F04: una version validada no puede regresar a draft.';
                    END IF;

                    IF NOT (NEW.withdrawn_at <=> OLD.withdrawn_at)
                       AND NOT (
                            OLD.withdrawn_at IS NULL
                            AND NEW.withdrawn_at IS NOT NULL
                            AND NEW.verification_status = 'withdrawn'
                       ) THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ACA-F04: withdrawn_at solo puede establecerse al retirar la version y no puede reescribirse.';
                    END IF;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ocpv_guard_immutable_delete
            BEFORE DELETE ON official_calendar_publication_versions
            FOR EACH ROW
            BEGIN
                IF OLD.verification_status IN ('validated', 'available', 'withdrawn') THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F04: una version validada no puede eliminarse; conserve la trazabilidad y cree una nueva version.';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        DB::unprepared('DROP TRIGGER IF EXISTS '.self::DELETE_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::UPDATE_TRIGGER);
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new \RuntimeException('ACA-YEAR-02 rechazado: APP_ENV debe ser testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new \RuntimeException('ACA-YEAR-02 rechazado: la conexión por defecto debe ser mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new \RuntimeException('ACA-YEAR-02 rechazado: existe bootstrap/cache/config.php. No se ejecuta con configuración cacheada.');
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
                throw new \RuntimeException(sprintf(
                    'ACA-YEAR-02 rechazado: database.connections.mysql.%s debe ser %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new \RuntimeException('ACA-YEAR-02 rechazado: DB_URL debe estar vacío.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new \RuntimeException('ACA-YEAR-02 rechazado: DB_PASSWORD debe estar configurada y no vacía.');
        }

        // Open the database connection only after the configuration points to the exact test database.
        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        $actualDatabase = $row->current_database ?? null;
        if ($actualDatabase !== 'cenicom_aca_year02_isolated_test') {
            throw new \RuntimeException(sprintf(
                'ACA-YEAR-02 rechazado: la base efectiva es %s, no cenicom_aca_year02_isolated_test.',
                var_export($actualDatabase, true),
            ));
        }
    }
};
