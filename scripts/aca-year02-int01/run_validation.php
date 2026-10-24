<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

$root = dirname(__DIR__, 2);
$targetDatabase = 'cenicom_aca_year02_product_integration_test';
$targetUser = 'aca_year02_int01_test';
$expectedCandidateFiles = [
    '2026_10_23_160001_create_academic_regulatory_authorities_and_official_calendar_publications_tables.php',
    '2026_10_23_160002_create_institution_academic_profiles_table.php',
    '2026_10_23_160003_create_official_calendar_publication_versions_table.php',
    '2026_10_23_160004_create_academic_years_table.php',
    '2026_10_23_160005_create_academic_periods_table.php',
    '2026_10_23_160006_create_academic_calendar_events_table.php',
    '2026_10_23_160007_create_academic_year_event_completions_table.php',
    '2026_10_23_160008_enforce_official_calendar_publication_version_immutability.php',
    '2026_10_23_160009_enforce_academic_period_event_temporal_integrity.php',
    '2026_10_23_160010_enforce_official_calendar_identity_immutability.php',
];
$baselineRelativePath = 'database/migrations/aca_year02_int01_validation_support';
$candidateRelativePath = 'database/migrations/aca_year02_product_candidate';

function int01Fail(string $message): never
{
    fwrite(STDERR, "INT-01 DETENIDO: {$message}\n");
    exit(1);
}

function int01Ulid(): string
{
    return (string) Str::ulid();
}

function int01Check(bool $condition, string $message, int &$failures): void
{
    if ($condition) {
        echo "PASS: {$message}\n";
        return;
    }

    echo "FAIL: {$message}\n";
    $failures++;
}

/** The caller owns an outer transaction; this helper uses a nested transaction/savepoint. */
function int01ExpectRejected(string $label, string $marker, callable $operation, int &$failures): void
{
    $connection = DB::connection('mysql');
    $connection->beginTransaction();

    try {
        $operation();
        $connection->rollBack();
        int01Check(false, $label.' (la operación fue aceptada, se esperaba rechazo)', $failures);
    } catch (Throwable $exception) {
        if ($connection->transactionLevel() > 1) {
            $connection->rollBack();
        }

        int01Check(
            str_contains($exception->getMessage(), $marker),
            $label.' (rechazo con la regla esperada)',
            $failures,
        );
        if (! str_contains($exception->getMessage(), $marker)) {
            echo '  Evidencia del error: '.preg_replace('/\s+/', ' ', $exception->getMessage())."\n";
        }
    }
}

