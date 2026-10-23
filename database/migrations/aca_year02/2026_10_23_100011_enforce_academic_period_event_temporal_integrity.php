<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TRIGGERS = [
        'trg_ac_period_integrity_bi',
        'trg_ac_period_integrity_bu',
        'trg_ac_event_integrity_bi',
        'trg_ac_event_integrity_bu',
    ];

    public function up(): void
    {
        $this->assertIsolatedDatabase();

        foreach ([
            'official_calendar_publication_versions',
            'academic_periods',
            'academic_calendar_events',
        ] as $table) {
            if (! Schema::connection('mysql')->hasTable($table)) {
                throw new RuntimeException(sprintf(
                    'ACA-F07 rechazado: falta la tabla %s; aplique primero las migraciones ACA-YEAR-02 anteriores.',
                    $table,
                ));
            }
        }

        // A failed manual attempt can be retried safely without changing existing data.
        foreach (self::TRIGGERS as $trigger) {
            DB::unprepared('DROP TRIGGER IF EXISTS '.$trigger);
        }

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ac_period_integrity_bi
            BEFORE INSERT ON academic_periods
            FOR EACH ROW
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.calendar_start_date IS NOT NULL
                      AND publication_version.calendar_end_date IS NOT NULL
                      AND NEW.start_date >= publication_version.calendar_start_date
                      AND NEW.end_date <= publication_version.calendar_end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el periodo debe quedar dentro de las fechas oficiales de su version de publicacion.';
                END IF;

                IF EXISTS (
                    SELECT 1
                    FROM academic_periods AS existing_period
                    WHERE existing_period.publication_version_id = NEW.publication_version_id
                      AND existing_period.id <> NEW.id
                      AND existing_period.start_date <= NEW.end_date
                      AND existing_period.end_date >= NEW.start_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: los periodos de una misma version oficial no pueden solaparse; los intervalos vacios entre periodos si estan permitidos.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ac_period_integrity_bu
            BEFORE UPDATE ON academic_periods
            FOR EACH ROW
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.calendar_start_date IS NOT NULL
                      AND publication_version.calendar_end_date IS NOT NULL
                      AND NEW.start_date >= publication_version.calendar_start_date
                      AND NEW.end_date <= publication_version.calendar_end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el periodo actualizado debe quedar dentro de las fechas oficiales de su version de publicacion.';
                END IF;

                IF EXISTS (
                    SELECT 1
                    FROM academic_periods AS existing_period
                    WHERE existing_period.publication_version_id = NEW.publication_version_id
                      AND existing_period.id <> OLD.id
                      AND existing_period.start_date <= NEW.end_date
                      AND existing_period.end_date >= NEW.start_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: la actualizacion solaparia periodos de una misma version oficial.';
                END IF;

                IF EXISTS (
                    SELECT 1
                    FROM academic_calendar_events AS existing_event
                    WHERE existing_event.academic_period_id = OLD.id
                      AND existing_event.publication_version_id = OLD.publication_version_id
                      AND (
                          existing_event.publication_version_id <> NEW.publication_version_id
                          OR existing_event.start_date < NEW.start_date
                          OR existing_event.end_date > NEW.end_date
                      )
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el periodo no puede cambiar de version ni de intervalo dejando eventos asociados fuera de sus fechas.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ac_event_integrity_bi
            BEFORE INSERT ON academic_calendar_events
            FOR EACH ROW
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.calendar_start_date IS NOT NULL
                      AND publication_version.calendar_end_date IS NOT NULL
                      AND NEW.start_date >= publication_version.calendar_start_date
                      AND NEW.end_date <= publication_version.calendar_end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento debe quedar dentro de las fechas oficiales de su version de publicacion.';
                END IF;

                IF NEW.event_type = 'EXAM_INTERVAL' AND NEW.academic_period_id IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: un intervalo de examenes debe pertenecer a un periodo academico.';
                END IF;

                IF NEW.academic_period_id IS NOT NULL AND NOT EXISTS (
                    SELECT 1
                    FROM academic_periods AS academic_period
                    WHERE academic_period.id = NEW.academic_period_id
                      AND academic_period.publication_version_id = NEW.publication_version_id
                      AND NEW.start_date >= academic_period.start_date
                      AND NEW.end_date <= academic_period.end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento debe quedar dentro de su periodo academico y de la misma version oficial.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_ac_event_integrity_bu
            BEFORE UPDATE ON academic_calendar_events
            FOR EACH ROW
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.calendar_start_date IS NOT NULL
                      AND publication_version.calendar_end_date IS NOT NULL
                      AND NEW.start_date >= publication_version.calendar_start_date
                      AND NEW.end_date <= publication_version.calendar_end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento actualizado debe quedar dentro de las fechas oficiales de su version.';
                END IF;

                IF NEW.event_type = 'EXAM_INTERVAL' AND NEW.academic_period_id IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: un intervalo de examenes debe pertenecer a un periodo academico.';
                END IF;

                IF NEW.academic_period_id IS NOT NULL AND NOT EXISTS (
                    SELECT 1
                    FROM academic_periods AS academic_period
                    WHERE academic_period.id = NEW.academic_period_id
                      AND academic_period.publication_version_id = NEW.publication_version_id
                      AND NEW.start_date >= academic_period.start_date
                      AND NEW.end_date <= academic_period.end_date
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento actualizado debe quedar dentro de su periodo academico y de la misma version oficial.';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        $this->assertIsolatedDatabase();

        foreach (self::TRIGGERS as $trigger) {
            DB::unprepared('DROP TRIGGER IF EXISTS '.$trigger);
        }
    }

    private function assertIsolatedDatabase(): void
    {
        if (! app()->environment('testing')) {
            throw new RuntimeException('ACA-F07 rechazado: APP_ENV debe ser testing.');
        }

        if ((string) config('database.default') !== 'mysql') {
            throw new RuntimeException('ACA-F07 rechazado: la conexion por defecto debe ser mysql.');
        }

        if (is_file(base_path('bootstrap/cache/config.php'))) {
            throw new RuntimeException('ACA-F07 rechazado: existe bootstrap/cache/config.php. No se ejecuta con configuracion cacheada.');
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
                    'ACA-F07 rechazado: database.connections.mysql.%s debe ser %s.',
                    $key,
                    var_export($value, true),
                ));
            }
        }

        if (! empty($config['url'])) {
            throw new RuntimeException('ACA-F07 rechazado: DB_URL debe estar vacio.');
        }

        if (trim((string) ($config['password'] ?? '')) === '') {
            throw new RuntimeException('ACA-F07 rechazado: DB_PASSWORD debe estar definida y no vacia.');
        }

        $row = DB::connection('mysql')->selectOne('SELECT DATABASE() AS current_database');
        $actualDatabase = $row->current_database ?? null;
        if ($actualDatabase !== 'cenicom_aca_year02_isolated_test') {
            throw new RuntimeException(sprintf(
                'ACA-F07 rechazado: la base efectiva es %s, no cenicom_aca_year02_isolated_test.',
                var_export($actualDatabase, true),
            ));
        }
    }
};
