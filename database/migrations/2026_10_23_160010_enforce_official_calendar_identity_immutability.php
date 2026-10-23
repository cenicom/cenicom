<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUTHORITY_TRIGGER = 'trg_aca_product_authority_identity_immutable';
    private const PUBLICATION_TRIGGER = 'trg_aca_product_publication_identity_immutable';

    public function up(): void
    {
        foreach ([
            'academic_regulatory_authorities',
            'official_calendar_publications',
            'official_calendar_publication_versions',
        ] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException(sprintf(
                    'ACA-INT-01: falta la tabla %s; aplique primero las migraciones anteriores.',
                    $table,
                ));
            }
        }

        DB::unprepared('DROP TRIGGER IF EXISTS '.self::AUTHORITY_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::PUBLICATION_TRIGGER);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_authority_identity_immutable
            BEFORE UPDATE ON academic_regulatory_authorities
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publications AS publication
                    WHERE publication.authority_id = OLD.id
                ) AND (
                    NOT (NEW.id <=> OLD.id)
                    OR NOT (NEW.country_code <=> OLD.country_code)
                    OR NOT (NEW.authority_code <=> OLD.authority_code)
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: la identidad de una autoridad usada por publicaciones no se puede cambiar; registre una nueva identidad si corresponde.';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_aca_product_publication_identity_immutable
            BEFORE UPDATE ON official_calendar_publications
            FOR EACH ROW
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM official_calendar_publication_versions AS publication_version
                    WHERE publication_version.publication_id = OLD.id
                      AND publication_version.verification_status IN ('validated', 'available', 'withdrawn')
                ) AND (
                    NOT (NEW.id <=> OLD.id)
                    OR NOT (NEW.authority_id <=> OLD.authority_id)
                    OR NOT (NEW.document_type <=> OLD.document_type)
                    OR NOT (NEW.document_number <=> OLD.document_number)
                    OR NOT (NEW.document_year <=> OLD.document_year)
                    OR NOT (NEW.reference_period <=> OLD.reference_period)
                    OR NOT (NEW.jurisdiction_code <=> OLD.jurisdiction_code)
                    OR NOT (NEW.regime_code <=> OLD.regime_code)
                    OR NOT (NEW.region_code <=> OLD.region_code)
                    OR NOT (NEW.calendar_code <=> OLD.calendar_code)
                    OR NOT (NEW.identity_status <=> OLD.identity_status)
                    OR NOT (NEW.identity_fingerprint <=> OLD.identity_fingerprint)
                ) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'ACA-INT-01: la identidad y compatibilidad de una publicación con versiones validadas no se pueden modificar en sitio.';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::PUBLICATION_TRIGGER);
        DB::unprepared('DROP TRIGGER IF EXISTS '.self::AUTHORITY_TRIGGER);
    }
};