function int01AssertTarget(array $config, string $root, string $targetDatabase, string $targetUser): object
{
    if (app()->environment() !== 'testing') {
        int01Fail('APP_ENV no es testing. No se ejecutó DDL.');
    }
    if (is_file($root.'/bootstrap/cache/config.php')) {
        int01Fail('existe bootstrap/cache/config.php; la configuración efectiva no puede probarse de forma segura. No se ejecutó DDL.');
    }
    if (
        ($config['default'] ?? null) !== 'mysql'
        || (string) ($config['connections']['mysql']['host'] ?? '') !== '127.0.0.1'
        || (string) ($config['connections']['mysql']['port'] ?? '') !== '3308'
        || (string) ($config['connections']['mysql']['database'] ?? '') !== $targetDatabase
        || (string) ($config['connections']['mysql']['username'] ?? '') !== $targetUser
        || trim((string) ($config['connections']['mysql']['url'] ?? '')) !== ''
        || trim((string) ($config['connections']['mysql']['password'] ?? '')) === ''
    ) {
        int01Fail('configuración no coincide con host/puerto/base/usuario exclusivamente autorizados para INT-01. No se ejecutó DDL.');
    }

    $identity = DB::connection('mysql')->selectOne(
        'SELECT DATABASE() AS database_name, CURRENT_USER() AS effective_account, VERSION() AS server_version',
    );

    $effectiveAccount = strtolower((string) ($identity->effective_account ?? ''));
    $allowedAccounts = [
        strtolower($targetUser.'@127.0.0.1'),
        strtolower($targetUser.'@localhost'),
    ];
    if (
        ($identity->database_name ?? null) !== $targetDatabase
        || ! in_array($effectiveAccount, $allowedAccounts, true)
    ) {
        int01Fail('SELECT DATABASE()/CURRENT_USER() no confirma la base y cuenta previstas. Se permiten solo las cuentas dedicadas @127.0.0.1 o @localhost; no se ejecutó DDL.');
    }

    $version = (string) ($identity->server_version ?? '');
    if (stripos($version, 'mariadb') === false || ! preg_match('/^10\.4\./', $version)) {
        int01Fail('servidor no identificado como MariaDB 10.4.x; no se ejecutó DDL. Versión detectada: '.$version);
    }

    // Enforce database-scoped grants for the temporary integration account. Never print grant text,
    // because SHOW GRANTS may include a password hash depending on server/account configuration.
    foreach (DB::connection('mysql')->select('SHOW GRANTS FOR CURRENT_USER()') as $grantRow) {
        $grantValues = array_values((array) $grantRow);
        $grantText = (string) ($grantValues[0] ?? '');
        if (
            preg_match('/\bon\s+`?\*`?\s*\.\s*\*/i', $grantText)
            && ! preg_match('/^\s*GRANT\s+USAGE\s+ON\s+\*/i', $grantText)
        ) {
            int01Fail('la cuenta de integración tiene un privilegio global; revóquelo y conceda permisos solamente en la base INT-01. No se ejecutó DDL.');
        }
        if (
            preg_match('/\bon\s+`?([A-Za-z0-9_]+)`?\s*\.\s*\*/i', $grantText, $matches)
            && strtolower($matches[1]) !== strtolower($targetDatabase)
        ) {
            int01Fail('la cuenta de integración tiene permisos sobre otra base de datos; restrinja los GRANT a la base INT-01. No se ejecutó DDL.');
        }
    }

    return $identity;
}

if (! is_file($root.'/vendor/autoload.php') || ! is_file($root.'/bootstrap/app.php')) {
    int01Fail('este script debe ejecutarse desde el árbol del proyecto Laravel CENICOM.');
}

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    $identity = int01AssertTarget(config('database'), $root, $targetDatabase, $targetUser);
} catch (Throwable $exception) {
    $message = preg_replace('/\s+/', ' ', $exception->getMessage()) ?? 'error no detallado';
    int01Fail(
        'falló el preflight de conexión antes de ejecutar DDL. Verifique usuario, contraseña, host permitido y privilegios limitados a la base de integración. Detalle: '.get_class($exception).' — '.$message
    );
}

$candidateDirectory = $root.'/'.$candidateRelativePath;
$baselineDirectory = $root.'/'.$baselineRelativePath;
if (! is_dir($candidateDirectory) || ! is_dir($baselineDirectory)) {
    int01Fail('falta la carpeta candidata o la carpeta de soporte de validación. No se ejecutó DDL.');
}
$actualCandidateFiles = array_map(
    'basename',
    glob($candidateDirectory.'/*.php') ?: [],
);
$sortedExpectedCandidateFiles = $expectedCandidateFiles;
sort($sortedExpectedCandidateFiles);
sort($actualCandidateFiles);
if ($actualCandidateFiles !== $sortedExpectedCandidateFiles) {
    int01Fail('la carpeta candidata debe contener exactamente los diez archivos esperados. Presentes: '.implode(', ', $actualCandidateFiles).'. No se ejecutó DDL.');
}
foreach ($expectedCandidateFiles as $file) {
    if (! is_file($candidateDirectory.'/'.$file)) {
        int01Fail('falta archivo candidato '.$file.'. No se ejecutó DDL.');
    }
}
if (! is_file($root.'/'.$baselineRelativePath.'/2026_10_23_155900_create_int01_validation_baseline_tables.php')) {
    int01Fail('falta la única migración de baseline para la base de integración. No se ejecutó DDL.');
}

$expectedTables = [
    'academic_regulatory_authorities',
    'official_calendar_publications',
    'institution_academic_profiles',
    'official_calendar_publication_versions',
    'academic_years',
    'academic_periods',
    'academic_calendar_events',
    'academic_year_event_completions',
    'audit_entries',
    'institutions',
    'migrations',
];
$expectedMigrationNames = array_merge(
    ['2026_10_23_155900_create_int01_validation_baseline_tables'],
    array_map(static fn (string $file): string => pathinfo($file, PATHINFO_FILENAME), $expectedCandidateFiles),
);

