<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INSERT_TRIGGER = 'trg_aca_product_ocpv_immutable_insert';
    private const UPDATE_TRIGGER = 'trg_aca_product_ocpv_immutable_update';
    private const DELETE_TRIGGER = 'trg_aca_product_ocpv_immutable_delete';

    public function up(): void
    {
        if (! Schema::hasTable('official_calendar_publication_versions')) {
            throw new RuntimeException(
                'ACA-F04 rechazado: falta official_calendar_publication_versions; aplique primero las migraciones ACA-YEAR-02 anteriores.',
            );
        }

        // These trigger names are reserved for the ACA product migration.
        // Replacing them makes recovery from a partially applied migration deterministic.
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::INSERT_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::UPDATE_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::DELETE_TRIGGER);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_ocpv_immutable_insert
            BEFORE INSERT ON official_calendar_publication_versions
            FOR EACH ROW
            BEGIN
                IF NEW.verification_status <> 'draft' THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: una version oficial nueva debe empezar en draft y avanzar mediante transiciones autorizadas.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_ocpv_immutable_update
            BEFORE UPDATE ON official_calendar_publication_versions
            FOR EACH ROW
            BEGIN
                IF NEW.verification_status = 'available' AND NOT EXISTS (
                    SELECT 1
                    FROM official_calendar_publications AS publication
                    WHERE publication.id = NEW.publication_id
                      AND publication.identity_status = 'confirmed'
                      AND publication.calendar_code IN ('A', 'B')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: una version no puede estar disponible sin identidad confirmada y calendario A/B resuelto.';
                END IF;

                IF NOT (
                    (OLD.verification_status = 'draft' AND NEW.verification_status IN ('draft', 'validated'))
                    OR (OLD.verification_status = 'validated' AND NEW.verification_status IN ('validated', 'available', 'withdrawn'))
                    OR (OLD.verification_status = 'available' AND NEW.verification_status IN ('available', 'withdrawn'))
                    OR (OLD.verification_status = 'withdrawn' AND NEW.verification_status = 'withdrawn')
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: transicion de estado de version oficial no permitida.';
                END IF;

                IF NOT (NEW.withdrawn_at <=> OLD.withdrawn_at)
                   AND NOT (
                        OLD.withdrawn_at IS NULL
                        AND NEW.withdrawn_at IS NOT NULL
                        AND NEW.verification_status = 'withdrawn'
                   ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: withdrawn_at solo puede establecerse una vez al retirar la version.';
                END IF;

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
                            SET MESSAGE_TEXT = 'ACA-INT-01: el contenido de una version validada es inmutable; cree una nueva version.';
                    END IF;

                    IF NEW.verification_status = 'draft' THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ACA-INT-01: una version validada no puede regresar a un estado anterior.';
                    END IF;

                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_ocpv_immutable_delete
            BEFORE DELETE ON official_calendar_publication_versions
            FOR EACH ROW
            BEGIN
                IF OLD.verification_status IN ('validated', 'available', 'withdrawn') THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: una version validada no puede eliminarse; conserve la trazabilidad y cree una nueva version.';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::DELETE_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::UPDATE_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::INSERT_TRIGGER);
    }


};
