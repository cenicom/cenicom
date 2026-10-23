<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TRIGGERS = [
        'trg_aca_product_period_integrity_bi',
        'trg_aca_product_period_integrity_bu',
        'trg_aca_product_period_integrity_bd',
        'trg_aca_product_event_integrity_bi',
        'trg_aca_product_event_integrity_bu',
        'trg_aca_product_event_integrity_bd',
    ];

    public function up(): void
    {
        foreach ([
            'official_calendar_publication_versions',
            'academic_periods',
            'academic_calendar_events',
        ] as $table) {
            if (! Schema::hasTable($table)) {
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
            CREATE TRIGGER trg_aca_product_period_integrity_bi
            BEFORE INSERT ON academic_periods
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden agregar periodos a una version oficial validada, disponible o retirada.';
                END IF;

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
            CREATE TRIGGER trg_aca_product_period_integrity_bu
            BEFORE UPDATE ON academic_periods
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = OLD.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) OR EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden modificar periodos de versiones oficiales validadas, disponibles o retiradas.';
                END IF;

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
            CREATE TRIGGER trg_aca_product_event_integrity_bi
            BEFORE INSERT ON academic_calendar_events
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden agregar eventos a una version oficial validada, disponible o retirada.';
                END IF;

                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento debe referenciar una version oficial existente.';
                END IF;

                -- Algunas fechas oficiales de docentes/recesos pueden rebasar el rango de cierre institucional.
                -- Si el evento sale del rango principal (o este aún no se define), exige una referencia específica.
                IF (
                    NOT EXISTS (
                        SELECT 1
                        FROM official_calendar_publication_versions AS publication_version
                        WHERE publication_version.id = NEW.publication_version_id
                          AND publication_version.calendar_start_date IS NOT NULL
                          AND publication_version.calendar_end_date IS NOT NULL
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM official_calendar_publication_versions AS publication_version
                        WHERE publication_version.id = NEW.publication_version_id
                          AND publication_version.calendar_start_date IS NOT NULL
                          AND publication_version.calendar_end_date IS NOT NULL
                          AND (
                              NEW.start_date < publication_version.calendar_start_date
                              OR NEW.end_date > publication_version.calendar_end_date
                          )
                    )
                ) AND (
                    NEW.source_reference IS NULL
                    OR CHAR_LENGTH(TRIM(NEW.source_reference)) = 0
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: un evento fuera del rango institucional (o sin rango definido) requiere referencia oficial específica.';
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
            CREATE TRIGGER trg_aca_product_event_integrity_bu
            BEFORE UPDATE ON academic_calendar_events
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = OLD.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) OR EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden modificar eventos de versiones oficiales validadas, disponibles o retiradas.';
                END IF;

                IF NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = NEW.publication_version_id
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: el evento debe referenciar una version oficial existente.';
                END IF;

                IF (
                    NOT EXISTS (
                        SELECT 1
                        FROM official_calendar_publication_versions AS publication_version
                        WHERE publication_version.id = NEW.publication_version_id
                          AND publication_version.calendar_start_date IS NOT NULL
                          AND publication_version.calendar_end_date IS NOT NULL
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM official_calendar_publication_versions AS publication_version
                        WHERE publication_version.id = NEW.publication_version_id
                          AND publication_version.calendar_start_date IS NOT NULL
                          AND publication_version.calendar_end_date IS NOT NULL
                          AND (
                              NEW.start_date < publication_version.calendar_start_date
                              OR NEW.end_date > publication_version.calendar_end_date
                          )
                    )
                ) AND (
                    NEW.source_reference IS NULL
                    OR CHAR_LENGTH(TRIM(NEW.source_reference)) = 0
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-F07: un evento fuera del rango institucional (o sin rango definido) requiere referencia oficial específica.';
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

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_period_integrity_bd
            BEFORE DELETE ON academic_periods
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = OLD.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden eliminar periodos de una version oficial validada, disponible o retirada.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_event_integrity_bd
            BEFORE DELETE ON academic_calendar_events
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.id = OLD.publication_version_id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: no se pueden eliminar eventos de una version oficial validada, disponible o retirada.';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        foreach (self::TRIGGERS as $trigger) {
            DB::unprepared('DROP TRIGGER IF EXISTS '.$trigger);
        }
    }


};