$initialTableNames = array_map(
    static fn ($row): string => (string) $row->TABLE_NAME,
    DB::connection('mysql')->select(
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME",
    ),
);
$runMigrations = $initialTableNames === [];

if (! $runMigrations) {
    $observedTables = $initialTableNames;
    sort($expectedTables);
    sort($observedTables);
    if ($observedTables !== $expectedTables) {
        int01Fail('la base no está vacía y no coincide exactamente con el esquema completo esperado. No se ejecutó DDL ni limpieza. Tablas detectadas: '.implode(', ', $observedTables));
    }

    // Resume only when all 11 migrations are recorded. This is deliberately strict:
    // a partial schema or migration ledger must be inspected instead of retried.
    $actualMigrationNames = array_map(
        static fn ($row): string => (string) $row->migration,
        DB::connection('mysql')->table('migrations')->orderBy('migration')->get(['migration'])->all(),
    );
    $expectedRecordedMigrations = $expectedMigrationNames;
    sort($expectedRecordedMigrations);
    sort($actualMigrationNames);
    if ($actualMigrationNames !== $expectedRecordedMigrations) {
        int01Fail('la base contiene tablas pero el registro de migraciones no coincide exactamente con baseline + 10 candidatas. No se ejecutó DDL ni limpieza. Migraciones presentes: '.implode(', ', $actualMigrationNames));
    }
    $runMigrations = false;
}

echo "===== INT-01 VALIDACIÓN AISLADA =====\n";
echo 'Base efectiva: '.$identity->database_name."\n";
echo 'Cuenta efectiva: '.$identity->effective_account."\n";
echo 'Servidor: '.$identity->server_version."\n";
if ($runMigrations) {
    echo "Preflight: aprobado; base vacía. Se aplicará baseline y candidatas una sola vez.\n";
    foreach ([
        ['baseline', $baselineRelativePath],
        ['candidatas de producto', $candidateRelativePath],
    ] as [$label, $path]) {
        echo "\n===== MIGRACIÓN: {$label} =====\n";
        $exitCode = Artisan::call('migrate', [
            '--database' => 'mysql',
            '--path' => $path,
            '--force' => true,
        ]);
        echo Artisan::output();
        if ($exitCode !== 0) {
            int01Fail('falló el comando de migración '.$label.'. La base puede tener DDL parcial; conserve la salida e inspeccione information_schema antes de cualquier reintento.');
        }
    }
} else {
    echo "Preflight: esquema completo exacto y las 11 migraciones registradas. Se omite DDL y se continúa con la validación.\n";
}
$actualTables = array_map(
    static fn ($row): string => (string) $row->TABLE_NAME,
    DB::connection('mysql')->select(
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME",
    ),
);
sort($expectedTables);
sort($actualTables);
if ($expectedTables !== $actualTables) {
    int01Fail('las tablas presentes difieren del inventario esperado. Esperadas: '.implode(', ', $expectedTables).'; presentes: '.implode(', ', $actualTables).'. Conserve la base para inspección.');
}
echo "\nPASS: inventario exacto de 11 tablas (incluye baseline de integración y tabla migrations).\n";

$expectedForeignKeys = [
    'fk_ocp_authority', 'fk_iap_institution', 'fk_iap_authority',
    'fk_ocpv_publication', 'fk_ocpv_supersedes', 'fk_ay_institution',
    'fk_ay_publication_version', 'fk_apv_publication_version',
    'fk_acev_publication_version', 'fk_acev_period_version',
    'fk_ayec_year_version', 'fk_ayec_event_version',
];
$actualForeignKeys = array_map(
    static fn ($row): string => (string) $row->CONSTRAINT_NAME,
    DB::connection('mysql')->select(
        "SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL ORDER BY CONSTRAINT_NAME",
    ),
);
sort($expectedForeignKeys);
sort($actualForeignKeys);
if ($expectedForeignKeys !== $actualForeignKeys) {
    int01Fail('nombres de claves foráneas incorrectos. Esperadas: '.implode(', ', $expectedForeignKeys).'; presentes: '.implode(', ', $actualForeignKeys).'.');
}
echo "PASS: 12 claves foráneas con nombres esperados.\n";

$expectedChecks = [
    'chk_acreg_country_code', 'chk_acreg_authority_code',
    'chk_ocp_calendar_code', 'chk_ocp_identity_status',
    'chk_iap_country_code', 'chk_iap_authority_code', 'chk_iap_jurisdiction', 'chk_iap_regime', 'chk_iap_region', 'chk_iap_calendar',
    'chk_ocpv_version_number', 'chk_ocpv_version_status', 'chk_ocpv_version_dates', 'chk_ocpv_state_stamps', 'chk_ocpv_available_source', 'chk_ocpv_document_sha', 'chk_ocpv_not_self_supersede',
    'chk_ay_calendar_code', 'chk_ay_cycle_code', 'chk_ay_status_lifecycle',
    'chk_apv_dates', 'chk_apv_sequence', 'chk_apv_duration',
    'chk_acev_dates', 'chk_acev_type', 'chk_acev_audience', 'chk_acev_exam_period',
];
// MariaDB 10.4 automatically adds a JSON_VALID CHECK for the JSON alias column
// audit_entries.metadata. It is a legitimate server-generated constraint, not an extra
// product CHECK and must be verified by both name, table and expression.
$expectedChecksWithJsonAlias = array_merge($expectedChecks, ['metadata']);
$actualCheckRows = DB::connection('mysql')->select(
    "SELECT TABLE_NAME, CONSTRAINT_NAME, CHECK_CLAUSE FROM information_schema.CHECK_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() ORDER BY CONSTRAINT_NAME",
);
$actualChecks = array_map(
    static fn ($row): string => (string) $row->CONSTRAINT_NAME,
    $actualCheckRows,
);
sort($expectedChecksWithJsonAlias);
sort($actualChecks);
if ($expectedChecksWithJsonAlias !== $actualChecks) {
    int01Fail('restricciones CHECK difieren del inventario esperado. Esperadas (27 de aplicación + metadata JSON de MariaDB): '.implode(', ', $expectedChecksWithJsonAlias).'; presentes: '.implode(', ', $actualChecks).'.');
}
$metadataJsonChecks = array_values(array_filter(
    $actualCheckRows,
    static fn ($row): bool => strtolower((string) $row->TABLE_NAME) === 'audit_entries'
        && strtolower((string) $row->CONSTRAINT_NAME) === 'metadata',
));
$metadataJsonClause = count($metadataJsonChecks) === 1
    ? strtolower(preg_replace('/[`\s]+/', '', (string) $metadataJsonChecks[0]->CHECK_CLAUSE))
    : '';
if (! str_contains($metadataJsonClause, 'json_valid(metadata)')) {
    int01Fail('el CHECK adicional metadata no corresponde a JSON_VALID(metadata) en audit_entries; revise la definición real antes de continuar.');
}
echo "PASS: 27 CHECKs de aplicación + CHECK JSON_VALID(metadata) generado por MariaDB (28 en total).\n";

$expectedTriggers = [
    'trg_aca_product_ocpv_immutable_insert',
    'trg_aca_product_ocpv_immutable_update',
    'trg_aca_product_ocpv_immutable_delete',
    'trg_aca_product_period_integrity_bi',
    'trg_aca_product_period_integrity_bu',
    'trg_aca_product_period_integrity_bd',
    'trg_aca_product_event_integrity_bi',
    'trg_aca_product_event_integrity_bu',
    'trg_aca_product_event_integrity_bd',
    'trg_aca_product_authority_identity_immutable',
    'trg_aca_product_publication_identity_immutable',
];
$actualTriggers = array_map(
    static fn ($row): string => (string) $row->TRIGGER_NAME,
    DB::connection('mysql')->select(
        'SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE() ORDER BY TRIGGER_NAME',
    ),
);
sort($expectedTriggers);
sort($actualTriggers);
if ($expectedTriggers !== $actualTriggers) {
    int01Fail('los triggers presentes difieren del inventario esperado. Esperados: '.implode(', ', $expectedTriggers).'; presentes: '.implode(', ', $actualTriggers).'.');
}
echo "PASS: 11 triggers con nombres esperados.\n";

$expectedExplicitIndexes = [
    'uq_acreg_country_code', 'uq_ocp_identity_fingerprint', 'ix_ocp_compatibility',
    'uq_ocpv_pub_id', 'uq_ocpv_pub_version', 'ix_ocpv_status_calendar', 'ix_ocpv_supersedes',
    'uq_ay_institution_cycle', 'uq_ay_version_id', 'ix_ay_institution_status', 'ix_ay_version_status',
    'uq_apv_version_code', 'uq_apv_version_sequence', 'uq_apv_version_id', 'ix_apv_period_dates',
    'uq_acev_version_code', 'uq_acev_version_id', 'ix_acev_version_type_dates', 'ix_acev_period',
    'uq_ayec_year_event', 'ix_ayec_year_completed_at',
];
$actualIndexNames = array_unique(array_map(
    static fn ($row): string => (string) $row->INDEX_NAME,
    DB::connection('mysql')->select(
        'SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE()',
    ),
));
$missingIndexes = array_values(array_diff($expectedExplicitIndexes, $actualIndexNames));
if ($missingIndexes !== []) {
    int01Fail('faltan índices explícitos esperados: '.implode(', ', $missingIndexes).'.');
}
echo 'PASS: '.count($expectedExplicitIndexes)." índices explícitos localizados.\n";

$failures = 0;
$connection = DB::connection('mysql');
$connection->beginTransaction();
try {
    $now = date('Y-m-d H:i:s');
    $institutionId = int01Ulid();
    $authorityId = int01Ulid();
    $publicationId = int01Ulid();
    $versionId = int01Ulid();
    $periodId = int01Ulid();
    $vacationEventId = int01Ulid();
    $examEventId = int01Ulid();
    $yearId = int01Ulid();
    $fingerprint = hash('sha256', 'ACA-YEAR-02-INT01-fixture-'.int01Ulid());

    $connection->table('institutions')->insert([
        'id' => $institutionId,
        'code' => 'INT01-'.substr($institutionId, -8),
        'short_code' => 'I'.substr($institutionId, -10),
        'name' => 'Institución de validación INT-01',
        'official_registration_country' => 'CO',
        'official_registration_authority' => 'TEST',
        'official_registration_value' => 'INT01',
        'status' => 'draft',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('academic_regulatory_authorities')->insert([
        'id' => $authorityId,
        'country_code' => 'CO',
        'authority_code' => 'INT01_TEST',
        'official_name' => 'Autoridad de pruebas INT-01',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('official_calendar_publications')->insert([
        'id' => $publicationId,
        'authority_id' => $authorityId,
        'document_type' => 'SCHOOL_CALENDAR',
        'document_number' => 'INT01-TEST',
        'document_year' => 2026,
        'reference_period' => '2026',
        'jurisdiction_code' => 'TEST',
        'regime_code' => 'TEST',
        'region_code' => 'TEST',
        'calendar_code' => 'A',
        'identity_status' => 'confirmed',
        'identity_fingerprint' => $fingerprint,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('institution_academic_profiles')->insert([
        'institution_id' => $institutionId,
        'country_code' => 'CO',
        'authority_code' => 'INT01_TEST',
        'jurisdiction_code' => 'TEST',
        'regime_code' => 'TEST',
        'region_code' => 'TEST',
        'calendar_code' => 'A',
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('official_calendar_publication_versions')->insert([
        'id' => $versionId,
        'publication_id' => $publicationId,
        'version_number' => 1,
        'supersedes_version_id' => null,
        'official_version_label' => null,
        'authority_name_snapshot' => 'Autoridad de pruebas INT-01',
        'title' => 'Calendario oficial ficticio para validar restricciones',
        'promulgated_on' => null,
        'calendar_start_date' => '2026-01-10',
        'calendar_end_date' => '2026-01-20',
        'source_url' => 'https://example.invalid/int01-fixture',
        'source_reference' => 'Fixture INT-01; no corresponde a una publicación real',
        'document_sha256' => null,
        'verification_status' => 'draft',
        'validated_at' => null,
        'withdrawn_at' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('academic_periods')->insert([
        'id' => $periodId,
        'publication_version_id' => $versionId,
        'code' => 'P1',
        'name' => 'Periodo de prueba 1',
        'sequence_number' => 1,
        'start_date' => '2026-01-10',
        'end_date' => '2026-01-15',
        'declared_duration' => null,
        'duration_unit' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    int01ExpectRejected(
        'Solapamiento inclusivo de períodos rechazado',
        'ACA-F07',
        static function () use ($connection, $versionId, $now): void {
            $connection->table('academic_periods')->insert([
                'id' => int01Ulid(), 'publication_version_id' => $versionId, 'code' => 'OVERLAP',
                'name' => 'Solapamiento de prueba', 'sequence_number' => 2,
                'start_date' => '2026-01-15', 'end_date' => '2026-01-18',
                'declared_duration' => null, 'duration_unit' => null, 'created_at' => $now, 'updated_at' => $now,
            ]);
        },
        $failures,
    );
    int01ExpectRejected(
        'Período fuera de las fechas oficiales rechazado',
        'ACA-F07',
        static function () use ($connection, $versionId, $now): void {
            $connection->table('academic_periods')->insert([
                'id' => int01Ulid(), 'publication_version_id' => $versionId, 'code' => 'OUTSIDE',
                'name' => 'Periodo fuera de rango', 'sequence_number' => 3,
                'start_date' => '2026-01-21', 'end_date' => '2026-01-22',
                'declared_duration' => null, 'duration_unit' => null, 'created_at' => $now, 'updated_at' => $now,
            ]);
        },
        $failures,
    );

    try {
        $connection->table('academic_calendar_events')->insert([
            'id' => $vacationEventId,
            'publication_version_id' => $versionId,
            'academic_period_id' => null,
            'code' => 'TEACHER_VACATION_AFTER_RANGE',
            'event_type' => 'TEACHER_VACATION',
            'title' => 'Vacaciones docentes posteriores al rango institucional',
            'audience' => 'TEACHERS',
            'start_date' => '2026-01-21',
            'end_date' => '2026-01-22',
            'is_officially_required' => false,
            'activation_prerequisite' => false,
            'closure_prerequisite' => false,
            'source_reference' => 'Referencia oficial específica de la fixture INT-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        int01Check(true, 'Evento docente fuera del rango institucional permitido con referencia específica', $failures);
    } catch (Throwable $exception) {
        int01Check(false, 'Evento docente fuera del rango institucional permitido con referencia específica', $failures);
        echo '  Evidencia del error: '.preg_replace('/\s+/', ' ', $exception->getMessage())."\n";
    }

    try {
        $connection->table('academic_calendar_events')->insert([
            'id' => $examEventId,
            'publication_version_id' => $versionId,
            'academic_period_id' => $periodId,
            'code' => 'EXAM_IN_PERIOD',
            'event_type' => 'EXAM_INTERVAL',
            'title' => 'Intervalo de examen dentro del periodo',
            'audience' => 'STUDENTS',
            'start_date' => '2026-01-14',
            'end_date' => '2026-01-15',
            'is_officially_required' => true,
            'activation_prerequisite' => false,
            'closure_prerequisite' => false,
            'source_reference' => 'Referencia de examen de la fixture INT-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        int01Check(true, 'Intervalo de examen contenido en su período permitido', $failures);
    } catch (Throwable $exception) {
        int01Check(false, 'Intervalo de examen contenido en su período permitido', $failures);
        echo '  Evidencia del error: '.preg_replace('/\s+/', ' ', $exception->getMessage())."\n";
    }

    int01ExpectRejected(
        'Intervalo de examen fuera de su período rechazado',
        'ACA-F07',
        static function () use ($connection, $versionId, $periodId, $now): void {
            $connection->table('academic_calendar_events')->insert([
                'id' => int01Ulid(), 'publication_version_id' => $versionId, 'academic_period_id' => $periodId,
                'code' => 'EXAM_OUTSIDE_PERIOD', 'event_type' => 'EXAM_INTERVAL', 'title' => 'Examen fuera de periodo',
                'audience' => 'STUDENTS', 'start_date' => '2026-01-16', 'end_date' => '2026-01-17',
                'is_officially_required' => true, 'activation_prerequisite' => false, 'closure_prerequisite' => false,
                'source_reference' => 'Referencia de examen de la fixture INT-01', 'created_at' => $now, 'updated_at' => $now,
            ]);
        },
        $failures,
    );

    $connection->table('official_calendar_publication_versions')->where('id', $versionId)->update([
        'verification_status' => 'validated',
        'validated_at' => $now,
        'updated_at' => $now,
    ]);
    int01Check(true, 'Transición inicial draft → validated permitida', $failures);

    int01ExpectRejected(
        'Edición de períodos de versión validada rechazada',
        'ACA-INT-01',
        static fn () => $connection->table('academic_periods')->where('id', $periodId)->update(['name' => 'No modificar']),
        $failures,
    );
    int01ExpectRejected(
        'Edición de eventos de versión validada rechazada',
        'ACA-INT-01',
        static fn () => $connection->table('academic_calendar_events')->where('id', $vacationEventId)->update(['title' => 'No modificar']),
        $failures,
    );
    int01ExpectRejected(
        'Cambio de identidad de autoridad referenciada rechazado',
        'ACA-INT-01',
        static fn () => $connection->table('academic_regulatory_authorities')->where('id', $authorityId)->update(['authority_code' => 'CHANGED']),
        $failures,
    );
    int01ExpectRejected(
        'Cambio de compatibilidad de publicación con versión validada rechazado',
        'ACA-INT-01',
        static fn () => $connection->table('official_calendar_publications')->where('id', $publicationId)->update(['region_code' => 'CHANGED']),
        $failures,
    );

    $connection->table('official_calendar_publication_versions')->where('id', $versionId)->update([
        'verification_status' => 'available',
        'updated_at' => $now,
    ]);
    int01Check(true, 'Transición validated → available permitida con identidad y A/B resueltos', $failures);

    $connection->table('academic_years')->insert([
        'id' => $yearId,
        'institution_id' => $institutionId,
        'publication_version_id' => $versionId,
        'cycle_code' => '2026',
        'calendar_code' => 'A',
        'status' => 'draft',
        'activated_at' => null,
        'closed_at' => null,
        'cancelled_at' => null,
        'cancellation_reason' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    $connection->table('academic_year_event_completions')->insert([
        'id' => int01Ulid(),
        'publication_version_id' => $versionId,
        'academic_year_id' => $yearId,
        'event_id' => $vacationEventId,
        'completed_by' => 'INT01 validation actor',
        'completed_at' => $now,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    int01Check(true, 'Completitud aceptada cuando año y evento comparten versión oficial', $failures);

    $connection->table('official_calendar_publication_versions')->where('id', $versionId)->update([
        'verification_status' => 'withdrawn',
        'withdrawn_at' => $now,
        'updated_at' => $now,
    ]);
    int01Check(true, 'Retiro de versión disponible permitido con sello temporal', $failures);
    int01ExpectRejected(
        'Reactivación de versión retirada rechazada',
        'ACA-INT-01',
        static fn () => $connection->table('official_calendar_publication_versions')->where('id', $versionId)->update([
            'verification_status' => 'available',
            'withdrawn_at' => null,
            'updated_at' => $now,
        ]),
        $failures,
    );
} catch (Throwable $exception) {
    $failures++;
    echo 'FAIL: excepción no prevista en pruebas transaccionales: '.preg_replace('/\s+/', ' ', $exception->getMessage())."\n";
} finally {
    if ($connection->transactionLevel() > 0) {
        $connection->rollBack();
    }
}

$leftover = (int) DB::connection('mysql')->table('academic_regulatory_authorities')->count();
if ($leftover !== 0) {
    $failures++;
    echo "FAIL: quedaron {$leftover} filas de prueba; inspeccione la transacción y no limpie manualmente sin revisar.\n";
} else {
    echo "PASS: transacción de pruebas revertida; no quedaron filas de fixture académica.\n";
}

if ($failures > 0) {
    int01Fail("validación de esquema/constraints terminada con {$failures} fallo(s). Preserve la base y salida para revisión; no ejecute rollback o limpieza automática.");
}

echo "\nINT-01 VALIDACIÓN DE ESQUEMA Y RESTRICCIONES: OK\n";
echo "No se modificó la base real cenicom ni cenicom_aca_year02_isolated_test.\n";
